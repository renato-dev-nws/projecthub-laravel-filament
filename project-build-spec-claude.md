# Sistema de Gestão de Projetos — Laravel 12 + Filament V4
## Documentação Completa de Arquitetura e Implementação

> **Stack:** Laravel 12 · Filament V4 · Livewire V3 · PostgreSQL · Redis · Laravel Sail · Laravel Sanctum

---

## Índice

1. [Visão Geral do Sistema](#1-visão-geral-do-sistema)
2. [Stack Tecnológica e Requisitos](#2-stack-tecnológica-e-requisitos)
3. [MCP Servers para Desenvolvimento com IA](#3-mcp-servers-para-desenvolvimento-com-ia)
4. [Arquitetura do Banco de Dados](#4-arquitetura-do-banco-de-dados)
5. [Estrutura de Módulos e Domínios](#5-estrutura-de-módulos-e-domínios)
6. [Painel Administrativo — Team Panel (Filament)](#6-painel-administrativo--team-panel-filament)
7. [Painel do Cliente — Client Portal (Filament)](#7-painel-do-cliente--client-portal-filament)
8. [Sistema de Autenticação e Roles](#8-sistema-de-autenticação-e-roles)
9. [Módulo de Leads e CRM](#9-módulo-de-leads-e-crm)
10. [Módulo de Orçamentos](#10-módulo-de-orçamentos)
11. [Módulo de Projetos e Roadmap](#11-módulo-de-projetos-e-roadmap)
12. [Sistema de Markdown e Documentação](#12-sistema-de-markdown-e-documentação)
13. [Notificações por E-mail e Banco de Dados](#13-notificações-por-e-mail-e-banco-de-dados)
14. [Filas, Jobs e Automações](#14-filas-jobs-e-automações)
15. [Cache e Performance com Redis](#15-cache-e-performance-com-redis)
16. [Storage e Upload de Arquivos](#16-storage-e-upload-de-arquivos)
17. [Ambiente de Desenvolvimento com Laravel Sail](#17-ambiente-de-desenvolvimento-com-laravel-sail)
18. [Guia de Instalação Passo a Passo](#18-guia-de-instalação-passo-a-passo)
19. [Estrutura de Diretórios do Projeto](#19-estrutura-de-diretórios-do-projeto)
20. [Testes e Qualidade de Código](#20-testes-e-qualidade-de-código)
21. [Considerações de Segurança](#21-considerações-de-segurança)
22. [Deploy e Infraestrutura](#22-deploy-e-infraestrutura)

---

## 1. Visão Geral do Sistema

O sistema é composto por **dois painéis independentes** construídos com Filament V4, cada um com seus próprios contextos de autenticação, roles e funcionalidades:

### Painel 1 — Team Panel (`/admin`)
Destinado à equipe interna da empresa. Gerencia o ciclo completo de um projeto: da captação de leads ao encerramento. Conta com controle de roles, dashboards executivos, gestão de clientes, orçamentos, projetos, roadmaps e documentação interna.

### Painel 2 — Client Portal (`/portal`)
Área exclusiva para clientes acompanharem o progresso de seus projetos. Interface simplificada com acesso restrito apenas ao que lhes pertence: progresso do roadmap, documentos entregues, histórico de comunicação e status geral do projeto.

### Fluxo Principal do Sistema

```
Lead capturado → Qualificação → Proposta/Orçamento → Aprovação
     → Criação do Projeto → Roadmap → Execução por Fases
     → Entrega de Milestones → Documentação → Encerramento
```

---

## 2. Stack Tecnológica e Requisitos

### Requisitos de Ambiente

| Componente | Versão Mínima | Observação |
|---|---|---|
| PHP | 8.2+ | Requerido pelo Laravel 12 |
| Laravel | 12.x | Estável e amplamente suportado |
| Filament | 4.x | Requer Livewire V3 |
| Livewire | 3.x | Base do Filament V4 |
| PostgreSQL | 15+ | Banco de dados principal |
| Redis | 7+ | Cache, filas e sessões |
| Node.js | 20+ | Para assets e MCP servers |
| Composer | 2.6+ | Gerenciamento de dependências PHP |
| Docker Desktop | Atual | Necessário para o Laravel Sail |

### Pacotes PHP Principais

```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/sail": "^1.0",
    "laravel/sanctum": "^4.0",
    "filament/filament": "^4.0",
    "spatie/laravel-permission": "^6.0",
    "spatie/laravel-media-library": "^11.0",
    "spatie/laravel-activitylog": "^4.0",
    "spatie/laravel-tags": "^4.0",
    "league/commonmark": "^2.4",
    "barryvdh/laravel-dompdf": "^3.0",
    "intervention/image-laravel": "^1.3",
    "bezhansalleh/filament-shield": "^3.0",
    "awcodes/filament-tiptap-editor": "^3.0",
    "filament/spatie-laravel-media-library-plugin": "^4.0",
    "filament/spatie-laravel-tags-plugin": "^4.0",
    "filament/spatie-laravel-settings-plugin": "^4.0",
    "saade/filament-fullcalendar": "^3.0",
    "pxlrbt/filament-excel": "^2.0",
    "malzariey/filament-daterangepicker-filter": "^3.0",
    "flowframe/laravel-trend": "^0.2"
  }
}
```

### Pacotes de Desenvolvimento

```json
{
  "require-dev": {
    "pestphp/pest": "^3.0",
    "pestphp/pest-plugin-laravel": "^3.0",
    "larastan/larastan": "^2.9",
    "laravel/pint": "^1.0",
    "nunomaduro/collision": "^8.0",
    "fakerphp/faker": "^1.23"
  }
}
```

> **Nota sobre Reverb e Horizon:** Este projeto não utiliza Laravel Reverb (WebSockets) nem Laravel Horizon nesta versão. Notificações são entregues via e-mail e banco de dados. Filas são processadas com `queue:work` via Sail. Ambos podem ser adicionados em uma fase posterior sem impacto estrutural.

---

## 3. MCP Servers para Desenvolvimento com IA

> **Essencial para quem usa VS Code + GitHub Copilot com Claude Sonnet/Opus.**
> Os MCP Servers fornecem ao agente de IA contexto real sobre seu projeto e sobre o Filament.

### 3.1 Laravel Boost (MCP oficial para Laravel)

O **Laravel Boost** é o MCP server recomendado para projetos Laravel com Filament. Ele conecta seu agente de IA diretamente ao codebase, permitindo que o Copilot entenda a estrutura de arquivos, execute comandos artisan e acesse o contexto do projeto em tempo real.

```bash
# Instalar via Composer (fora do Sail, com PHP do host)
composer require laravel/boost --dev

# Publicar configuração
php artisan vendor:publish --tag="boost-config"

# Gerar configuração do MCP
php artisan boost:install
```

**Configuração para VS Code (`.vscode/mcp.json`):**

```json
{
  "servers": {
    "laravel-boost": {
      "type": "stdio",
      "command": "php",
      "args": [
        "${workspaceFolder}/artisan",
        "boost:mcp"
      ]
    }
  }
}
```

> **Com Laravel Sail:** o Laravel Boost precisa do PHP do host para funcionar como MCP server no VS Code. Se não tiver PHP instalado localmente, instale apenas o PHP CLI (sem extensões extras) para executar o `artisan boost:mcp`. O restante do projeto continua rodando dentro do Sail.

### 3.2 Filament Blueprint (Planejamento assistido por IA)

O **Filament Blueprint** é uma extensão premium do Laravel Boost. Alimenta o agente de IA com conhecimento detalhado sobre componentes, padrões e boas práticas do Filament V4 — eliminando erros de namespace e configuração incorreta de componentes.

Com o Blueprint instalado, você pode pedir ao Copilot:
```
"Using Filament Blueprint, create an implementation plan for
a ProjectResource with Kanban board, timeline, and team assignment
using Filament v4."
```

O agente retorna planos com namespaces corretos, comandos CLI exatos e configurações completas de componentes.

**Compra e instalação:** Disponível em `filamentphp.com/plugins/blueprint`

### 3.3 Filament MCP Server (Referência de componentes)

MCP server open-source que provê documentação e geração de código para Filament diretamente no agente de IA. Suporta Filament V4.

```bash
# Instalar globalmente via npm (no host, não no Sail)
npm install -g filament-mcp-server

# Compilar
cd /path/to/filament-mcp-server && npm run build
```

**Configuração para VS Code (`.vscode/mcp.json` — combinando servidores):**

```json
{
  "servers": {
    "laravel-boost": {
      "type": "stdio",
      "command": "php",
      "args": ["${workspaceFolder}/artisan", "boost:mcp"]
    },
    "filament-docs": {
      "type": "stdio",
      "command": "node",
      "args": ["/absolute/path/to/filament-mcp-server/dist/index.js"]
    }
  }
}
```

### 3.4 Laravel Loop Filament (Expor Resources como MCP)

Para cenários avançados onde o agente de IA precisa interagir com os dados do painel durante o desenvolvimento:

```bash
sail composer require kirschbaum-development/laravel-loop
sail composer require kirschbaum-development/laravel-loop-filament
```

```php
// app/Providers/AppServiceProvider.php
use Kirschbaum\Loop\Loop;
use Kirschbaum\Loop\Filament\FilamentToolkit;
use Kirschbaum\Loop\Enums\Mode;

Loop::toolkit(
    FilamentToolkit::make(mode: Mode::ReadOnly)
);
```

### Resumo da Recomendação de MCP

1. **Laravel Boost** — Obrigatório. Contexto real do projeto Laravel.
2. **Filament Blueprint** — Fortemente recomendado (premium). Elimina erros de implementação.
3. **Filament MCP Server** — Complementar. Referência rápida de componentes V4.

---

## 4. Arquitetura do Banco de Dados

### 4.1 Diagrama de Entidades Principais

```
users ──────────────── project_members (pivot: role, joined_at)
  │                              │
  └── roles/permissions          └── projects
                                      │
clients ──────────────────────────────┤
  │                                   │
  └── client_portal_users             ├── project_phases
                                      │     └── project_tasks
leads ──── lead_notes                 │
  │                                   ├── roadmap_items
  └── converted to ──► clients        │
                │                     ├── project_documents
                └──► quotes           │     (markdown, files)
                          │           │
                          └──► projects── project_comments
                                      │
services ──────────────── quote_items─┴── time_logs
```

### 4.2 Migrations Detalhadas

#### Tabela: `users`

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('avatar_url')->nullable();
    $table->string('phone', 20)->nullable();
    $table->string('position')->nullable();
    $table->string('department')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();

    $table->index('email');
    $table->index('is_active');
});
```

#### Tabela: `clients`

```php
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->string('company_name');
    $table->string('trade_name')->nullable();
    $table->string('cnpj', 20)->nullable()->unique();
    $table->string('cpf', 14)->nullable()->unique();
    $table->enum('type', ['pessoa_juridica', 'pessoa_fisica'])->default('pessoa_juridica');
    $table->string('email')->nullable();
    $table->string('phone', 20)->nullable();
    $table->string('website')->nullable();
    $table->string('industry')->nullable();
    $table->string('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state', 2)->nullable();
    $table->string('zip_code', 10)->nullable();
    $table->string('country', 2)->default('BR');
    $table->enum('status', ['active', 'inactive', 'prospect'])->default('prospect');
    $table->text('notes')->nullable();
    $table->foreignId('account_manager_id')->nullable()->constrained('users');
    $table->timestamps();
    $table->softDeletes();

    $table->index('status');
    $table->index('account_manager_id');
});
```

#### Tabela: `client_contacts`

```php
Schema::create('client_contacts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('email')->nullable();
    $table->string('phone', 20)->nullable();
    $table->string('position')->nullable();
    $table->boolean('is_primary')->default(false);
    $table->boolean('receives_reports')->default(true);
    $table->timestamps();
});
```

#### Tabela: `client_portal_users`

```php
Schema::create('client_portal_users', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->string('avatar_url')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamp('last_login_at')->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
});
```

#### Tabela: `leads`

```php
Schema::create('leads', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->nullable();
    $table->string('phone', 20)->nullable();
    $table->string('company')->nullable();
    $table->string('source')->nullable();
    $table->enum('status', [
        'new', 'contacted', 'qualified', 'proposal_sent',
        'negotiation', 'converted', 'lost'
    ])->default('new');
    $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
    $table->decimal('estimated_value', 15, 2)->nullable();
    $table->text('description')->nullable();
    $table->date('expected_close_date')->nullable();
    $table->foreignId('assigned_to')->nullable()->constrained('users');
    $table->foreignId('converted_client_id')->nullable()->constrained('clients');
    $table->timestamp('converted_at')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index('status');
    $table->index('assigned_to');
});
```

#### Tabela: `lead_notes`

```php
Schema::create('lead_notes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained();
    $table->text('content');
    $table->enum('type', ['call', 'email', 'meeting', 'note'])->default('note');
    $table->timestamp('contacted_at')->nullable();
    $table->timestamps();
});
```

#### Tabela: `services`

```php
Schema::create('services', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('code')->unique();
    $table->text('description')->nullable();
    $table->enum('type', ['fixed', 'hourly', 'monthly'])->default('fixed');
    $table->decimal('default_price', 15, 2)->default(0);
    $table->string('unit', 30)->nullable();
    $table->boolean('is_active')->default(true);
    $table->string('category')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

#### Tabela: `quotes`

```php
Schema::create('quotes', function (Blueprint $table) {
    $table->id();
    $table->string('number')->unique();
    $table->foreignId('client_id')->constrained();
    $table->foreignId('lead_id')->nullable()->constrained();
    $table->foreignId('created_by')->constrained('users');
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('status', [
        'draft', 'sent', 'viewed', 'approved', 'rejected', 'expired'
    ])->default('draft');
    $table->decimal('subtotal', 15, 2)->default(0);
    $table->decimal('discount_percent', 5, 2)->default(0);
    $table->decimal('discount_value', 15, 2)->default(0);
    $table->decimal('tax_percent', 5, 2)->default(0);
    $table->decimal('tax_value', 15, 2)->default(0);
    $table->decimal('total', 15, 2)->default(0);
    $table->string('currency', 3)->default('BRL');
    $table->date('valid_until')->nullable();
    $table->text('terms_conditions')->nullable();
    $table->text('internal_notes')->nullable();
    $table->string('signed_token')->nullable()->unique();
    $table->timestamp('sent_at')->nullable();
    $table->timestamp('viewed_at')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index('status');
    $table->index('client_id');
});
```

#### Tabela: `quote_items`

```php
Schema::create('quote_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
    $table->foreignId('service_id')->nullable()->constrained();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('quantity', 10, 2)->default(1);
    $table->string('unit', 30)->nullable();
    $table->decimal('unit_price', 15, 2)->default(0);
    $table->decimal('discount_percent', 5, 2)->default(0);
    $table->decimal('total', 15, 2)->default(0);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

#### Tabela: `projects`

```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->string('code')->unique();
    $table->foreignId('client_id')->constrained();
    $table->foreignId('quote_id')->nullable()->constrained();
    $table->foreignId('project_manager_id')->constrained('users');
    $table->text('description')->nullable();
    $table->enum('status', [
        'planning', 'active', 'on_hold', 'completed', 'cancelled'
    ])->default('planning');
    $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();
    $table->integer('estimated_hours')->nullable();
    $table->integer('logged_hours')->default(0);
    $table->decimal('budget', 15, 2)->nullable();
    $table->decimal('spent', 15, 2)->default(0);
    $table->integer('progress_percent')->default(0);
    $table->json('settings')->nullable();
    $table->boolean('client_portal_enabled')->default(true);
    $table->boolean('client_can_comment')->default(true);
    $table->string('color', 7)->default('#6366f1');
    $table->timestamps();
    $table->softDeletes();

    $table->index('status');
    $table->index('client_id');
    $table->index('project_manager_id');
});
```

#### Tabela: `project_members`

```php
Schema::create('project_members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('role')->default('member');
    $table->date('joined_at')->nullable();
    $table->timestamps();

    $table->unique(['project_id', 'user_id']);
});
```

#### Tabela: `project_phases`

```php
Schema::create('project_phases', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->enum('status', ['pending', 'in_progress', 'completed', 'blocked'])->default('pending');
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();
    $table->integer('sort_order')->default(0);
    $table->integer('progress_percent')->default(0);
    $table->string('color', 7)->nullable();
    $table->timestamps();
});
```

#### Tabela: `project_tasks`

```php
Schema::create('project_tasks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained()->cascadeOnDelete();
    $table->foreignId('phase_id')->nullable()->constrained('project_phases');
    $table->foreignId('assigned_to')->nullable()->constrained('users');
    $table->foreignId('created_by')->constrained('users');
    $table->foreignId('parent_task_id')->nullable()->constrained('project_tasks');
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('status', [
        'todo', 'in_progress', 'review', 'done', 'blocked'
    ])->default('todo');
    $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
    $table->date('due_date')->nullable();
    $table->integer('estimated_hours')->nullable();
    $table->integer('logged_hours')->default(0);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    $table->softDeletes();

    $table->index('project_id');
    $table->index('assigned_to');
    $table->index('status');
});
```

#### Tabela: `roadmap_items`

```php
Schema::create('roadmap_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained()->cascadeOnDelete();
    $table->foreignId('phase_id')->nullable()->constrained('project_phases');
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('type', ['milestone', 'deliverable', 'review', 'launch'])->default('milestone');
    $table->enum('status', ['planned', 'in_progress', 'completed', 'delayed'])->default('planned');
    $table->date('planned_date');
    $table->date('actual_date')->nullable();
    $table->boolean('is_public')->default(true);
    $table->integer('sort_order')->default(0);
    $table->timestamps();

    $table->index('project_id');
    $table->index('is_public');
});
```

#### Tabela: `project_documents`

```php
Schema::create('project_documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained()->cascadeOnDelete();
    $table->foreignId('created_by')->constrained('users');
    $table->string('title');
    $table->string('slug');
    $table->longText('content')->nullable();
    $table->enum('type', ['markdown', 'file', 'link'])->default('markdown');
    $table->string('file_path')->nullable();
    $table->string('external_url')->nullable();
    $table->boolean('is_public')->default(false);
    $table->enum('visibility', ['team', 'client', 'public'])->default('team');
    $table->string('category')->nullable();
    $table->integer('version')->default(1);
    $table->json('version_history')->nullable();
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    $table->softDeletes();

    $table->index('project_id');
    $table->index('is_public');
    $table->unique(['project_id', 'slug']);
});
```

#### Tabela: `project_comments`

```php
Schema::create('project_comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained()->cascadeOnDelete();
    $table->nullableMorphs('commentable');
    $table->string('author_type');
    $table->unsignedBigInteger('author_id');
    $table->text('content');
    $table->foreignId('parent_id')->nullable()->constrained('project_comments');
    $table->boolean('is_internal')->default(false);
    $table->timestamps();
    $table->softDeletes();

    $table->index(['author_type', 'author_id']);
    $table->index('project_id');
});
```

#### Tabela: `time_logs`

```php
Schema::create('time_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained();
    $table->foreignId('task_id')->nullable()->constrained('project_tasks');
    $table->foreignId('user_id')->constrained();
    $table->text('description')->nullable();
    $table->decimal('hours', 5, 2);
    $table->date('logged_date');
    $table->boolean('is_billable')->default(true);
    $table->timestamps();

    $table->index('project_id');
    $table->index('user_id');
    $table->index('logged_date');
});
```

---

## 5. Estrutura de Módulos e Domínios

```
app/
├── Filament/
│   ├── TeamPanel/
│   │   ├── Resources/
│   │   │   ├── ClientResource/
│   │   │   ├── LeadResource/
│   │   │   ├── QuoteResource/
│   │   │   ├── ProjectResource/
│   │   │   ├── ServiceResource/
│   │   │   └── UserResource/
│   │   ├── Pages/
│   │   │   ├── Dashboard.php
│   │   │   ├── LeadPipeline.php
│   │   │   ├── ProjectKanban.php
│   │   │   └── Reports/
│   │   └── Widgets/
│   │       ├── StatsOverview.php
│   │       ├── ProjectProgressWidget.php
│   │       ├── RecentActivityWidget.php
│   │       └── UpcomingMilestonesWidget.php
│   └── ClientPanel/
│       ├── Resources/
│       │   └── MyProjectResource/
│       ├── Pages/
│       │   ├── Dashboard.php
│       │   ├── RoadmapView.php
│       │   └── DocumentViewer.php
│       └── Widgets/
│           ├── ProjectStatusWidget.php
│           └── UpcomingMilestonesWidget.php
├── Models/
├── Policies/
├── Services/
├── Jobs/
├── Notifications/
├── Observers/
└── Providers/
    └── Filament/
        ├── TeamPanelProvider.php
        └── ClientPanelProvider.php
```

---

## 6. Painel Administrativo — Team Panel (Filament)

### 6.1 Configuração do Panel Provider

```php
// app/Providers/Filament/TeamPanelProvider.php
namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;

class TeamPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Indigo,
                'gray'    => Color::Slate,
            ])
            ->brandName('ProjectHub — Admin')
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                NavigationGroup::make('CRM')->icon('heroicon-o-users'),
                NavigationGroup::make('Projetos')->icon('heroicon-o-folder'),
                NavigationGroup::make('Financeiro')->icon('heroicon-o-currency-dollar'),
                NavigationGroup::make('Configurações')->icon('heroicon-o-cog-6-tooth'),
            ])
            ->discoverResources(
                in: app_path('Filament/TeamPanel/Resources'),
                for: 'App\\Filament\\TeamPanel\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/TeamPanel/Pages'),
                for: 'App\\Filament\\TeamPanel\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/TeamPanel/Widgets'),
                for: 'App\\Filament\\TeamPanel\\Widgets'
            )
            ->middleware([
                \App\Http\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                AuthenticateSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class])
            ->authGuard('web')
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                \Filament\SpatieLaravelMediaLibraryPlugin\MediaLibraryPlugin::make(),
                \Filament\SpatieLaravelTagsPlugin\TagsPlugin::make(),
            ]);
    }
}
```

### 6.2 Dashboard — Widgets e KPIs

```php
// app/Filament/TeamPanel/Widgets/StatsOverview.php
namespace App\Filament\TeamPanel\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\{Project, Lead, Quote, TimeLog};

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Projetos Ativos', Project::where('status', 'active')->count())
                ->description('Em execução agora')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Leads em Aberto', Lead::whereNotIn('status', ['converted', 'lost'])->count())
                ->description('Aguardando tratativa')
                ->descriptionIcon('heroicon-m-funnel')
                ->color('warning'),

            Stat::make('Orçamentos Pendentes', Quote::where('status', 'sent')->count())
                ->description('Aguardando aprovação')
                ->color('info'),

            Stat::make('Horas no Mês',
                TimeLog::whereMonth('logged_date', now()->month)->sum('hours') . 'h'
            )
                ->description('Horas lançadas no mês atual')
                ->color('primary'),
        ];
    }
}
```

### 6.3 ProjectResource — Resource Completo

```php
// app/Filament/TeamPanel/Resources/ProjectResource.php
namespace App\Filament\TeamPanel\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Project;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationGroup = 'Projetos';
    protected static ?string $navigationLabel = 'Projetos';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informações do Projeto')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nome do Projeto')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                            $set('slug', \Illuminate\Support\Str::slug($state))
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Forms\Components\Select::make('client_id')
                        ->label('Cliente')
                        ->relationship('client', 'company_name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('company_name')->required(),
                            Forms\Components\TextInput::make('email'),
                        ]),

                    Forms\Components\Select::make('project_manager_id')
                        ->label('Gerente do Projeto')
                        ->relationship('projectManager', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'planning'  => 'Planejamento',
                            'active'    => 'Em Andamento',
                            'on_hold'   => 'Pausado',
                            'completed' => 'Concluído',
                            'cancelled' => 'Cancelado',
                        ])
                        ->default('planning')
                        ->required(),

                    Forms\Components\Select::make('priority')
                        ->label('Prioridade')
                        ->options([
                            'low'      => 'Baixa',
                            'medium'   => 'Média',
                            'high'     => 'Alta',
                            'critical' => 'Crítica',
                        ])
                        ->default('medium'),
                ])->columns(2),

            Forms\Components\Section::make('Datas e Orçamento')
                ->schema([
                    Forms\Components\DatePicker::make('start_date')->label('Data de Início'),
                    Forms\Components\DatePicker::make('end_date')->label('Previsão de Entrega')->after('start_date'),
                    Forms\Components\TextInput::make('estimated_hours')->label('Horas Estimadas')->numeric()->suffix('h'),
                    Forms\Components\TextInput::make('budget')->label('Orçamento')->numeric()->prefix('R$'),
                ])->columns(2),

            Forms\Components\Section::make('Equipe do Projeto')
                ->schema([
                    Forms\Components\Select::make('members')
                        ->label('Membros da Equipe')
                        ->relationship('members', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload(),
                ]),

            Forms\Components\Section::make('Descrição')
                ->schema([
                    Forms\Components\RichEditor::make('description')
                        ->label('Descrição do Projeto')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Configurações do Portal')
                ->schema([
                    Forms\Components\Toggle::make('client_portal_enabled')->label('Habilitar Portal do Cliente')->default(true),
                    Forms\Components\Toggle::make('client_can_comment')->label('Permitir Comentários do Cliente')->default(true),
                    Forms\Components\ColorPicker::make('color')->label('Cor do Projeto'),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color')->label(''),
                Tables\Columns\TextColumn::make('code')->label('Código')->badge()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Projeto')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('client.company_name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'planning'  => 'info',
                        'active'    => 'success',
                        'on_hold'   => 'warning',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'planning'  => 'Planejamento',
                        'active'    => 'Em Andamento',
                        'on_hold'   => 'Pausado',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                        default     => $state,
                    }),
                Tables\Columns\TextColumn::make('projectManager.name')->label('Gerente')->sortable(),
                Tables\Columns\TextColumn::make('progress_percent')->label('Progresso')->formatStateUsing(fn ($state) => "{$state}%")->sortable(),
                Tables\Columns\TextColumn::make('end_date')->label('Entrega')->date('d/m/Y')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options([
                    'planning' => 'Planejamento', 'active' => 'Em Andamento',
                    'on_hold'  => 'Pausado',      'completed' => 'Concluído',
                ]),
                Tables\Filters\SelectFilter::make('project_manager_id')
                    ->label('Gerente')
                    ->relationship('projectManager', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_roadmap')
                    ->label('Roadmap')
                    ->icon('heroicon-o-map')
                    ->url(fn (Project $record) => static::getUrl('roadmap', ['record' => $record])),
                Tables\Actions\Action::make('view_kanban')
                    ->label('Kanban')
                    ->icon('heroicon-o-view-columns')
                    ->url(fn (Project $record) => static::getUrl('kanban', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\TeamPanel\Resources\ProjectResource\RelationManagers\PhasesRelationManager::class,
            \App\Filament\TeamPanel\Resources\ProjectResource\RelationManagers\TasksRelationManager::class,
            \App\Filament\TeamPanel\Resources\ProjectResource\RelationManagers\RoadmapRelationManager::class,
            \App\Filament\TeamPanel\Resources\ProjectResource\RelationManagers\DocumentsRelationManager::class,
            \App\Filament\TeamPanel\Resources\ProjectResource\RelationManagers\TimeLogsRelationManager::class,
            \App\Filament\TeamPanel\Resources\ProjectResource\RelationManagers\CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'   => \App\Filament\TeamPanel\Resources\ProjectResource\Pages\ListProjects::route('/'),
            'create'  => \App\Filament\TeamPanel\Resources\ProjectResource\Pages\CreateProject::route('/create'),
            'edit'    => \App\Filament\TeamPanel\Resources\ProjectResource\Pages\EditProject::route('/{record}/edit'),
            'roadmap' => \App\Filament\TeamPanel\Resources\ProjectResource\Pages\ProjectRoadmap::route('/{record}/roadmap'),
            'kanban'  => \App\Filament\TeamPanel\Resources\ProjectResource\Pages\ProjectKanban::route('/{record}/kanban'),
        ];
    }
}
```

### 6.4 Página de Roadmap

```php
// app/Filament/TeamPanel/Resources/ProjectResource/Pages/ProjectRoadmap.php
namespace App\Filament\TeamPanel\Resources\ProjectResource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\TeamPanel\Resources\ProjectResource;
use App\Models\Project;

class ProjectRoadmap extends Page
{
    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.team-panel.pages.project-roadmap';

    public Project $record;

    public function mount(Project $record): void
    {
        $this->record = $record->load(['phases.roadmapItems', 'roadmapItems', 'client']);
    }

    public function getTitle(): string
    {
        return "Roadmap — {$this->record->name}";
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('add_milestone')
                ->label('Adicionar Marco')
                ->icon('heroicon-o-plus')
                ->form([
                    \Filament\Forms\Components\TextInput::make('title')->required(),
                    \Filament\Forms\Components\Select::make('type')->options([
                        'milestone'   => 'Marco',
                        'deliverable' => 'Entrega',
                        'review'      => 'Revisão',
                        'launch'      => 'Lançamento',
                    ]),
                    \Filament\Forms\Components\DatePicker::make('planned_date')->required(),
                    \Filament\Forms\Components\Toggle::make('is_public')
                        ->label('Visível para o cliente')
                        ->default(true),
                ])
                ->action(function (array $data) {
                    $this->record->roadmapItems()->create($data);
                    $this->redirect(request()->header('Referer'));
                }),
        ];
    }
}
```

### 6.5 Kanban de Tarefas

```php
// app/Filament/TeamPanel/Resources/ProjectResource/Pages/ProjectKanban.php
namespace App\Filament\TeamPanel\Resources\ProjectResource\Pages;

use Filament\Resources\Pages\Page;
use App\Models\{Project, ProjectTask};
use Livewire\Attributes\On;

class ProjectKanban extends Page
{
    protected static string $resource = \App\Filament\TeamPanel\Resources\ProjectResource::class;
    protected static string $view = 'filament.team-panel.pages.project-kanban';

    public Project $record;
    public array $board = [];

    public function mount(Project $record): void
    {
        $this->record = $record;
        $this->loadBoard();
    }

    public function loadBoard(): void
    {
        $columns = ['todo', 'in_progress', 'review', 'done'];
        $tasks = $this->record->tasks()
            ->with(['assignedTo', 'phase'])
            ->orderBy('sort_order')
            ->get()
            ->groupBy('status');

        foreach ($columns as $col) {
            $this->board[$col] = $tasks->get($col, collect())->toArray();
        }
    }

    #[On('task-status-changed')]
    public function updateTaskStatus(int $taskId, string $status): void
    {
        ProjectTask::find($taskId)?->update(['status' => $status]);
        $this->record->recalculateProgress();
        $this->loadBoard();
    }
}
```

---

## 7. Painel do Cliente — Client Portal (Filament)

### 7.1 Configuração do Client Panel Provider

```php
// app/Providers/Filament/ClientPanelProvider.php
namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use App\Models\ClientPortalUser;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('portal')
            ->login()
            ->colors(['primary' => Color::Blue])
            ->brandName('Meus Projetos')
            ->authModel(ClientPortalUser::class)
            ->authGuard('client_portal')
            ->discoverResources(
                in: app_path('Filament/ClientPanel/Resources'),
                for: 'App\\Filament\\ClientPanel\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/ClientPanel/Pages'),
                for: 'App\\Filament\\ClientPanel\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/ClientPanel/Widgets'),
                for: 'App\\Filament\\ClientPanel\\Widgets'
            )
            ->middleware([
                \App\Http\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Filament\Http\Middleware\AuthenticateSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
                \Filament\Http\Middleware\DisableBladeIconComponents::class,
                \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                \Filament\Http\Middleware\Authenticate::class,
            ]);
    }
}
```

### 7.2 Guard de Autenticação do Portal

```php
// config/auth.php
'guards' => [
    'web' => [
        'driver'   => 'session',
        'provider' => 'users',
    ],
    'client_portal' => [
        'driver'   => 'session',
        'provider' => 'client_portal_users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model'  => App\Models\User::class,
    ],
    'client_portal_users' => [
        'driver' => 'eloquent',
        'model'  => App\Models\ClientPortalUser::class,
    ],
],
```

### 7.3 Visão do Roadmap para o Cliente

```php
// app/Filament/ClientPanel/Pages/RoadmapView.php
namespace App\Filament\ClientPanel\Pages;

