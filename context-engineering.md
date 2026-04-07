# Context Engineering — ProjectHub
## Especificações Técnicas para Desenvolvimento Assistido por IA

> **Stack:** Laravel 12 · Filament V4 · Livewire V3 · PostgreSQL · Redis · Laravel Sail
> **Documento:** Especificações de features para desenvolvimento iterativo por agente de IA (Claude)
> **Data:** Abril de 2026

---

## ESTADO ATUAL DO PROJETO

### O que já existe e está funcionando

| Módulo | Status | Observações |
|---|---|---|
| Autenticação Team Panel (`/admin`) | ✅ Completo | Guard `web`, roles via Spatie Permission |
| Autenticação Client Panel (`/portal`) | ✅ Completo | Guard `client_portal`, model `ClientPortalUser` |
| Model + Migration: `Client` | ✅ Completo | PJ/PF, softDeletes, MediaLibrary, activitylog |
| Model + Migration: `Lead` | ✅ Completo | softDeletes, activitylog, sem campos `website`/`referral_url` |
| Model + Migration: `Service` | ✅ Completo | Sem categoria relacional (campo `category` é string simples) |
| Model + Migration: `Quote` | ✅ Completo | `client_id` nullable, `lead_id` nullable |
| Model + Migration: `QuoteItem` | ✅ Completo | Cálculo automático de `subtotal` via booted() |
| Model + Migration: `Project` | ✅ Completo | sluggable, MediaLibrary, activitylog |
| Model + Migration: `ProjectPhase` | ✅ Completo | |
| Model + Migration: `ProjectTask` | ✅ Completo | Observer recalcula progresso do projeto |
| Model + Migration: `RoadmapItem` | ✅ Completo | `phase_id` FK nullable para `project_phases` |
| Model + Migration: `ProjectDocument` | ✅ Completo | |
| Model + Migration: `ProjectComment` | ✅ Completo | |
| Model + Migration: `TimeLog` | ✅ Completo | |
| Model + Migration: `ProjectMember` | ✅ Completo | pivot com role e joined_at |
| Model + Migration: `ClientContact` | ✅ Completo | |
| Model + Migration: `ClientPortalUser` | ✅ Completo | |
| `LeadResource` (CRUD) | ✅ Completo | navigationGroup='CRM', sem Kanban |
| `ClientResource` (CRUD) | ✅ Completo | sem view detalhada com relações |
| `QuoteResource` (CRUD) | ✅ Completo | `client_id` ainda required no form atual |
| `ProjectResource` (CRUD) | ✅ Completo | com 6 RelationManagers |
| `ServiceResource` (CRUD) | ✅ Completo | |
| `UserResource` (CRUD) | ✅ Completo | |
| `TeamPanelProvider` | ✅ Completo | navigationGroups: CRM, Projetos, Financeiro, Configurações |
| `ClientPanelProvider` | ✅ Completo | |
| `ProjectTaskObserver` | ✅ Completo | recalcula `progress_percent` em create/update/delete |
| Roles Seeder | ✅ Completo | Super Admin, Admin, Project Manager, Developer, Designer, Account Manager |

### Estrutura de Diretórios dos Resources (padrão adotado)

```
app/Filament/TeamPanel/Resources/{ModelName}/
├── {ModelName}Resource.php          ← Resource principal (roteamento e metadata)
├── Pages/
│   ├── List{ModelName}s.php
│   ├── Create{ModelName}.php
│   └── Edit{ModelName}.php
├── Schemas/
│   └── {ModelName}Form.php          ← Lógica de formulário isolada
└── Tables/
    └── {ModelName}sTable.php        ← Lógica de tabela isolada
```

> **REGRA INVIOLÁVEL:** Nunca colocar lógica de `form()` ou `table()` diretamente no Resource — sempre delegar para as classes dedicadas em `Schemas/` e `Tables/`.

---

## CONVENÇÕES DO PROJETO

### Filament V4 — Namespaces Críticos

```php
// Schema (formulário) — V4 usa Filament\Schemas\Schema
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

// Componentes de formulário continuam em Filament\Forms\Components
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Get;
use Filament\Forms\Set;

// Tables
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

// Notificações toast
use Filament\Notifications\Notification;

// Icons — V4 usa enum Heroicon, não strings
use Filament\Support\Icons\Heroicon;
// Uso: protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;
```

### Layout de Formulários — PADRÃO OBRIGATÓRIO

> **PROBLEMA ATUAL:** O layout com `->columns(2)` na raiz do schema cria espaços em branco porque sections de tamanhos diferentes ficam lado a lado com lacunas verticais.
>
> **SOLUÇÃO:** Usar `Grid::make(2)` e distribuir as sections manualmente com `->columnSpan(1)`. Sections que precisam de largura total usam `->columnSpanFull()`.

```php
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

return $schema->components([
    Grid::make(2)->schema([
        // Coluna 1 — cards ímpares na ordem
        Section::make('Dados Principais')->schema([...])->columnSpan(1),
        // Coluna 2 — cards pares na ordem
        Section::make('Configurações')->schema([...])->columnSpan(1),
        // Coluna 1
        Section::make('Datas')->schema([...])->columnSpan(1),
        // Coluna 2
        Section::make('Financeiro')->schema([...])->columnSpan(1),
        // Largura total — sempre no final ou quando necessário
        Section::make('Descrição')->schema([...])->columnSpanFull(),
    ]),
]);
```

### Brand e Logo

- **Tema light:** `public/img/icon.svg`
- **Tema dark:** `public/img/icon-bg-dark.svg`
- Substituir `->brandName('ProjectHub — Admin')` por `->brandLogo(fn () => view('filament.brand'))`

### Validação em PT-BR

