<?php

namespace App\Filament\TeamPanel\Pages;

use App\Models\Lead;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\QuotePhase;
use App\Models\Service;
use App\Services\GeminiService;
use App\Services\PricingCalculatorService;
use BackedEnum;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use UnitEnum;

class PreQuoteAI extends Page
{
    use InteractsWithForms;

    public function getView(): string
    {
        return 'filament.team-panel.pages.pre-quote-ai';
    }

    protected static ?string $navigationLabel = 'Pré-Orçamento IA';
    protected static UnitEnum|string|null $navigationGroup = 'Projetos';
    protected static ?int $navigationSort = 4;
    protected static BackedEnum|string|null $navigationIcon = \Filament\Support\Icons\Heroicon::OutlinedSparkles;

    public ?int $lead_id = null;
    public string $project_description = '';
    public ?array $aiResult = null;
    public bool $isLoading = false;
    public ?int $generatedQuoteId = null;

    public function analyze(): void
    {
        $this->validate([
            'project_description' => 'required|string|min:30',
        ]);

        $this->isLoading = true;

        $services = Service::where('is_active', true)->get()
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])
            ->toArray();

        $prompt = $this->buildPrompt($services);
        $raw = app(GeminiService::class)->generateJson($prompt);
        $this->aiResult = $this->enrichWithPricing($raw);
        $this->isLoading = false;
    }

    private function buildPrompt(array $services): string
    {
        $list = collect($services)->map(fn ($s) => "ID {$s['id']}: {$s['name']}")->join("\n");

        return <<<PROMPT
Você é especialista em análise de projetos de software para agência digital.
Analise a descrição e gere um plano de fases com estimativas de horas.

SERVIÇOS DISPONÍVEIS (use SOMENTE estes IDs):
{$list}

DESCRIÇÃO DO PROJETO:
{$this->project_description}

Retorne JSON no formato:
{
    "project_summary": "Resumo em 2-3 frases",
    "phases": [
        {
            "name": "Nome da fase",
            "description": "O que será feito",
            "estimated_days": 10,
            "items": [
                { "service_id": 1, "description": "Detalhe da atividade", "hours": 20 }
            ]
        }
    ]
}
PROMPT;
    }

    private function enrichWithPricing(array $result): array
    {
        $calculator = app(PricingCalculatorService::class);

        foreach ($result['phases'] ?? [] as $pi => $phase) {
            $phaseTotal = 0;

            foreach ($phase['items'] ?? [] as $ii => $item) {
                $price = $calculator->getPriceForHours($item['service_id'] ?? 0, $item['hours'] ?? 0);
                $result['phases'][$pi]['items'][$ii]['unit_price'] = $price;
                $result['phases'][$pi]['items'][$ii]['subtotal'] = round($price * ($item['hours'] ?? 0), 2);
                $phaseTotal += $result['phases'][$pi]['items'][$ii]['subtotal'];
            }

            $result['phases'][$pi]['subtotal'] = $phaseTotal;
        }

        $result['total'] = collect($result['phases'] ?? [])->sum('subtotal');

        return $result;
    }

    public function generateQuote(): void
    {
        if (! $this->aiResult) {
            return;
        }

        $leadName = Lead::find($this->lead_id)?->name;
        $titleBase = $leadName ? "Orcamento IA - {$leadName}" : 'Orcamento IA';
        $title = Str::limit($titleBase, 255, '');
        $description = $this->aiResult['project_summary'] ?? null;
        
        $quote = Quote::create([
            'lead_id'    => $this->lead_id,
            'title'      => $title,
            'description'=> $description,
            'number'     => 'AI-' . now()->format('YmdHis'),
            'status'     => 'draft',
            'created_by' => Auth::id(),
        ]);

        foreach ($this->aiResult['phases'] ?? [] as $order => $phaseData) {
            $phase = QuotePhase::create([
                'quote_id'       => $quote->id,
                'name'           => $phaseData['name'],
                'description'    => $phaseData['description'] ?? null,
                'estimated_days' => $phaseData['estimated_days'] ?? null,
                'sort_order'     => $order,
            ]);

            foreach ($phaseData['items'] ?? [] as $itemOrder => $itemData) {
                $itemDescription = Str::limit((string) ($itemData['description'] ?? ''), 255, '');

                QuoteItem::create([
                    'quote_id'       => $quote->id,
                    'quote_phase_id' => $phase->id,
                    'service_id'     => $itemData['service_id'],
                    'description'    => $itemDescription,
                    'hours'          => $itemData['hours'],
                    'unit_price'     => $itemData['unit_price'],
                    'quantity'       => $itemData['hours'],
                    'sort_order'     => $itemOrder,
                ]);
            }
        }

        $quote->recalculateTotals();
        $this->generatedQuoteId = $quote->id;

        \Filament\Notifications\Notification::make()
            ->title('Orçamento gerado com sucesso!')
            ->success()
            ->send();
    }
}
