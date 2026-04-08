# ProjectHub

Sistema completo para gestão de operações de uma software house/agência digital, com foco em CRM, orçamentos, execução de projetos e financeiro.

## Visão Geral

O projeto possui dois painéis Filament:

- **TeamPanel** (`/admin`): operação interna da equipe (vendas, projetos, financeiro e configurações)
- **ClientPanel** (`/portal`): portal do cliente para acompanhamento dos próprios projetos

Stack principal:

- Laravel 12
- Filament 4
- Tailwind CSS 4 + Vite
- PostgreSQL + Redis + Mailpit (via Sail)
- Spatie Permission, Activitylog e Media Library
- Integração com Google Gemini para recursos de IA

---

## Funcionalidades do Sistema (Inventário Completo)

## 1. CRM de Leads

1. Cadastro e gestão de leads com dados comerciais.
2. Pipeline por status (`new`, `contacted`, `qualified`, `proposal_sent`, `negotiation`, `converted`, `lost`).
3. Priorização de leads (baixa, média, alta).
4. Atribuição de lead para usuário responsável.
5. Controle de origem de lead (Lead Sources).
6. Registro de notas/histórico por lead.
7. Cluster de Leads no menu CRM com:
   - Lista de leads.
   - Formulário de criação/edição.
   - **Kanban de Leads** com drag-and-drop entre status.

## 2. Pré-Orçamento com IA

1. Página dedicada de **Pré-Orçamento IA**.
2. Entrada de descrição do projeto (texto livre) e lead opcional.
3. Consulta à API Gemini para sugerir escopo em JSON.
4. Enriquecimento automático com precificação real dos serviços cadastrados.
5. Geração de orçamento estruturado com fases + itens.
6. Criação automática de orçamento em estado draft após confirmação.

## 3. Orçamentos (Quotes)

1. CRUD completo de orçamentos.
2. Organização por fases (`QuotePhase`) e itens (`QuoteItem`).
3. Cálculo e recálculo automático de totais.
4. Campos de acompanhamento comercial, incluindo visualização/aprovação.
5. Geração de PDF de orçamento via rota autenticada.

## 4. Projetos

1. CRUD completo de projetos.
2. Relações gerenciadas diretamente no projeto:
   - Fases.
   - Tarefas.
   - Roadmap.
   - Documentos.
   - Time logs.
   - Comentários.
3. Configurações de visibilidade no portal do cliente:
   - `client_portal_enabled`
   - `client_can_comment`
4. Ação de **geração de roadmap com IA** na edição do projeto.

## 5. Tarefas e Execução

1. Tarefas com status, prioridade e ordenação.
2. Atribuição de responsável.
3. Associação opcional a item do roadmap.
4. Observer para recálculo de progresso do projeto quando tarefas mudam.

## 6. Roadmap

1. Gestão de roadmap por projeto.
2. Relação entre roadmap e tarefas.
3. Geração assistida por IA para sugerir itens e tarefas.

## 7. Financeiro

1. **Transações financeiras** (receita/despesa).
2. Vínculos de transação com projeto, cliente, fornecedor, banco e categoria.
3. Regras de formulário para receitas:
   - Seleção de projeto.
   - Preenchimento automático do cliente a partir do projeto.
4. Campo de forma de pagamento (`payment_method`).
5. Campo de link de pagamento (`payment_link`).
6. Controle de status da transação (`pending`, `paid`, `overdue`, `cancelled`).

## 8. Fornecedores e Bancos

1. Cadastro de fornecedores com categoria.
2. Cadastro de bancos.
3. Categorias financeiras e categorias de fornecedores.
4. Recursos simples com criação/edição por modal para entidades pequenas.

## 9. Clientes e Portal

1. Cadastro de clientes e contatos.
2. Usuários de portal (`ClientPortalUser`) com guard próprio.
3. No portal do cliente:
   - Dashboard com widgets de projetos.
   - Listagem de projetos do próprio cliente.
   - Sem criação/edição/exclusão no portal.

## 10. Serviços

1. Catálogo de serviços.
2. Categorias de serviço.
3. Faixas de preço por serviço (`ServicePricingTier`) usadas na precificação.

## 11. Usuários, Perfis e Permissões

1. Gestão de usuários internos.
2. Controle por roles/permissões (Spatie Permission).
3. Policies para entidades críticas (Projetos, Leads, Clientes, Orçamentos).
4. Guardas separados:
   - `web` (equipe interna).
   - `client_portal` (cliente externo).