use Filament\Pages\Page;
use App\Models\Project;

class RoadmapView extends Page
{
    protected static string $view = 'filament.client-panel.pages.roadmap-view';
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Roadmap';

    public ?Project $project = null;

    public function mount(): void
    {
        $clientId = auth('client_portal')->user()->client_id;

        $this->project = Project::where('client_id', $clientId)
            ->where('client_portal_enabled', true)
            ->with([
                'phases',
                'roadmapItems' => fn($q) => $q->where('is_public', true)->orderBy('planned_date'),
            ])
            ->firstOrFail();
    }
}
```

### 7.4 Visualizador de Documentos Markdown

```php
// app/Filament/ClientPanel/Pages/DocumentViewer.php
namespace App\Filament\ClientPanel\Pages;

use Filament\Pages\Page;
use App\Models\ProjectDocument;
use App\Services\MarkdownService;

class DocumentViewer extends Page
{
    protected static string $view = 'filament.client-panel.pages.document-viewer';
    protected static bool $shouldRegisterNavigation = false;

    public ProjectDocument $document;
    public string $renderedContent = '';

    public function mount(int $documentId): void
    {
        $clientId = auth('client_portal')->user()->client_id;

        $this->document = ProjectDocument::whereHas(
            'project',
            fn($q) => $q->where('client_id', $clientId)
        )
        ->where('id', $documentId)
        ->where('is_public', true)
        ->firstOrFail();

        $this->renderedContent = app(MarkdownService::class)
            ->toHtml($this->document->content ?? '');
    }
}
```

---

## 8. Sistema de Autenticação e Roles

### 8.1 Roles e Permissões com Spatie Permission

```bash
sail artisan shield:install --fresh
sail artisan shield:generate --all
```

```php
// database/seeders/RolesAndPermissionsSeeder.php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$roles = [
    'super_admin', 'admin', 'project_manager',
    'developer', 'designer', 'commercial', 'financial',
];