> **BUG ATIVO:** Mensagens de erro exibem `"validation.required"` em vez do texto traduzido português.
> **Fix:** Publicar arquivos de lang e configurar locale.

---

## FASE 1 — CORREÇÕES E AJUSTES DE BASE

> Aplicar antes de qualquer nova feature. São pré-requisitos para a qualidade da UI.

### 1.1 — Layout de Formulários (Grid 2 colunas)

**Arquivos a refatorar:**
- `app/Filament/TeamPanel/Resources/Leads/Schemas/LeadForm.php`
- `app/Filament/TeamPanel/Resources/Clients/Schemas/ClientForm.php`
- `app/Filament/TeamPanel/Resources/Quotes/Schemas/QuoteForm.php`
- `app/Filament/TeamPanel/Resources/Projects/Schemas/ProjectForm.php`
- `app/Filament/TeamPanel/Resources/Services/Schemas/ServiceForm.php`
- `app/Filament/TeamPanel/Resources/Users/Schemas/UserForm.php`

**Transformação padrão:**
```php
// ANTES — cria gaps verticais entre sections de alturas diferentes
return $schema->components([
    Section::make('A')->schema([...])->columns(2),
    Section::make('B')->schema([...]),
]);

// DEPOIS — grid externo garante layout de 2 colunas fluido
use Filament\Schemas\Components\Grid;

return $schema->components([
    Grid::make(2)->schema([
        Section::make('A')->schema([...])->columnSpan(1),
        Section::make('B')->schema([...])->columnSpan(1),
        Section::make('C')->schema([...])->columnSpan(1),
        Section::make('D')->schema([...])->columnSpan(1),
        Section::make('Textarea/Completo')->schema([...])->columnSpanFull(),
    ]),
]);
```

### 1.2 — Logo SVG no Brand do TeamPanel

**Arquivo:** `app/Providers/Filament/TeamPanelProvider.php`

```php
// Substituir:
->brandName('ProjectHub — Admin')
->favicon(asset('favicon.ico'))

// Por:
->brandLogo(fn () => view('filament.brand'))
->brandLogoHeight('2rem')
->favicon(asset('img/icon.svg'))
```

**Criar:** `resources/views/filament/brand.blade.php`
```blade
<picture>
    <source media="(prefers-color-scheme: dark)"
            srcset="{{ asset('img/icon-bg-dark.svg') }}">
    <img src="{{ asset('img/icon.svg') }}"
         alt="ProjectHub"
         style="height: 2rem; width: auto; display: block;">
</picture>
```

### 1.3 — Mensagens de Validação PT-BR

```bash
php artisan lang:publish
```

Em `config/app.php`:
```php
'locale'          => 'pt_BR',
'fallback_locale' => 'en',
'timezone'        => 'America/Sao_Paulo',
```

Criar/atualizar `lang/pt_BR/validation.php` com todas as mensagens traduzidas.

---

## FASE 2 — MÓDULO LEADS (Melhorias)

### 2.1 — Cluster de Leads: Lista + Kanban

**Contexto:** O `LeadResource` atual tem apenas List/Create/Edit. Precisamos de um Kanban como página extra no mesmo resource, usando a navegação de cluster do Filament V4.

**Novo arquivo:** `app/Filament/TeamPanel/Resources/Leads/Pages/LeadKanban.php`

```php
namespace App\Filament\TeamPanel\Resources\Leads\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\TeamPanel\Resources\Leads\LeadResource;
use App\Models\Lead;
use Livewire\Attributes\On;

class LeadKanban extends Page
{
    protected static string $resource = LeadResource::class;
    protected static string $view = 'filament.team-panel.pages.lead-kanban';
    protected static ?string $navigationLabel = 'Kanban';
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
                    'leads' => $leads->where('status', $status)->values(),
                ],
            ])->toArray();
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
```

**Registrar no LeadResource:**
```php
public static function getPages(): array
{
    return [
        'index'  => ListLeads::route('/'),
        'create' => CreateLead::route('/create'),
        'edit'   => EditLead::route('/{record}/edit'),
        'kanban' => LeadKanban::route('/kanban'),
    ];
}
```

**Navegação do ListLeads:** label = 'Lista' | **LeadKanban:** label = 'Kanban'

**View Blade (Livewire + Alpine.js):** `resources/views/filament/team-panel/pages/lead-kanban.blade.php`
- Columns horizontais por status com scroll horizontal
- Cards arrastáveis via Alpine.js drag-and-drop
- Ao soltar: `$wire.dispatch('lead-moved', { leadId, newStatus })`
- Badge de prioridade colorido (low=gray, medium=yellow, high=red)

### 2.2 — Campos de URL no Lead

**Migration nova:**
```php
Schema::table('leads', function (Blueprint $table) {
    $table->string('website')->nullable()->after('company');
    $table->string('referral_url')->nullable()->after('website');
});
```

**Model Lead — adicionar ao `$fillable`:** `'website'`, `'referral_url'`

**LeadForm.php — adicionar na Section 'Dados do Lead':**
```php
TextInput::make('website')
    ->label('Site do Lead')
    ->url()
    ->prefixIcon(Heroicon::OutlinedGlobeAlt)
    ->maxLength(255),

TextInput::make('referral_url')
    ->label('URL de Referência')
    ->url()
    ->prefixIcon(Heroicon::OutlinedLink)
    ->maxLength(255),
```

---

## FASE 3 — MÓDULO ORÇAMENTOS (Reestruturação)

### 3.1 — Orçamento sem Cliente Obrigatório + Cadastro Rápido de Lead

**Regra de negócio:** Um orçamento pode ser vinculado a um `Client` OU a um `Lead` — pelo menos um dos dois deve estar preenchido.

