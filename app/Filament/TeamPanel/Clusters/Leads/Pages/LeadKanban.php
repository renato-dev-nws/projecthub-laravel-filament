<?php

namespace App\Filament\TeamPanel\Clusters\Leads\Pages;

use App\Filament\TeamPanel\Clusters\Leads\LeadsCluster;
use App\Models\Lead;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class LeadKanban extends Page
{
    protected static ?string $cluster = LeadsCluster::class;

    protected static ?string $navigationLabel = 'Kanban';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Pipeline de Leads';

    public array $boards = [];

    public static array $statuses = [
        'new'           => 'Novo',
        'contacted'     => 'Contactado',
        'qualified'     => 'Qualificado',
        'proposal_sent' => 'Proposta Enviada',
        'negotiation'   => 'Negociação',
        'converted'     => 'Convertido',
        'lost'          => 'Perdido',
    ];

    public function getView(): string
    {
        return 'filament.team-panel.pages.lead-kanban';
    }

    public function mount(): void
    {
        $this->loadBoards();
    }

    public function loadBoards(): void
    {
        $leads = Lead::query()
            ->with('assignedTo')
            ->withoutTrashed()
            ->get();

        $this->boards = collect(self::$statuses)
            ->mapWithKeys(fn ($label, $status) => [
                $status => [
                    'label' => $label,
                    'leads' => $leads->where('status', $status)->values()->toArray(),
                ],
            ])
            ->toArray();
    }

    #[On('lead-moved')]
    public function moveLead(int $leadId, string $newStatus): void
    {
        $lead = Lead::findOrFail($leadId);
        $this->authorize('update', $lead);
        $lead->update(['status' => $newStatus]);
        $this->loadBoards();
    }
}