foreach ($roles as $role) {
    Role::create(['name' => $role, 'guard_name' => 'web']);
}

$modules = [
    'clients', 'leads', 'quotes', 'projects', 'tasks',
    'documents', 'services', 'users', 'reports', 'time_logs',
];
$actions = ['view_any', 'view', 'create', 'update', 'delete', 'export'];

foreach ($modules as $module) {
    foreach ($actions as $action) {
        Permission::create(['name' => "{$action}_{$module}", 'guard_name' => 'web']);
    }
}

Role::findByName('project_manager')->givePermissionTo([
    'view_any_projects', 'view_projects', 'create_projects', 'update_projects',
    'view_any_clients', 'view_clients',
    'view_any_tasks', 'create_tasks', 'update_tasks',
    'view_any_documents', 'create_documents', 'update_documents',
    'view_any_time_logs', 'create_time_logs',
]);

Role::findByName('commercial')->givePermissionTo([
    'view_any_leads', 'view_leads', 'create_leads', 'update_leads',
    'view_any_clients', 'create_clients', 'update_clients',
    'view_any_quotes', 'create_quotes', 'update_quotes',
]);
```

### 8.2 Policy de Projeto com Scoping por Membro

```php
// app/Policies/ProjectPolicy.php
namespace App\Policies;