**QuoteForm.php — campo cliente:**
```php
Select::make('client_id')
    ->label('Cliente')
    ->relationship('client', 'company_name')
    ->searchable()
    ->preload()
    ->nullable()  // ← remover required()
    ->live(),

Select::make('lead_id')
    ->label('Lead')
    ->relationship('lead', 'name')
    ->searchable()
    ->preload()
    ->nullable()
    ->createOptionForm([
        TextInput::make('name')->label('Nome')->required(),
        TextInput::make('email')->label('E-mail')->email(),
        Select::make('source')
            ->label('Origem')
            ->options(['website' => 'Website', 'referral' => 'Indicação', 'other' => 'Outro'])
            ->default('other'),
    ])
    ->createOptionUsing(function (array $data): int {
        return \App\Models\Lead::create(array_merge($data, ['status' => 'new']))->id;
    }),
```

**Validação customizada (no Resource):**
```php
// Adicionar ao QuoteResource::form() ou via afterValidation hook:
// Verificar que pelo menos client_id ou lead_id está preenchido.
```

### 3.2 — Fases do Projeto no Orçamento

**Novas tabelas a criar via migrations:**

```php
// Migration: create_quote_phases_table
Schema::create('quote_phases', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->integer('estimated_days')->nullable();
    $table->date('deadline_date')->nullable();
    $table->integer('sort_order')->default(0);
    $table->decimal('subtotal', 15, 2)->default(0);
    $table->timestamps();
});

// Migration: add_quote_phase_id_and_hours_to_quote_items_table
Schema::table('quote_items', function (Blueprint $table) {
    $table->foreignId('quote_phase_id')
        ->nullable()
        ->after('quote_id')
        ->constrained('quote_phases')
        ->nullOnDelete();
    $table->decimal('hours', 10, 2)->default(0)->after('quantity');
});
```

**Novo Model:** `app/Models/QuotePhase.php`
```php
class QuotePhase extends Model
{
    protected $fillable = ['quote_id', 'name', 'description',
                           'estimated_days', 'deadline_date', 'sort_order', 'subtotal'];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }
}
```

**Quote Model — adicionar:**
```php
public function phases(): HasMany
{
    return $this->hasMany(QuotePhase::class)->orderBy('sort_order');
}
```

**QuoteForm.php — Repeater aninhado de fases:**
```php
Section::make('Fases do Projeto')
    ->schema([
        Repeater::make('phases')
            ->label('Fases')
            ->relationship('phases')
            ->schema([
                TextInput::make('name')->label('Nome da Fase')->required()->columnSpan(2),
                DatePicker::make('deadline_date')->label('Prazo da Fase')->columnSpan(1),
                TextInput::make('estimated_days')->label('Dias Estimados')->numeric()->suffix('dias')->columnSpan(1),
                Textarea::make('description')->label('Descrição da Fase')->rows(2)->columnSpanFull(),

                Repeater::make('items')
                    ->label('Itens da Fase')
                    ->relationship('items')
                    ->schema([
                        Select::make('service_id')
                            ->label('Serviço')
                            ->relationship('service', 'name')
                            ->searchable()->preload()->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                // Preencher unit_price com default_price do serviço
                                if ($state) {
                                    $service = \App\Models\Service::find($state);
                                    $set('unit_price', $service?->default_price ?? 0);
                                }
                            }),
                        TextInput::make('description')->label('Descrição')->required(),
                        TextInput::make('hours')->label('Horas')->numeric()->suffix('h'),
                        TextInput::make('unit_price')->label('R$/hora')->numeric()->prefix('R$'),
                    ])
                    ->columns(4)
                    ->columnSpanFull(),
            ])
            ->columns(4)
            ->columnSpanFull(),
    ]),
```

### 3.3 — Tabela de Descontos Progressivos por Serviço

**Nova tabela:**
```php
Schema::create('service_pricing_tiers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('service_id')->constrained()->cascadeOnDelete();
    $table->decimal('min_hours', 10, 2)->default(0);
    $table->decimal('max_hours', 10, 2)->nullable(); // NULL = sem limite superior
    $table->decimal('price_per_hour', 15, 2)->default(0);
    $table->string('label', 100)->nullable(); // ex: "Até 10h", "11h a 50h"
    $table->integer('sort_order')->default(0);
    $table->timestamps();

    $table->index('service_id');
});
```

**Novo Model:** `app/Models/ServicePricingTier.php`
```php
class ServicePricingTier extends Model
{
    protected $fillable = ['service_id', 'min_hours', 'max_hours',
                           'price_per_hour', 'label', 'sort_order'];
    protected function casts(): array
    {
        return [
            'min_hours'      => 'decimal:2',
            'max_hours'      => 'decimal:2',
            'price_per_hour' => 'decimal:2',
        ];
    }
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

**Service Model — adicionar:**
```php
public function pricingTiers(): HasMany
{
    return $this->hasMany(ServicePricingTier::class)->orderBy('min_hours');
}
```

**Novo Service:** `app/Services/PricingCalculatorService.php`
```php
namespace App\Services;

use App\Models\ServicePricingTier;
use App\Models\Service;

