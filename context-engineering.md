# Context Engineering - nw-laravel-projects-panel

## 1. Visão Geral do Projeto

Este projeto é uma aplicação Laravel 12 com foco em gestão de clientes, projetos, propostas (quotes), leads e operações internas de equipe.

A interface de administração é construída com Filament 4, usando dois painéis distintos:
- `admin` para a equipe interna (`TeamPanel`) em `/admin`
- `portal` para clientes externos (`ClientPanel`) em `/portal`

O objetivo é uma plataforma híbrida onde a equipe administra CRM, projetos e finanças, enquanto clientes acessam seu próprio portal de projetos.

## 2. Arquitetura Técnica

### 2.1 Tecnologias principais

- Laravel 12
- Filament 4 (Painéis, Recursos, Páginas, Widgets)
- Tailwind CSS + Vite
- PHP 8.2
- Spatie:
  - `spatie/laravel-permission`
  - `spatie/laravel-activitylog`
  - `spatie/laravel-medialibrary`
  - `spatie/laravel-sluggable`
  - `spatie/laravel-tags`
- `barryvdh/laravel-dompdf`
- `intervention/image-laravel`
- `flowframe/laravel-trend`

### 2.2 Estrutura de diretórios relevante

- `app/Providers/Filament/`
  - `TeamPanelProvider.php`
  - `ClientPanelProvider.php`
- `app/Filament/TeamPanel/`
  - `Resources/`, `Pages/`, `Widgets/`
- `app/Filament/ClientPanel/`
  - `Resources/`, `Pages/`, `Widgets/`
- `app/Models/`
  - `User.php`
  - `ClientPortalUser.php`
  - `Client.php`
  - `Project.php`
  - `Quote.php`
  - demais modelos de domínio
- `config/auth.php`
- `routes/web.php`

### 2.3 Fluxo de roteamento

O arquivo `routes/web.php` contém apenas uma rota padrão `/` que retorna a view `welcome`.
A maior parte da aplicação é exposta via Filament nos caminhos configurados pelos painéis.

## 3. Painéis Filament e Domínios

### 3.1 TeamPanel (`admin`)

- ID do painel: `admin`
- Path: `admin`
- Guard Auth: `web`
- Nome exibido: `ProjectHub — Admin`
- Grupos de navegação: `CRM`, `Projetos`, `Financeiro`, `Configurações`
- Recursos descobertos em: `app/Filament/TeamPanel/Resources`
- Páginas descobertas em: `app/Filament/TeamPanel/Pages`
- Widgets descobertos em: `app/Filament/TeamPanel/Widgets`

Este painel gerencia recursos como:
- Clientes (`Clients`)
- Projetos (`Projects`)
- Leads (`Leads`)
- Cotações (`Quotes`)
- Serviços (`Services`)
- Usuários internos (`Users`)

Além disso, o modelo `Project` expõe relation managers para:
- `Documents`
- `RoadmapItems`
- `Comments`
- `Phases`
- `Tasks`
- `TimeLogs`

### 3.2 ClientPanel (`portal`)

- ID do painel: `client`
- Path: `portal`
- Guard Auth: `client_portal`
- Nome exibido: `Meus Projetos`
- Recursos descobertos em: `app/Filament/ClientPanel/Resources`
- Páginas descobertas em: `app/Filament/ClientPanel/Pages`
- Widgets descobertos em: `app/Filament/ClientPanel/Widgets`

O portal do cliente fornece acesso dedicado a seus próprios projetos e dados relacionados.

## 4. Autenticação e Autorização

### 4.1 Guards e providers

`config/auth.php` define dois guards com providers separados:

- `web` → usuário interno `App\Models\User`
- `client_portal` → usuário de portal `App\Models\ClientPortalUser`

Também existem brokers de senha separados para cada tipo.

### 4.2 Lógica de acesso aos painéis

#### `App\Models\User`
- Implementa `FilamentUser`
- Método `canAccessPanel(Panel $panel)` retorna verdadeiro somente se:
  - `is_active` for verdade
  - `panel->getId() === 'admin'`

#### `App\Models\ClientPortalUser`
- Implementa `FilamentUser`
- Método `canAccessPanel(Panel $panel)` retorna verdadeiro somente se:
  - `is_active` for verdade
  - `panel->getId() === 'client'`

## 5. Modelo de Domínio e Relacionamentos

### 5.1 Clientes e usuários do portal

- `Client`
  - `accountManager()` → `User`
  - `contacts()` → `ClientContact`
  - `portalUsers()` → `ClientPortalUser`
  - `projects()` → `Project`
  - `quotes()` → `Quote`

- `ClientPortalUser`
  - `client()` → `Client`

### 5.2 Projetos

- `Project`
  - `client()` → `Client`
  - `projectManager()` → `User`
  - `quote()` → `Quote`
  - `members()` → `User` (pivot `project_members`)
  - `projectMembers()` → `ProjectMember`
  - `phases()` → `ProjectPhase`
  - `tasks()` → `ProjectTask`
  - `roadmapItems()` → `RoadmapItem`
  - `documents()` → `ProjectDocument`
  - `comments()` → `ProjectComment`
  - `timeLogs()` → `TimeLog`

- Propriedades importantes: `client_portal_enabled`, `client_can_comment`, `progress_percent`, `budget`, `spent`, `estimated_hours`

### 5.3 Propostas e cotações

- `Quote`
  - `client()` → `Client`
  - `lead()` → `Lead`
  - `creator()` → `User`
  - `items()` → `QuoteItem`

- Valores financeiros: `subtotal`, `discount_percent`, `tax_percent`, `total`
- Fluxos de status/envio/visualização/aprovação

### 5.4 Usuários internos e equipe

- `User`
  - usa `HasRoles` do Spatie
  - `managedClients()`, `assignedLeads()`, `managedProjects()`, `assignedTasks()` etc.
  - `SoftDeletes`

### 5.5 Histórico / mídia / auditoria

- `Client` e `Project` usam `LogsActivity`
- `Client` e `Project` usam `InteractsWithMedia`
- `Project` usa `HasSlug`

## 6. Pontos de integração e pacotes de suporte

- `barryvdh/laravel-dompdf` para geração de PDF (provável uso em propostas ou relatórios)
- `Intervention/Image` para manipulação de imagens
- `filament/spatie-laravel-media-library-plugin` para integração Filament + Media Library
- `filament/spatie-laravel-tags-plugin` para tagueamento de recursos Filament
- `flowframe/laravel-trend` para métricas e tendências
- `spatie/laravel-permission` para controle de acesso baseado em papéis

## 7. Observações de contexto

- O frontend público é mínimo e mantém apenas a rota home `/`.
- A aplicação é essencialmente centrada em painéis Filament para gerenciamento interno e portal de cliente.
- A existência de `TeamPanel` e `ClientPanel` indica separação clara de responsabilidades entre equipe e cliente.
- A política de autenticação é estrita: cada modelo só acessa seu painel específico.

## 8. Recomendações para continuidade

- Revisar `app/Filament/TeamPanel/Resources` e `app/Filament/ClientPanel/Resources` para confirmar quais campos são expostos e quais actions estão habilitadas.
- Verificar eventos de modelo e listeners caso haja lógica adicional de sincronização ou notificações.
- Garantir que `client_portal_enabled` no `Project` esteja alinhado com a experiência do portal de clientes.
- Documentar o fluxo de inscrição/login de `ClientPortalUser` se houver suporte a criação de contas via cliente.