use App\Models\{User, Project};

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_projects');
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasRole('super_admin')) return true;

        return $user->can('view_projects') &&
            ($user->hasRole(['admin', 'project_manager']) ||
             $project->members()->where('user_id', $user->id)->exists());
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('update_projects') &&
            ($user->hasRole(['super_admin', 'admin', 'project_manager']) ||
             $project->project_manager_id === $user->id);
    }
}
```

---

## 9. Módulo de Leads e CRM

### 9.1 Pipeline Kanban de Leads

```php
// app/Filament/TeamPanel/Pages/LeadPipeline.php
namespace App\Filament\TeamPanel\Pages;

use Filament\Pages\Page;
use App\Models\Lead;
use Livewire\Attributes\On;

class LeadPipeline extends Page
{
    protected static string $view = 'filament.team-panel.pages.lead-pipeline';
    protected static ?string $navigationIcon = 'heroicon-o-funnel';
    protected static ?string $navigationGroup = 'CRM';
    protected static ?string $navigationLabel = 'Pipeline';

    public array $columns = [];

    public function mount(): void
    {
        $this->loadPipeline();
    }

    public function loadPipeline(): void
    {
        $statuses = ['new', 'contacted', 'qualified', 'proposal_sent', 'negotiation'];
        $leads = Lead::whereIn('status', $statuses)
            ->with(['assignedTo'])
            ->get()
            ->groupBy('status');

        foreach ($statuses as $status) {
            $this->columns[$status] = $leads->get($status, collect())->toArray();
        }
    }