class PricingCalculatorService
{
    public function getPriceForHours(int $serviceId, float $hours): float
    {
        $tier = ServicePricingTier::where('service_id', $serviceId)
            ->where('min_hours', '<=', $hours)
            ->where(function ($q) use ($hours) {
                $q->whereNull('max_hours')
                  ->orWhere('max_hours', '>=', $hours);
            })
            ->orderBy('min_hours', 'desc')
            ->first();

        if ($tier) {
            return (float) $tier->price_per_hour;
        }

        return (float) (Service::find($serviceId)?->default_price ?? 0);
    }
}
```

**ServiceResource — adicionar RelationManager:**
```
app/Filament/TeamPanel/Resources/Services/RelationManagers/PricingTiersRelationManager.php
```

### 3.4 — Export PDF do Orçamento

**Pacote:** `barryvdh/laravel-dompdf` (já instalado no `composer.json`)

**Controller:** `app/Http/Controllers/QuotePdfController.php`
```php
namespace App\Http\Controllers;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotePdfController extends Controller
{
    public function __invoke(Quote $quote)
    {
        $this->authorize('view', $quote);

        $pdf = Pdf::loadView('pdf.quote', [
            'quote' => $quote->load([
                'client', 'lead', 'phases.items.service', 'creator'
            ]),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("orcamento-{$quote->number}.pdf");
    }
}
```

**Rota:** `routes/web.php`
```php
use App\Http\Controllers\QuotePdfController;

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/quotes/{quote}/pdf', QuotePdfController::class)
         ->name('quotes.pdf');
});
```

**View:** `resources/views/pdf/quote.blade.php`
- Cabeçalho: logomarca `public/img/icon.svg` + dados da empresa + número/data
- Dados do cliente ou lead
- Tabela de fases com sub-tabela de itens (horas × R$/hora = subtotal)
- Rodapé com totais, desconto e total final
- Estilos inline (dompdf não suporta classes tailwind)

**Action no QuotesTable.php:**
```php
Tables\Actions\Action::make('download_pdf')
    ->label('PDF')
    ->icon(Heroicon::OutlinedDocumentArrowDown)
    ->url(fn (Quote $record) => route('quotes.pdf', $record))
    ->openUrlInNewTab(),
```

---

## FASE 4 — MÓDULO PROJETOS (Melhorias)

### 4.1 — Tarefas Vinculadas ao Item do Roadmap

**Migration:**
```php
Schema::table('project_tasks', function (Blueprint $table) {
    $table->foreignId('roadmap_item_id')
        ->nullable()
        ->after('phase_id')  // supondo que phase_id já existe
        ->constrained('roadmap_items')
        ->nullOnDelete();
});
```

**Model `ProjectTask` — adicionar `'roadmap_item_id'` ao `$fillable`**

**TasksRelationManager — adicionar campo:**
```php
Select::make('roadmap_item_id')
    ->label('Item do Roadmap')
    ->options(function (Get $get, $livewire) {
        $projectId = $livewire->ownerRecord?->id;
        if (!$projectId) return [];
        return \App\Models\RoadmapItem::where('project_id', $projectId)
            ->pluck('title', 'id');
    })
    ->searchable()
    ->nullable(),
```

### 4.2 — Cadastro Rápido de Cliente: PF / PJ / Puxar Lead

**ProjectForm.php — campo `client_id` expandido:**
```php
Select::make('client_id')
    ->label('Cliente')
    ->relationship('client', 'company_name')
    ->searchable()
    ->preload()
    ->required()
    ->live()
    ->createOptionForm([
        Select::make('type')
            ->label('Tipo de Cliente')
            ->options([
                'pessoa_juridica' => 'Pessoa Jurídica (PJ)',
                'pessoa_fisica'   => 'Pessoa Física (PF)',
            ])
            ->default('pessoa_juridica')
            ->live()
            ->required(),

        // Puxar de Lead existente
        Select::make('_from_lead_id')
            ->label('Puxar dados de um Lead')
            ->options(
                \App\Models\Lead::whereNull('converted_client_id')
                    ->pluck('name', 'id')
            )
            ->searchable()
            ->live()
            ->afterStateUpdated(function ($state, Set $set) {
                if (!$state) return;
                $lead = \App\Models\Lead::find($state);
                if ($lead) {
                    $set('company_name', $lead->company ?? $lead->name);
                    $set('email', $lead->email);
                    $set('phone', $lead->phone);
                    $set('type', 'pessoa_juridica');
                }
            }),

        TextInput::make('company_name')
            ->label(fn (Get $get) => $get('type') === 'pessoa_fisica' ? 'Nome Completo' : 'Razão Social')
            ->required(),

        TextInput::make('cnpj')
            ->label('CNPJ')
            ->visible(fn (Get $get) => $get('type') === 'pessoa_juridica'),

        TextInput::make('cpf')
            ->label('CPF')
            ->visible(fn (Get $get) => $get('type') === 'pessoa_fisica'),

        TextInput::make('email')->label('E-mail')->email(),
        TextInput::make('phone')->label('Telefone')->tel(),
    ]),
```

### 4.3 — Campo `quote_id` Dependente do Cliente

**Comportamento:** campo `quote_id` só exibe opções após `client_id` ser selecionado. Exibe apenas orçamentos `approved` ou `sent` do cliente.

**ProjectForm.php — campo `quote_id`:**
```php
// client_id vem antes e é ->live()
Select::make('quote_id')
    ->label('Orçamento')
    ->options(function (Get $get) {
        $clientId = $get('client_id');
        if (!$clientId) return [];
        return \App\Models\Quote::where('client_id', $clientId)
            ->whereIn('status', ['approved', 'sent'])
            ->orderByDesc('created_at')
            ->pluck('title', 'id');
    })
    ->disabled(fn (Get $get) => !$get('client_id'))
    ->helperText(fn (Get $get) => !$get('client_id')
        ? 'Selecione um cliente primeiro para ver os orçamentos disponíveis'
        : null)
    ->searchable()
    ->nullable(),
