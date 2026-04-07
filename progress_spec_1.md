# PROGRESSÃO DE PROJETO E INCLUSÃO DE MÓDULOS

## MÓDULOS EXISTENTES

### LAYOUT
- Nas páginas de formulário deve alterar a estrutura de grids. Na atual estrutura os formulários são divididos por linhas com 2 colunas cada. Vamos usar apenas as colunas. Na coluna 1 ficam os cards ímpares (1,3,5...) na order e na coluna 2 ficam os cards pares da ordem. Pela diferença de tamanho dos cards, no modelo atual o layout fica com muitos espaços em branco. Sendo apenas 2 colunas tudos os cards do form ficam sequenciais.

- Tirar "ProjectHub - Admin" do lugar do brand na navbar e incluir public/img/icon.svg (theme light) e public/img/icon-bg-dark.svg (theme dark)

### VALORES E SERVIÇOS
- Tabela de descontos progressivos (ex: até 10 horas é 150,00 a hora de 11 a 50 horas é 120,00 a hora, etc).
- Implantar tabelas complementares e lógica de cálculos para orçamento.

### LEADS
- Transportar o resorce de leads para um cluster onde o resourse atual é do menu "Lista"
- Criar um resource no cluster leads de Kanban. Esta página deverá ter um kanban personalizado.
- Incluir 2 campos de URL do lead: site do lead e URL de referência

### ORÇAMENTOS
- O sistema de orçamentos pecisa ser mais elaborado, podendo ser visualizado e exportado em PDF (com a logomarca public/img/icon.svg)
- Um orçamento pode ser vinculado a um cliente ou a um lead. Sendo um lead, não pode ser obrigatório um cliente.
- Deve haver um card para as fases do projeto. Os itens serão vinculados às fases com quantas horas serão necessárias e o serviço utilizado, alterando o modelo atual.
- O prazo de conclusão de cada fase deve constar também.
- Na criação de orçamento, no campo de associar a lead, incluir um botão para cadastro rápido com nome e-mail e origem. Botão similar so de cliente no cadastro de projeto. Sendo o lead cadastrado como "novo".

### PROJETOS
- As tarefas podem estar associadas a um item do roadmap correspondente a fase da tarefa.
- Em um novo projeto o cliente a ser cadastrado no cadastro rápido (do campo de seleção de cliente) deve escolher PF, PJ ou Puxar os dados de um lead para o cadastro rápido.
- Apenas depois de selecionar o cliente o campo de orçamento é habilitado apenas com os orçamentos do cliente (ou lead que se trasformou em cliente). Alterar ordem dos campos.

### CLIENTES
- Criar view de cliente com projetos relacionados, orçamentos e demais relações, incluindo usuário.
- Incluir site do cliente

### OUTROS
- Corrigir mensgens de erros dos formulários. Estão retornando como "validation.required", por exemplo.
- Incluir configurações de opções
    - Origem de leads devem ser configuradas (criadas e editas) e usadas com relation nos forms
    - Categorias de serviços devem ser configuradas (criadas e editas) e usadas com relation nos forms

## MÓDULOS A CRIAR

### Integração GEMINI Flash
- Esta integração será feita por hora voltada para os orçamentos e projetos. Na parte de orçamentos preciso do módulo "Pré Orçamento IA" que vai trabalhar da seguinte forma integrada ao GEMINI:

Selecão de Lead (com possibilidade de cadastro rápido) -> Textarea para descrever o que o projeto pede -> Com o prompt adequado envia para o Gemini analisar e gerar as especificações técncas do projeto com as fases, tipos de serviços e horas necessárias -> O sistema processa e mostra o resultado com as fases, horas e valores de acordo com a tabela de serviços e descontos progressivos, para aprovação ou solicitação de alteração -> Quando aprovado os dados retornados de fases horas e valores, associados a um lead formam os dados para gerar um orçamento automaticamente.

- Para projetos o Gemini servirá para gerar roadmaps e tarefas. Ao acionar a analise um diálogo com as tarefas ou roadmap para serem selecionaos ou incluídos todos. Verifica o que já está no roadmap ou tarefas e inclui apenas o que ainda não consta.

### Financeiro
- Criar módulo financeiro com contas a pagar e entradas de projetos
- Cadastro de bancos para relacionar as entradas e saídas
- Cadastro de categorias de débitos e fornecedores
- Financeiro é um role a parte com policy diferenciada. Em projetos apenas quem tem acesso pode ver aba relacionada ao financeiro.