    #[On('lead-moved')]
    public function moveLead(int $leadId, string $newStatus): void
    {
        Lead::find($leadId)?->update(['status' => $newStatus]);
        $this->loadPipeline();
    }
}
```

### 9.2 Conversão de Lead em Cliente

```php
// app/Services/LeadConversionService.php
namespace App\Services;

use App\Models\{Lead, Client};

class LeadConversionService
{
    public function convert(Lead $lead, array $clientData): Client
    {
        $client = Client::create(array_merge($clientData, [
            'status'             => 'active',
            'account_manager_id' => $lead->assigned_to,
        ]));

        $lead->update([
            'status'              => 'converted',
            'converted_client_id' => $client->id,
            'converted_at'        => now(),
        ]);

        activity('lead_conversion')
            ->performedOn($lead)
            ->causedBy(auth()->user())
            ->withProperties(['client_id' => $client->id])
            ->log('Lead convertido em cliente');

        return $client;
    }
}
```

---

## 10. Módulo de Orçamentos

### 10.1 Cálculo Automático de Valores

```php
// app/Models/Quote.php
protected static function booted(): void
{
    static::saving(function (Quote $quote) {
        $quote->recalculateTotals();
    });
}

public function recalculateTotals(): void
{
    $subtotal      = $this->items()->sum(\DB::raw('quantity * unit_price'));
    $discountValue = $subtotal * ($this->discount_percent / 100);
    $afterDiscount = $subtotal - $discountValue;
    $taxValue      = $afterDiscount * ($this->tax_percent / 100);

    $this->subtotal       = $subtotal;
    $this->discount_value = $discountValue;
    $this->tax_value      = $taxValue;
    $this->total          = $afterDiscount + $taxValue;
}
```

### 10.2 Link de Aprovação Pública

```php
// routes/web.php
Route::get('/quote/{token}/view',     [QuotePublicController::class, 'show'])->name('quote.public.view');
Route::post('/quote/{token}/approve', [QuotePublicController::class, 'approve'])->name('quote.public.approve');

// app/Http/Controllers/QuotePublicController.php
public function show(string $token): \Illuminate\View\View
{
    $quote = Quote::where('signed_token', $token)
        ->whereIn('status', ['sent', 'viewed'])
        ->firstOrFail();

    if ($quote->status === 'sent') {
        $quote->update(['status' => 'viewed', 'viewed_at' => now()]);
    }

    return view('quotes.public', compact('quote'));
}

public function approve(string $token): \Illuminate\Http\RedirectResponse
{
    $quote = Quote::where('signed_token', $token)
        ->where('status', 'viewed')
        ->firstOrFail();

    $quote->update(['status' => 'approved', 'approved_at' => now()]);
    \App\Jobs\CreateProjectFromQuote::dispatch($quote);

    return redirect()->back()->with('success', 'Orçamento aprovado com sucesso!');
}
```

---

## 11. Módulo de Projetos e Roadmap

### 11.1 Cálculo Automático de Progresso

```php
// app/Models/Project.php
public function recalculateProgress(): void
{
    $totalTasks = $this->tasks()->count();

    if ($totalTasks === 0) {
        $this->update(['progress_percent' => 0]);
        return;
    }

    $completedTasks = $this->tasks()->where('status', 'done')->count();
    $progress = (int) round(($completedTasks / $totalTasks) * 100);

    $this->update(['progress_percent' => $progress]);
    $this->updateMilestoneStatuses();
}