```

**Ordem dos campos no form:** `client_id` → `quote_id` → `project_manager_id` → demais.

---

## FASE 5 — MÓDULO CLIENTES (View de Detalhes)

### 5.1 — Página de Detalhes do Cliente com Relações

**Novo arquivo:** `app/Filament/TeamPanel/Resources/Clients/Pages/ViewClient.php`
```php
namespace App\Filament\TeamPanel\Resources\Clients\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\TeamPanel\Resources\Clients\ClientResource;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;
}
```

**RelationManagers a criar para Client:**
```
app/Filament/TeamPanel/Resources/Clients/RelationManagers/
├── ContactsRelationManager.php
├── ProjectsRelationManager.php
├── QuotesRelationManager.php
└── PortalUsersRelationManager.php
```

**ClientResource — atualizar:**
```php
public static function getRelations(): array
{
    return [
        RelationManagers\ContactsRelationManager::class,
        RelationManagers\ProjectsRelationManager::class,
        RelationManagers\QuotesRelationManager::class,
        RelationManagers\PortalUsersRelationManager::class,
    ];
}

public static function getPages(): array
{
    return [
        'index'  => ListClients::route('/'),
        'create' => CreateClient::route('/create'),
        'edit'   => EditClient::route('/{record}/edit'),
        'view'   => ViewClient::route('/{record}'),
    ];
}
```

### 5.2 — Campo `website` no ClientForm

A coluna `website` já existe na migration. Apenas adicionar ao `ClientForm.php`:
```php
TextInput::make('website')
    ->label('Site')
    ->url()
    ->maxLength(255)
    ->placeholder('https://'),
```

---

## FASE 6 — CONFIGURAÇÕES (Origens de Leads e Categorias de Serviços)

### 6.1 — LeadSource: Origens de Leads Configuráveis

**Migration nova:**
```php
Schema::create('lead_sources', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug', 100)->unique();
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

Schema::table('leads', function (Blueprint $table) {
    $table->foreignId('lead_source_id')
        ->nullable()
        ->after('source')
        ->constrained('lead_sources')
        ->nullOnDelete();
});
```

**Model:** `app/Models/LeadSource.php`
```php
class LeadSource extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'sort_order'];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
```

**Lead Model — adicionar:**
- `'lead_source_id'` ao `$fillable`
- `public function leadSource(): BelongsTo`

**LeadForm.php — substituir Select de `source` hardcoded:**
```php
Select::make('lead_source_id')
    ->label('Origem')
    ->relationship('leadSource', 'name')
    ->searchable()
    ->preload()
    ->createOptionForm([
        TextInput::make('name')->label('Nome')->required(),
        TextInput::make('slug')->label('Slug')->required(),
    ]),
```

**Resource:**
```
app/Filament/TeamPanel/Resources/Settings/LeadSourceResource.php
```
- `$navigationGroup = 'Configurações'`
- `$navigationSort = 1`
- `$navigationLabel = 'Origens de Leads'`

### 6.2 — ServiceCategory: Categorias de Serviços Configuráveis

**Migration nova:**
```php
Schema::create('service_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('color', 7)->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

Schema::table('services', function (Blueprint $table) {
    $table->foreignId('service_category_id')
        ->nullable()
        ->after('category')
        ->constrained('service_categories')
        ->nullOnDelete();
});
```

**Model:** `app/Models/ServiceCategory.php`
```php
class ServiceCategory extends Model
{
    protected $fillable = ['name', 'description', 'color', 'is_active', 'sort_order'];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
```

**ServiceForm.php — substituir campo `category` string:**
```php
Select::make('service_category_id')
    ->label('Categoria')
    ->relationship('category', 'name')
    ->searchable()
    ->preload()
    ->createOptionForm([
        TextInput::make('name')->label('Nome')->required(),
        ColorPicker::make('color')->label('Cor'),
    ]),
```

**Resource:**
```
app/Filament/TeamPanel/Resources/Settings/ServiceCategoryResource.php
```
- `$navigationGroup = 'Configurações'`
- `$navigationSort = 2`
- `$navigationLabel = 'Categorias de Serviços'`

---

## FASE 7 — INTEGRAÇÃO GEMINI AI

> **Pacote:** HTTP nativo Laravel (`Illuminate\Support\Facades\Http`)
> **API Key:** `GEMINI_API_KEY` no `.env`
> **Modelo recomendado:** `gemini-1.5-flash` (rápido) ou `gemini-1.5-pro` (mais preciso)
> **Formato de resposta:** `application/json` via `generationConfig.responseMimeType`

### 7.1 — GeminiService Base

**Arquivo:** `app/Services/GeminiService.php`
```php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model  = config('services.gemini.model', 'gemini-1.5-flash');
    }

    public function generateJson(string $prompt): array
    {
        $response = Http::timeout(60)
            ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $prompt]]],
                ],
                'generationConfig' => [
                    'temperature'      => 0.3,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Erro ao chamar API Gemini: ' . $response->body());
        }

        $text = $response->json('candidates.0.content.parts.0.text', '{}');

        return json_decode($text, true) ?? [];
    }
}
```

**Config `config/services.php` — adicionar:**
```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
    'model'   => env('GEMINI_MODEL', 'gemini-1.5-flash'),
],
```

**.env — adicionar:**
```env
GEMINI_API_KEY=sua-api-key-aqui
GEMINI_MODEL=gemini-1.5-flash
```

### 7.2 — Pré-Orçamento IA

**Fluxo de dados:**
```
Página PreQuoteAI
   ↓ lead_id + project_description
GeminiService::generateJson(prompt com lista de serviços)
   ↓ JSON: { project_summary, phases: [{ name, items: [{ service_id, hours }] }] }
PricingCalculatorService::getPriceForHours(service_id, hours)
   ↓ Enriquece o JSON com unit_price e subtotal por item/fase
Exibir resultado editável na UI
   ↓ Aprovação do usuário
Gerar Quote + QuotePhases + QuoteItems automaticamente
```

**Arquivo:** `app/Filament/TeamPanel/Pages/PreQuoteAI.php`

```php
namespace App\Filament\TeamPanel\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Services\{GeminiService, PricingCalculatorService};
use App\Models\{Lead, Quote, QuotePhase, QuoteItem, Service};