## 12. Auditoria e Mídia

1. Activity Log para rastreabilidade de mudanças (modelos com log habilitado).
2. Media Library para anexos e arquivos em entidades suportadas.

---

## Arquitetura Funcional

Fluxo principal de negócio:

1. Lead entra no CRM.
2. Oportunidade evolui no pipeline (lista/kanban).
3. Pré-orçamento com IA acelera construção da proposta.
4. Orçamento é finalizado e acompanhado.
5. Projeto é executado com fases, tarefas, roadmap e apontamento.
6. Financeiro acompanha receitas/despesas por contexto (cliente/projeto/fornecedor/banco).
7. Cliente acompanha execução no portal.

---

## Detalhes Técnicos Relevantes

## Painéis Filament

- **TeamPanel**
  - Path: `/admin`
  - Guard: `web`
  - Tema Vite customizado
  - Navegação agrupada: Projetos, CRM, Financeiro, Configurações

- **ClientPanel**
  - Path: `/portal`
  - Guard: `client_portal`
  - Acesso restrito aos projetos do cliente autenticado

## Estrutura de Código

- `app/Filament/TeamPanel`: recursos, páginas e widgets do painel admin
- `app/Filament/ClientPanel`: recursos, páginas e widgets do portal
- `app/Filament/TeamPanel/Clusters/Leads`: cluster de Leads com Kanban
- `app/Models`: domínio de negócio
- `app/Policies`: autorização
- `app/Observers`: automações de domínio
- `app/Services`: integrações e regras de aplicação (IA/precificação)

## IA (Gemini)

- Serviço dedicado em `GeminiService`.
- Modelo padrão configurável por variável de ambiente.
- Fallback de modelos quando houver indisponibilidade (404) de um modelo específico.

## Banco de Dados e Seeds

- Migrations orientadas a `migrate:fresh --seed`.
- Seeders para:
  - Roles/permissões
  - Usuários
  - Clientes e usuários de portal
  - Leads
  - Serviços e categorias
  - Bancos
  - Categorias financeiras
  - Fornecedores e categorias
  - Orçamentos
  - Projetos

---

## Setup do Projeto

## Pré-requisitos

- PHP 8.2+
- Composer
- Node.js + npm
- Docker (recomendado com Sail)

## 1) Instalação rápida

```bash
composer run setup
```

Esse comando executa:

1. Instala dependências PHP
2. Cria `.env` (se não existir)
3. Gera `APP_KEY`
4. Executa migrations
5. Instala dependências front-end
6. Gera build de produção

## 2) Rodando em desenvolvimento (sem Docker)

```bash
composer run dev
```

Sobe, em paralelo:

- servidor Laravel
- queue listener
- pail (logs)
- vite dev server

## 3) Rodando com Sail (Docker)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run build
```

Serviços do `compose.yaml`:

- app (`laravel.test`)
- PostgreSQL
- Redis
- Mailpit

## 4) Testes

```bash
composer run test
```

Para criar banco de testes no PostgreSQL do Sail:

```bash
make testing-db
```

---

## Variáveis de Ambiente Importantes

No `.env`, configurar especialmente:

- Banco de dados (`DB_*`)
- Redis (`REDIS_*`)
- Mail (`MAIL_*`)
- Gemini:

```env
GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash
```

---

## Credenciais de Seed (Ambiente Local)

O `DatabaseSeeder` garante um usuário admin padrão:

- Email: `admin@projecthub.app`
- Senha: `password`

> Recomendação: altere imediatamente em qualquer ambiente compartilhado.

---

## Scripts Úteis

```bash
# Front-end
npm run dev
npm run build

# Laravel
php artisan migrate
php artisan migrate:fresh --seed
php artisan queue:listen
php artisan pail

# Sail
./vendor/bin/sail artisan route:list
./vendor/bin/sail artisan optimize:clear
```

---

## Contribuição Técnica

Para evoluir o projeto com consistência:

1. Seguir o padrão de Resources do Filament já adotado (Schemas, Tables, Pages separados).
2. Manter regras de autorização via Policies.
3. Preferir Services para lógica de negócio complexa (ex.: IA e precificação).
4. Atualizar seeders quando adicionar novos módulos essenciais.
5. Validar comportamento nos dois painéis (`/admin` e `/portal`) quando a mudança afetar domínio compartilhado.

---

## Licença

Este projeto está sob licença MIT.