public function updateMilestoneStatuses(): void
{
    $this->roadmapItems()
        ->where('status', 'planned')
        ->where('planned_date', '<', now())
        ->update(['status' => 'delayed']);
}
```

---

## 12. Sistema de Markdown e Documentação

### 12.1 Serviço de Markdown Seguro

```php
// app/Services/MarkdownService.php
namespace App\Services;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdown\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownService
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $environment = new Environment([
            'html_input'         => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level'  => 50,
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    public function toHtml(string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }
}
```

### 12.2 Versionamento de Documentos

```php
// app/Models/ProjectDocument.php
public function saveNewVersion(string $newContent, int $userId): void
{
    $history   = $this->version_history ?? [];
    $history[] = [
        'version'  => $this->version,
        'content'  => $this->content,
        'saved_by' => $userId,
        'saved_at' => now()->toISOString(),
    ];

    $this->update([
        'content'         => $newContent,
        'version'         => $this->version + 1,
        'version_history' => $history,
    ]);
}
```

### 12.3 Editor no Filament com TipTap

```php
use Awcodes\FilamentTiptapEditor\TiptapEditor;

TiptapEditor::make('content')
    ->label('Conteúdo')
    ->profile('default')
    ->columnSpanFull()
    ->extraInputAttributes(['style' => 'min-height: 400px;']),
```

---

## 13. Notificações por E-mail e Banco de Dados

> Este projeto usa notificações via **e-mail** e **banco de dados** (channel `database`). Não há WebSockets nesta versão. Ambos os canais são suficientes para a maioria dos casos de uso.

### 13.1 Notificação de Marco Concluído

```php
// app/Notifications/MilestoneCompleted.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\RoadmapItem;

class MilestoneCompleted extends Notification
{
    use Queueable;

    public function __construct(public RoadmapItem $milestone) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Marco concluído: {$this->milestone->title}")
            ->greeting("Olá, {$notifiable->name}!")
            ->line("O marco **{$this->milestone->title}** foi concluído no projeto **{$this->milestone->project->name}**.")
            ->action('Ver Projeto no Portal', url('/portal'))
            ->line('Acesse o portal para acompanhar o progresso completo.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'      => 'Marco concluído',
            'body'       => $this->milestone->title,
            'project_id' => $this->milestone->project_id,
            'icon'       => 'heroicon-o-check-circle',
            'color'      => 'success',
        ];
    }
}
```

### 13.2 Notificações no Filament (Toast)

```php
use Filament\Notifications\Notification;

// Notificação visual para o usuário atual
Notification::make()
    ->title('Marco concluído!')
    ->body($milestone->title)
    ->success()
    ->icon('heroicon-o-flag')
    ->send();

// Notificação no banco para outro usuário
Notification::make()
    ->title('Tarefa atribuída a você')
    ->body($task->title)
    ->warning()
    ->sendToDatabase($task->assignedTo);
```

### 13.3 Mapa de Notificações do Sistema

| Evento | Canais | Destinatário |
|---|---|---|
| Lead atribuído | e-mail + database | Responsável pelo lead |
| Orçamento enviado | e-mail | Cliente |
| Orçamento aprovado | e-mail + database | Criador do orçamento |
| Projeto criado | e-mail + database | Equipe do projeto |
| Tarefa atribuída | database | Responsável pela tarefa |
| Marco concluído | e-mail + database | Usuários do portal do cliente |
| Progresso +10% | e-mail | Usuários do portal do cliente |
| Comentário novo | database | Participantes da thread |

---

## 14. Filas, Jobs e Automações

### 14.1 Configuração de Filas com Redis

```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'redis'),

'connections' => [
    'redis' => [
        'driver'      => 'redis',
        'connection'  => 'queue',
        'queue'       => env('REDIS_QUEUE', 'default'),
        'retry_after' => 90,
        'block_for'   => null,
    ],
],
```

Rodar o worker via Sail em desenvolvimento:

```bash
sail artisan queue:work redis --queue=default,emails,notifications --tries=3
```

### 14.2 Jobs Principais

```php
// app/Jobs/SendQuoteEmail.php
namespace App\Jobs;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class SendQuoteEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(public Quote $quote) {}

    public function handle(): void
    {
        $this->quote->update(['signed_token' => Str::random(64)]);

        $pdfPath = \App\Services\QuoteService::generateAndStorePdf($this->quote);

        \Illuminate\Support\Facades\Mail::to($this->quote->client->email)
            ->send(new \App\Mail\QuoteSent($this->quote, $pdfPath));
    }
}
```

```php
// app/Jobs/CalculateProjectProgress.php
class CalculateProjectProgress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(public int $projectId) {}

    public function handle(): void
    {
        $project = \App\Models\Project::find($this->projectId);
        if (! $project) return;

        $previousProgress = $project->progress_percent;
        $project->recalculateProgress();
        $project->refresh();

        if (abs($project->progress_percent - $previousProgress) >= 10) {
            $project->client->portalUsers->each(
                fn($user) => $user->notify(
                    new \App\Notifications\ProjectProgressUpdated($project)
                )
            );
        }
    }
}
```

### 14.3 Observers para Automação

```php
// app/Observers/ProjectTaskObserver.php
namespace App\Observers;

use App\Models\ProjectTask;
use App\Jobs\CalculateProjectProgress;

class ProjectTaskObserver
{
    public function updated(ProjectTask $task): void
    {
        if ($task->wasChanged('status')) {
            CalculateProjectProgress::dispatch($task->project_id)
                ->delay(now()->addSeconds(5));

            if ($task->status === 'review' && $task->assignedTo) {
                $task->assignedTo->notify(
                    new \App\Notifications\TaskReadyForReview($task)
                );
            }
        }
    }
}

// app/Providers/AppServiceProvider.php
public function boot(): void
{
    \App\Models\ProjectTask::observe(\App\Observers\ProjectTaskObserver::class);
    \App\Models\Quote::observe(\App\Observers\QuoteObserver::class);
}
```

---

## 15. Cache e Performance com Redis

### 15.1 Múltiplos Databases Redis

```php
// config/database.php — dentro de 'redis'
'cache'   => ['host' => env('REDIS_HOST'), 'password' => env('REDIS_PASSWORD'), 'port' => 6379, 'database' => 1],
'queue'   => ['host' => env('REDIS_HOST'), 'password' => env('REDIS_PASSWORD'), 'port' => 6379, 'database' => 2],
'session' => ['host' => env('REDIS_HOST'), 'password' => env('REDIS_PASSWORD'), 'port' => 6379, 'database' => 3],
```

### 15.2 Cache de Dados Frequentes

```php
// app/Models/Project.php
public static function getActiveForClient(int $clientId): \Illuminate\Support\Collection
{
    return cache()->remember(
        "client:{$clientId}:active_projects",
        now()->addMinutes(15),
        fn() => static::where('client_id', $clientId)
            ->where('status', 'active')
            ->with(['phases', 'roadmapItems' => fn($q) => $q->where('is_public', true)])
            ->get()
    );
}

protected static function booted(): void
{
    static::updated(function (Project $project) {
        cache()->forget("client:{$project->client_id}:active_projects");
    });
}
```

---

## 16. Storage e Upload de Arquivos

### 16.1 Spatie Media Library

```php
// app/Models/Project.php
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model implements HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments')->useDisk('local');

        $this->addMediaCollection('cover')
            ->singleFile()
            ->useDisk('public')
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->fit(Fit::Crop, 400, 200)
                    ->nonQueued();
            });
    }
}
```

### 16.2 Upload no Filament

```php
Forms\Components\SpatieMediaLibraryFileUpload::make('attachments')
    ->label('Arquivos do Projeto')
    ->collection('attachments')
    ->multiple()
    ->maxFiles(20)
    ->maxSize(51200)
    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/zip'])
    ->downloadable()
    ->openable(),
```

---

## 17. Ambiente de Desenvolvimento com Laravel Sail

O **Laravel Sail** é a solução oficial de Docker para Laravel. Fornece um ambiente completo sem necessidade de gerenciar `docker-compose.yml` manualmente, ideal para desenvolvimento.

### 17.1 Serviços Utilizados

| Serviço | Porta | Descrição |
|---|---|---|
| PHP 8.2 + Nginx | 80 | Aplicação Laravel |
| PostgreSQL 15 | 5432 | Banco de dados principal |
| Redis 7 | 6379 | Cache, filas e sessões |
| Mailpit | 1025 / 8025 | SMTP local + UI de e-mails |

### 17.2 Alias Recomendado

Adicione ao `~/.bashrc` ou `~/.zshrc`:

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

### 17.3 Comandos do Dia a Dia

```bash
# Subir / parar ambiente
sail up -d
sail down

# Artisan
sail artisan migrate
sail artisan db:seed
sail artisan optimize:clear

# Filas
sail artisan queue:work redis --queue=default,emails,notifications --tries=3

# Composer / NPM
sail composer require pacote/nome
sail npm install && sail npm run dev

# Testes
sail test
sail test --filter=ProjectTest

# Acesso direto
sail shell    # terminal PHP
sail psql     # PostgreSQL CLI

# Logs
sail logs -f
```

### 17.4 `.env` para Sail

```env
APP_NAME=ProjectHub
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=projecthub
DB_USERNAME=sail
DB_PASSWORD=password

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
BROADCAST_CONNECTION=log

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@projecthub.app
MAIL_FROM_NAME="${APP_NAME}"