class PreQuoteAI extends Page
{
    use InteractsWithForms;

    protected static string $view = 'filament.team-panel.pages.pre-quote-ai';
    protected static ?string $navigationLabel = 'Pré-Orçamento IA';
    protected static ?string $navigationGroup = 'Financeiro';
    protected static ?int $navigationSort = 5;

    // State
    public ?int $lead_id = null;
    public string $project_description = '';
    public ?array $aiResult = null;
    public bool $isLoading = false;
    public ?int $generatedQuoteId = null;

    public function analyze(): void
    {
        $this->validate([
            'project_description' => 'required|string|min:50',
        ]);

        $services = Service::where('is_active', true)
            ->with('pricingTiers')
            ->get()
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])
            ->toArray();

        $prompt = $this->buildPrompt($services);
        $raw = app(GeminiService::class)->generateJson($prompt);

        $this->aiResult = $this->enrichWithPricing($raw);
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

        foreach ($result['phases'] as &$phase) {
            $phaseTotal = 0;
            foreach ($phase['items'] as &$item) {
                $price            = $calculator->getPriceForHours($item['service_id'], $item['hours']);
                $item['unit_price'] = $price;
                $item['subtotal']   = round($price * $item['hours'], 2);
                $phaseTotal += $item['subtotal'];
            }
            $phase['subtotal'] = $phaseTotal;
        }

        $result['total'] = collect($result['phases'])->sum('subtotal');

        return $result;
    }

    public function generateQuote(): void
    {
        if (!$this->aiResult) return;

        $quote = Quote::create([
            'lead_id'    => $this->lead_id,
            'title'      => $this->aiResult['project_summary'],
            'number'     => 'AI-' . now()->format('YmdHis'),
            'status'     => 'draft',
            'created_by' => auth()->id(),
        ]);

        foreach ($this->aiResult['phases'] as $order => $phaseData) {
            $phase = QuotePhase::create([
                'quote_id'      => $quote->id,
                'name'          => $phaseData['name'],
                'description'   => $phaseData['description'] ?? null,
                'estimated_days'=> $phaseData['estimated_days'] ?? null,
                'sort_order'    => $order,
            ]);

            foreach ($phaseData['items'] as $itemOrder => $itemData) {
                QuoteItem::create([
                    'quote_id'       => $quote->id,
                    'quote_phase_id' => $phase->id,
                    'service_id'     => $itemData['service_id'],
                    'description'    => $itemData['description'],
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
```

**View:** `resources/views/filament/team-panel/pages/pre-quote-ai.blade.php`

Estrutura da UI (Livewire steps):
1. **Step 1** — Selecione o lead + textarea de descrição + botão "Analisar com IA"
2. **Step 2** — Exibir `$aiResult` em tabela editável por fase (mostrar horas, R$/h, subtotal)
3. **Step 3** — Botão "Gerar Orçamento" + link para o orçamento criado

### 7.3 — Geração de Roadmap e Tarefas por IA no Projeto

**Arquivo:** `app/Filament/TeamPanel/Actions/GenerateRoadmapAction.php`
```php
namespace App\Filament\TeamPanel\Actions;

use Filament\Actions\Action;
use App\Services\GeminiService;
use App\Models\{Project, RoadmapItem, ProjectTask, ProjectPhase};
use Filament\Forms\Components\{Textarea, Toggle, Select};

class GenerateRoadmapAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'generate-roadmap-ai';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Gerar com IA')
            ->modalHeading('Geração de Roadmap e Tarefas com IA')
            ->form([
                Textarea::make('instructions')
                    ->label('Instruções adicionais')
                    ->rows(3)
                    ->placeholder('Ex: foque em mobile-first, use React + Laravel API...'),
                Toggle::make('include_tasks')
                    ->label('Incluir tarefas por fase')
                    ->default(true),
                Toggle::make('skip_existing')
                    ->label('Ignorar itens já existentes')
                    ->default(true),
            ])
            ->action(function (array $data, Project $record): void {
                $existingRoadmap = $record->roadmapItems()->pluck('title')->join(', ');
                $existingTasks   = $record->tasks()->pluck('title')->join(', ');

                $prompt = $this->buildPrompt($record, $existingRoadmap, $existingTasks, $data);
                $result = app(GeminiService::class)->generateJson($prompt);

                $this->persistResult($result, $record, $data);

                \Filament\Notifications\Notification::make()
                    ->title('Roadmap gerado com sucesso!')
                    ->success()
                    ->send();
            });
    }

    private function buildPrompt(Project $project, string $roadmap, string $tasks, array $data): string
    {
        $skip = $data['skip_existing'] ? "NÃO inclua itens já existentes." : "";
        return <<<PROMPT
        Projeto: {$project->name}
        Descrição: {$project->description}
        Roadmap atual: {$roadmap}
        Tarefas atuais: {$tasks}
        Instruções: {$data['instructions']}
        {$skip}

        Gere roadmap e tarefas no formato JSON:
        {
            "roadmap_items": [
                { "title": "...", "description": "...", "type": "milestone|deliverable|review|launch", "planned_date": "YYYY-MM-DD" }
            ],
            "tasks": [
                { "title": "...", "description": "...", "phase_name": "..." }
            ]
        }
        PROMPT;
    }

    private function persistResult(array $result, Project $project, array $data): void
    {
        $existingTitles = $data['skip_existing']
            ? $project->roadmapItems()->pluck('title')->map('strtolower')->toArray()
            : [];

        foreach ($result['roadmap_items'] ?? [] as $item) {
            if (in_array(strtolower($item['title']), $existingTitles)) continue;
            $project->roadmapItems()->create([
                'title'        => $item['title'],
                'description'  => $item['description'] ?? null,
                'type'         => $item['type'] ?? 'milestone',
                'planned_date' => $item['planned_date'] ?? now()->addDays(30)->format('Y-m-d'),
            ]);
        }

        if ($data['include_tasks']) {
            $existingTaskTitles = $data['skip_existing']
                ? $project->tasks()->pluck('title')->map('strtolower')->toArray()
                : [];

            foreach ($result['tasks'] ?? [] as $task) {
                if (in_array(strtolower($task['title']), $existingTaskTitles)) continue;
                $project->tasks()->create([
                    'title'       => $task['title'],
                    'description' => $task['description'] ?? null,
                    'status'      => 'todo',
                ]);
            }
        }
    }
}
```

**Registrar na página EditProject:**
```php
// app/Filament/TeamPanel/Resources/Projects/Pages/EditProject.php
protected function getHeaderActions(): array
{
    return [
        GenerateRoadmapAction::make(),
        Actions\DeleteAction::make(),
    ];
}
```

---

## FASE 8 — MÓDULO FINANCEIRO

> **Acesso:** Role `Financial` exclusivo. Apenas usuários com role `Financial`, `Admin` ou `Super Admin` podem ver este módulo.
> **Política:** Aba financeira em projetos também é controlada por esta role.

### 8.1 — Estrutura de Tabelas

**Migrations a criar:**

```php
// create_banks_table
Schema::create('banks', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code', 10)->nullable();
    $table->string('agency', 20)->nullable();
    $table->string('account_number', 30)->nullable();
    $table->decimal('balance', 15, 2)->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// create_financial_categories_table
Schema::create('financial_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->enum('type', ['income', 'expense'])->default('expense');
    $table->string('color', 7)->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});

// create_suppliers_table
Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('cnpj', 20)->nullable();
    $table->string('email')->nullable();
    $table->string('phone', 20)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();
    $table->softDeletes();
});

// create_financial_transactions_table
Schema::create('financial_transactions', function (Blueprint $table) {
    $table->id();
    $table->enum('type', ['income', 'expense'])->default('expense');
    $table->string('description');
    $table->decimal('amount', 15, 2)->default(0);
    $table->date('due_date');
    $table->date('paid_date')->nullable();
    $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
    $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
    $table->foreignId('financial_category_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
    $table->string('reference_number', 100)->nullable();
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
    $table->softDeletes();

    $table->index('status');
    $table->index('due_date');
    $table->index('type');
});
```

### 8.2 — Role Financial e Controle de Acesso

**Seeder — adicionar role:**
```php
\Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Financial', 'guard_name' => 'web']);
```

**Policy:** `app/Policies/FinancialTransactionPolicy.php`
```php
public function viewAny(User $user): bool
{
    return $user->hasAnyRole(['Super Admin', 'Admin', 'Financial']);
}
```

**Aba financeira em ProjectResource (condicional):**
```php
// No form/view de project, adicionar tab condicional:
Tabs\Tab::make('Financeiro')
    ->visible(fn () => auth()->user()?->hasAnyRole(['Super Admin', 'Admin', 'Financial']))
    ->schema([
        // Relação com financial_transactions do projeto
    ]),
```

**Também registrar no TeamPanelProvider:**
```php
->navigationGroups([
    NavigationGroup::make('CRM'),
    NavigationGroup::make('Projetos'),
    NavigationGroup::make('Financeiro'),
    NavigationGroup::make('Configurações'),
])
```

### 8.3 — Resources do Módulo Financeiro

```
app/Filament/TeamPanel/Resources/Financial/
├── BankResource.php                         (navigationSort: 10)
├── FinancialCategoryResource.php            (navigationSort: 11)
├── SupplierResource.php                     (navigationSort: 12)
└── FinancialTransactionResource.php         (navigationSort: 9)
```

Todos com `$navigationGroup = 'Financeiro'`.

`FinancialTransactionResource` deve incluir:
- Filtros por `type`, `status`, `due_date`, `bank_id`, `project_id`
- Summary widget de saldo (entradas - saídas)
- Action de marcar como pago (selecionar data e banco)

---

## REFERÊNCIA: PADRÕES DE CÓDIGO

### Como criar um novo Resource

```bash
sail artisan make:filament-resource NomeModel --generate --panel=team
```

Depois separar em:
- `Schemas/NomeModelForm.php` — extrair lógica do `form()`
- `Tables/NomeModelsTable.php` — extrair lógica do `table()`

E no Resource:
```php
public static function form(Schema $schema): Schema
{
    return NomeModelForm::configure($schema);
}

public static function table(Table $table): Table
{
    return NomeModelsTable::configure($table);
}
```

### Como criar Migration de Alteração

```bash
sail artisan make:migration add_campo_to_tabela_table --table=tabela
```

```php
public function up(): void
{
    Schema::table('tabela', function (Blueprint $table) {
        $table->string('campo')->nullable()->after('outro_campo');
    });
}

public function down(): void
{
    Schema::table('tabela', function (Blueprint $table) {
        $table->dropColumn('campo');
    });
}
```

### Como registrar Observer

```php
// app/Providers/AppServiceProvider.php :: boot()
\App\Models\NomeModel::observe(\App\Observers\NomeModelObserver::class);
```

### Como adicionar Action em Header de Página

```php
// Em Edit{Model}.php ou outra Page:
protected function getHeaderActions(): array
{
    return [
        Actions\DeleteAction::make(),
        MinhaCustomAction::make(),
    ];
}
```

---

## REFERÊNCIA: MAPA DE DEPENDÊNCIAS DAS NOVAS FEATURES

```
LeadSource        ──────────────────────→ Lead.lead_source_id (FK)
ServiceCategory   ──────────────────────→ Service.service_category_id (FK)
ServicePricingTier ─────────────────────→ Service.id (FK) + PricingCalculatorService
QuotePhase        ──────────────────────→ Quote.id (FK)
QuoteItem.quote_phase_id ───────────────→ QuotePhase.id (FK)
QuoteItem.hours   ──────────────────────→ PricingCalculatorService.getPriceForHours()
ProjectTask.roadmap_item_id ────────────→ RoadmapItem.id (FK)
FinancialTransaction.project_id ────────→ Project.id (FK)
FinancialTransaction.bank_id ───────────→ Bank.id (FK)
GeminiService     ──────────────────────→ PreQuoteAI + GenerateRoadmapAction
PricingCalculatorService ───────────────→ PreQuoteAI::enrichWithPricing()
```

---

## REFERÊNCIA: VARIÁVEIS DE AMBIENTE

```env
# Gemini AI
GEMINI_API_KEY=sua-chave-aqui
GEMINI_MODEL=gemini-1.5-flash

# Locale
APP_LOCALE=pt_BR
APP_TIMEZONE=America/Sao_Paulo
```

---

## ORDEM DE DESENVOLVIMENTO RECOMENDADA

| # | Feature | Fase | Migrations | Pré-requisito |
|---|---|---|---|---|
| 1 | Mensagens validação PT-BR | 1.3 | Não | — |
| 2 | Layout Grid 2 colunas em todos os forms | 1.1 | Não | — |
| 3 | Logo SVG no TeamPanel | 1.2 | Não | — |
| 4 | `website` + `referral_url` no Lead | 2.2 | Sim | — |
| 5 | `ServiceCategory` model + resource | 6.2 | Sim | — |
| 6 | `LeadSource` model + resource | 6.1 | Sim | — |
| 7 | `ServicePricingTier` model + RelMan | 3.3 | Sim | — |
| 8 | `PricingCalculatorService` | 3.3 | Não | ServicePricingTier |
| 9 | `QuotePhase` model + migration | 3.2 | Sim | — |
| 10 | Redesign QuoteForm com fases | 3.2 | Não | QuotePhase |
| 11 | Quote sem cliente obrigatório + cadastro rápido lead | 3.1 | Não | — |
| 12 | Export PDF do orçamento | 3.4 | Não | QuotePhase |
| 13 | `website` no ClientForm | 5.2 | Não | — |
| 14 | View detalhada de Cliente + RelManagers | 5.1 | Não | — |
| 15 | `roadmap_item_id` nas tarefas | 4.1 | Sim | — |
| 16 | Cadastro rápido PF/PJ/Lead no Projeto | 4.2 | Não | — |
| 17 | `quote_id` dependente de `client_id` | 4.3 | Não | — |
| 18 | Kanban de Leads | 2.1 | Não | Alpine.js drag |
| 19 | `GeminiService` base | 7.1 | Não | GEMINI_API_KEY |
| 20 | Página Pré-Orçamento IA | 7.2 | Não | GeminiService + PricingCalc |
| 21 | `GenerateRoadmapAction` | 7.3 | Não | GeminiService |
| 22 | Migrations do módulo financeiro | 8.1 | Sim | — |
| 23 | Models do módulo financeiro | 8.1 | Não | Migrations 8.1 |
| 24 | Resources do módulo financeiro | 8.3 | Não | Models 8.1 |
| 25 | Role Financial + policy | 8.2 | Não | Resources 8.3 |
| 26 | Aba financeira em Projetos | 8.2 | Não | Role Financial |

---

## PROMPT DE CONTEXTO PARA O AGENTE IA

Usar este bloco ao iniciar uma sessão de desenvolvimento com o Claude:

```
Projeto: ProjectHub — Laravel 12 + Filament V4 (gestão de projetos para agência digital)
Stack: Laravel 12, Filament V4, Livewire 3, PostgreSQL, Redis, Laravel Sail

PADRÕES OBRIGATÓRIOS:
1. Resources separam lógica em Schemas/{Model}Form.php e Tables/{Model}sTable.php
2. Filament V4: usar Filament\Schemas\Schema (não Filament\Forms\Form)
   Sections: Filament\Schemas\Components\Section
   Grid: Filament\Schemas\Components\Grid
   Tabs: Filament\Schemas\Components\Tabs + Tab
3. Icons: enum Heroicon (ex: Heroicon::OutlinedFunnel) — não strings
4. Layout: Grid::make(2) na raiz, sections com columnSpan(1) ou columnSpanFull()
5. Validações: lang/pt_BR — nunca mensagens em inglês hardcoded
6. Nunca lógica de form()/table() diretamente no Resource

PAINÉIS:
- Team Panel: path=/admin, guard=web, groups: CRM | Projetos | Financeiro | Configurações
- Client Panel: path=/portal, guard=client_portal, model=ClientPortalUser

MODELS EXISTENTES (com relações):
User, Client (PJ/PF, website, medialib), ClientContact, ClientPortalUser,
Lead (source relacional futuro), LeadNote, Service (categoria relacional futura),
Quote (client nullable, lead nullable), QuoteItem (auto-subtotal),
Project (slug, medialib, activitylog, progress_percent),
ProjectMember (pivot), ProjectPhase, ProjectTask (observer recalc progress),
RoadmapItem (phase_id nullable), ProjectDocument, ProjectComment, TimeLog

PACOTES DISPONÍVEIS: filament/filament ^4, spatie/laravel-permission ^6,
spatie/laravel-medialibrary ^11, spatie/laravel-activitylog ^4,
spatie/laravel-sluggable ^3, barryvdh/laravel-dompdf ^3,
flowframe/laravel-trend, filament/spatie-laravel-media-library-plugin ^4
```