FILESYSTEM_DISK=local
```

> **Atenção:** No Sail os hosts são os nomes dos containers (`pgsql`, `redis`, `mailpit`), nunca `localhost` ou `127.0.0.1`.

### 17.5 Personalizar o docker-compose.yml (opcional)

```bash
# Publicar o docker-compose.yml para customização
sail artisan sail:publish
```

---

## 18. Guia de Instalação Passo a Passo

### Passo 1 — Criar o projeto Laravel 12

```bash
composer create-project laravel/laravel projecthub "^12.0"
cd projecthub
```

### Passo 2 — Instalar e iniciar o Sail

```bash
composer require laravel/sail --dev
php artisan sail:install
# Selecionar: pgsql, redis, mailpit

# Adicionar alias ao shell (ver Seção 17.2)
sail up -d
```

### Passo 3 — Instalar dependências principais

```bash
sail composer require \
    filament/filament:"^4.0" \
    filament/spatie-laravel-media-library-plugin:"^4.0" \
    filament/spatie-laravel-tags-plugin:"^4.0" \
    filament/spatie-laravel-settings-plugin:"^4.0" \
    spatie/laravel-permission \
    spatie/laravel-media-library \
    spatie/laravel-activitylog \
    spatie/laravel-tags \
    bezhansalleh/filament-shield \
    awcodes/filament-tiptap-editor \
    league/commonmark \
    barryvdh/laravel-dompdf \
    intervention/image-laravel \
    pxlrbt/filament-excel \
    flowframe/laravel-trend \
    saade/filament-fullcalendar \
    malzariey/filament-daterangepicker-filter \
    laravel/sanctum
```

### Passo 4 — Instalar dependências de desenvolvimento

```bash
sail composer require --dev \
    pestphp/pest \
    pestphp/pest-plugin-laravel \
    "larastan/larastan:^2.9" \
    laravel/pint
```

### Passo 5 — Publicar configurações

```bash
sail artisan filament:install --panels

sail artisan vendor:publish \
    --provider="Spatie\Permission\PermissionServiceProvider"

sail artisan vendor:publish \
    --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" \
    --tag="medialibrary-migrations"

sail artisan vendor:publish \
    --provider="Spatie\Activitylog\ActivitylogServiceProvider" \
    --tag="activitylog-migrations"

sail artisan shield:install
```

### Passo 6 — Criar os Panel Providers

```bash
sail artisan make:filament-panel team
sail artisan make:filament-panel client
```

Registrar em `bootstrap/providers.php`:

```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\TeamPanelProvider::class,
    App\Providers\Filament\ClientPanelProvider::class,
];
```

### Passo 7 — Criar Models e Migrations

```bash
sail artisan make:model Client -mfs
sail artisan make:model ClientContact -m
sail artisan make:model ClientPortalUser -mfs
sail artisan make:model Lead -mfs
sail artisan make:model LeadNote -m
sail artisan make:model Service -mfs
sail artisan make:model Quote -mfs
sail artisan make:model QuoteItem -m
sail artisan make:model Project -mfs
sail artisan make:model ProjectMember -m
sail artisan make:model ProjectPhase -m
sail artisan make:model ProjectTask -m
sail artisan make:model RoadmapItem -m
sail artisan make:model ProjectDocument -m
sail artisan make:model ProjectComment -m
sail artisan make:model TimeLog -m
```

### Passo 8 — Criar Resources Filament

```bash
# Team Panel
sail artisan make:filament-resource Client --generate --panel=team
sail artisan make:filament-resource Lead --generate --panel=team
sail artisan make:filament-resource Quote --generate --panel=team
sail artisan make:filament-resource Project --generate --panel=team
sail artisan make:filament-resource Service --generate --panel=team
sail artisan make:filament-resource User --generate --panel=team

# Client Panel
sail artisan make:filament-resource Project --generate --panel=client

# Relation Managers do Projeto
sail artisan make:filament-relation-manager ProjectResource phases name --panel=team
sail artisan make:filament-relation-manager ProjectResource tasks title --panel=team
sail artisan make:filament-relation-manager ProjectResource roadmapItems title --panel=team
sail artisan make:filament-relation-manager ProjectResource documents title --panel=team
sail artisan make:filament-relation-manager ProjectResource timeLogs description --panel=team
sail artisan make:filament-relation-manager ProjectResource comments content --panel=team
```

### Passo 9 — Configurar guard do portal

Editar `config/auth.php` conforme Seção 7.2.

### Passo 10 — Migrations, seeds e Shield

```bash
sail artisan migrate
sail artisan db:seed
sail artisan shield:generate --all
```

### Passo 11 — Criar super admin

```bash
sail artisan make:filament-user
sail artisan tinker
>>> \App\Models\User::first()->assignRole('super_admin');
```

### Passo 12 — Assets frontend

```bash
sail npm install
sail npm run dev
```

### Passo 13 — Instalar MCP Servers (opcional, recomendado)

```bash
# Laravel Boost — com PHP do host (não do Sail)
composer require laravel/boost --dev
php artisan boost:install

# Filament MCP Server — no host
npm install -g filament-mcp-server
```

Criar `.vscode/mcp.json` conforme Seção 3.

---

## 19. Estrutura de Diretórios do Projeto

```
projecthub/
├── app/
│   ├── Filament/
│   │   ├── TeamPanel/
│   │   │   ├── Pages/
│   │   │   │   ├── Dashboard.php
│   │   │   │   ├── LeadPipeline.php
│   │   │   │   └── Reports/
│   │   │   ├── Resources/
│   │   │   │   ├── ClientResource/
│   │   │   │   │   ├── Pages/
│   │   │   │   │   └── RelationManagers/
│   │   │   │   │       ├── ContactsRelationManager.php
│   │   │   │   │       ├── ProjectsRelationManager.php
│   │   │   │   │       └── QuotesRelationManager.php
│   │   │   │   ├── LeadResource/
│   │   │   │   ├── QuoteResource/
│   │   │   │   ├── ProjectResource/
│   │   │   │   │   ├── Pages/
│   │   │   │   │   │   ├── ListProjects.php
│   │   │   │   │   │   ├── CreateProject.php
│   │   │   │   │   │   ├── EditProject.php
│   │   │   │   │   │   ├── ProjectKanban.php
│   │   │   │   │   │   └── ProjectRoadmap.php
│   │   │   │   │   └── RelationManagers/
│   │   │   │   │       ├── PhasesRelationManager.php
│   │   │   │   │       ├── TasksRelationManager.php
│   │   │   │   │       ├── RoadmapRelationManager.php
│   │   │   │   │       ├── DocumentsRelationManager.php
│   │   │   │   │       ├── TimeLogsRelationManager.php
│   │   │   │   │       └── CommentsRelationManager.php
│   │   │   │   ├── ServiceResource/
│   │   │   │   └── UserResource/
│   │   │   └── Widgets/
│   │   │       ├── StatsOverview.php
│   │   │       ├── ProjectProgressWidget.php
│   │   │       ├── RecentActivityWidget.php
│   │   │       └── UpcomingMilestonesWidget.php
│   │   └── ClientPanel/
│   │       ├── Pages/
│   │       │   ├── Dashboard.php
│   │       │   ├── RoadmapView.php
│   │       │   └── DocumentViewer.php
│   │       ├── Resources/
│   │       │   └── MyProjectResource/
│   │       └── Widgets/
│   │           ├── ProjectStatusWidget.php
│   │           ├── UpcomingMilestonesWidget.php
│   │           └── RecentUpdatesWidget.php
│   ├── Http/Controllers/QuotePublicController.php
│   ├── Jobs/
│   │   ├── SendQuoteEmail.php
│   │   ├── GenerateQuotePdf.php
│   │   ├── CreateProjectFromQuote.php
│   │   ├── SendProjectUpdateNotification.php
│   │   └── CalculateProjectProgress.php
│   ├── Mail/
│   │   ├── QuoteSent.php
│   │   ├── ProjectCreated.php
│   │   └── MilestoneCompleted.php
│   ├── Models/
│   │   ├── User.php               ├── ProjectMember.php
│   │   ├── Client.php             ├── ProjectPhase.php
│   │   ├── ClientContact.php      ├── ProjectTask.php
│   │   ├── ClientPortalUser.php   ├── RoadmapItem.php
│   │   ├── Lead.php               ├── ProjectDocument.php
│   │   ├── LeadNote.php           ├── ProjectComment.php
│   │   ├── Service.php            └── TimeLog.php
│   │   ├── Quote.php
│   │   ├── QuoteItem.php
│   │   └── Project.php
│   ├── Notifications/
│   │   ├── LeadAssigned.php         ├── MilestoneCompleted.php
│   │   ├── QuoteSent.php            ├── ProjectProgressUpdated.php
│   │   ├── QuoteApproved.php        └── TaskReadyForReview.php
│   │   ├── ProjectCreated.php
│   │   └── TaskAssigned.php
│   ├── Observers/
│   │   ├── ProjectTaskObserver.php
│   │   ├── QuoteObserver.php
│   │   └── RoadmapItemObserver.php
│   ├── Policies/
│   │   ├── ProjectPolicy.php
│   │   ├── ClientPolicy.php
│   │   └── QuotePolicy.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── Filament/
│   │       ├── TeamPanelProvider.php
│   │       └── ClientPanelProvider.php
│   └── Services/
│       ├── LeadConversionService.php
│       ├── MarkdownService.php
│       ├── ProjectService.php
│       ├── QuoteService.php
│       └── ReportService.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RolesAndPermissionsSeeder.php
│       ├── ServicesSeeder.php
│       └── DemoDataSeeder.php
├── resources/views/
│   ├── filament/
│   │   ├── team-panel/pages/
│   │   │   ├── lead-pipeline.blade.php
│   │   │   ├── project-kanban.blade.php
│   │   │   └── project-roadmap.blade.php
│   │   └── client-panel/pages/
│   │       ├── dashboard.blade.php
│   │       ├── roadmap-view.blade.php
│   │       └── document-viewer.blade.php
│   ├── quotes/
│   │   ├── public.blade.php
│   │   └── pdf.blade.php
│   └── emails/
│       ├── quote-sent.blade.php
│       └── project-created.blade.php
├── .vscode/mcp.json
├── docker-compose.yml   ← gerado pelo Sail
├── composer.json
└── package.json
```

---

## 20. Testes e Qualidade de Código

### 20.1 Configuração do Pest

```php
// tests/Pest.php
uses(Tests\TestCase::class)->in('Feature', 'Unit');

function actingAsAdmin(): void
{
    $user = \App\Models\User::factory()->create();
    $user->assignRole('admin');
    test()->actingAs($user);
}

function actingAsProjectManager(): void
{
    $user = \App\Models\User::factory()->create();
    $user->assignRole('project_manager');
    test()->actingAs($user);
}

function actingAsClientPortalUser(\App\Models\Client $client): void
{
    $portalUser = \App\Models\ClientPortalUser::factory()->for($client)->create();
    test()->actingAs($portalUser, 'client_portal');
}
```

### 20.2 Feature Tests

```php
// tests/Feature/ProjectTest.php
test('project manager can create a project', function () {
    actingAsProjectManager();
    $client = \App\Models\Client::factory()->create();

    \App\Models\Project::factory()->create([
        'client_id'          => $client->id,
        'project_manager_id' => auth()->id(),
    ]);

    expect(\App\Models\Project::count())->toBe(1);
});

test('client portal user can only see own projects', function () {
    $client      = \App\Models\Client::factory()->create();
    $otherClient = \App\Models\Client::factory()->create();
    $project      = \App\Models\Project::factory()->for($client)->create(['client_portal_enabled' => true]);
    $otherProject = \App\Models\Project::factory()->for($otherClient)->create();

    actingAsClientPortalUser($client);

    $visible = \App\Models\Project::where('client_id', auth('client_portal')->user()->client_id)->get();

    expect($visible->contains($project))->toBeTrue()
        ->and($visible->contains($otherProject))->toBeFalse();
});

test('quote becomes project after approval', function () {
    $quote = \App\Models\Quote::factory()->create([
        'status'       => 'viewed',
        'signed_token' => 'test-token-123',
    ]);

    post(route('quote.public.approve', 'test-token-123'))->assertRedirect();

    expect($quote->fresh()->status)->toBe('approved');
});
```

### 20.3 Qualidade de Código

```bash
# Formatação com Pint
sail exec laravel.test ./vendor/bin/pint

# Análise estática com Larastan
sail exec laravel.test ./vendor/bin/phpstan analyse --level=8

# Testes com cobertura
sail test --coverage
```

---

## 21. Considerações de Segurança

### 21.1 Isolamento Total entre os Painéis

Guards distintos (`web` e `client_portal`), models diferentes (`User` e `ClientPortalUser`) e sessões independentes. Um usuário autenticado no portal nunca acessa rotas do painel admin.

### 21.2 Scoping de Dados no Portal do Cliente

```php
// app/Models/Scopes/ClientScope.php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\{Builder, Model, Scope};

class ClientScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (auth('client_portal')->check()) {
            $builder->where('client_id', auth('client_portal')->user()->client_id);
        }
    }
}
```

### 21.3 Rate Limiting nas Rotas Públicas

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('quote-approval', fn($request) =>
    Limit::perMinute(5)->by($request->ip())
);

// routes/web.php
Route::middleware(['throttle:quote-approval'])->group(function () {
    Route::get('/quote/{token}/view',     [QuotePublicController::class, 'show']);
    Route::post('/quote/{token}/approve', [QuotePublicController::class, 'approve']);
});
```

### 21.4 Sanitização de Markdown

Todo conteúdo markdown de usuários é processado com `html_input: 'strip'` no `MarkdownService` para prevenir XSS.

### 21.5 Proteção de Arquivos Sensíveis

```php
// Documentos internos nunca ficam expostos publicamente
Storage::temporaryUrl($document->file_path, now()->addMinutes(30));
```

---

## 22. Deploy e Infraestrutura

### 22.1 Checklist de Deploy em Produção

```bash
# Dependências
composer install --optimize-autoloader --no-dev
npm ci && npm run build

# Cache
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan icons:cache

# Banco
php artisan migrate --force
php artisan filament:upgrade
```

### 22.2 Supervisor — Queue Worker em Produção

```ini
; /etc/supervisor/conf.d/queue.conf
[program:queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/projecthub/artisan queue:work redis --queue=default,emails,notifications --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/queue-worker.log
stopwaitsecs=3600
```

### 22.3 PostgreSQL — Índices Adicionais

```sql
CREATE INDEX CONCURRENTLY idx_projects_status_client ON projects(status, client_id);
CREATE INDEX CONCURRENTLY idx_tasks_project_status   ON project_tasks(project_id, status);
CREATE INDEX CONCURRENTLY idx_roadmap_project_public ON roadmap_items(project_id, is_public);
CREATE INDEX CONCURRENTLY idx_documents_visibility   ON project_documents(project_id, visibility);
CREATE INDEX CONCURRENTLY idx_time_logs_project_date ON time_logs(project_id, logged_date);
CREATE INDEX CONCURRENTLY idx_leads_status_assigned  ON leads(status, assigned_to);
```

### 22.4 Stack Recomendada em Produção

| Componente | Recomendação |
|---|---|
| Servidor | Laravel Forge + DigitalOcean ou Laravel Cloud |
| Banco de dados | PostgreSQL gerenciado (Supabase, Neon ou RDS) |
| Redis | Upstash, Redis Cloud ou ElastiCache |
| Storage | Amazon S3 ou Cloudflare R2 |
| E-mail | Resend, Postmark ou Amazon SES |
| Monitoramento | Laravel Telescope (dev) + Sentry (produção) |

---

## Apêndice — Referências Rápidas

### URLs do Sistema

| URL | Destino |
|---|---|
| `/admin` | Painel da equipe (Team Panel) |
| `/admin/login` | Login da equipe |
| `/portal` | Portal do cliente (Client Panel) |
| `/portal/login` | Login do cliente |
| `/quote/{token}/view` | Visualização pública de orçamento |
| `/quote/{token}/approve` | Aprovação pública de orçamento |

### Comandos Sail do Dia a Dia

```bash
sail up -d                          # Subir ambiente
sail down                           # Parar ambiente
sail artisan migrate                # Migrations
sail artisan db:seed                # Seeds
sail artisan optimize:clear         # Limpar caches
sail artisan shield:generate --all  # Regenerar permissões
sail artisan shield:super-admin --user=1
sail artisan queue:work redis --tries=3
sail test
sail logs -f
```

### Variáveis Críticas para Produção

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com.br

DB_CONNECTION=pgsql
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis
BROADCAST_CONNECTION=log
FILESYSTEM_DISK=s3
```

---

*Documentação gerada em: abril de 2026 | Laravel 12 + Filament V4 + Livewire V3 + Laravel Sail*