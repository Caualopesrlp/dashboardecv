<div align="center">
  <img src="https://crests.football-data.org/1782.png" alt="EC Vitória" width="100">
  <h1>Dashboard de Análise de Desempenho — EC Vitória</h1>
  <p><strong>Painel de Análise de Desempenho & Inteligência Esportiva</strong></p>
  <p>Uma aplicação web desenvolvida em Laravel para acompanhar, analisar e projetar o desempenho do Esporte Clube Vitória no Brasileirão Série A.</p>
</div>

---

<!-- GIF Animado do Dashboard em Funcionamento -->
<div align="center">
  <img width="100%" alt="EC Vitória Dashboard Demo" src="https://github.com/user-attachments/assets/8336ed85-2970-4a1b-b0e2-da6bd179f92e" />
</div>


---

## 📌 Sobre o Projeto

Este dashboard foi desenvolvido com o objetivo de transformar os dados brutos das partidas do Vitória em indicadores visuais, dinâmicos e acionáveis para análise esportiva.

A solução consome a **Football-Data API v4**, processa e persiste os dados no banco SQLite local através de comandos automatizados no **Artisan CLI** (agendados via **Laravel Task Scheduler**) e renderiza um painel executivo com métricas consolidadas, tabela de classificação atualizada, retrospecto de confrontos diretos (H2H) e gráficos interativos.

---

## 💡 Inspiração & Créditos

Este projeto foi **totalmente inspirado** no trabalho de [Pablo Melo](https://github.com/pablohmelo02), que desenvolveu uma análise de desempenho do Clube Náutico e compartilhou a ideia em um post no LinkedIn. A partir dessa referência, adaptei o conceito para o Esporte Clube Vitória.

- 🔗 **Post original no LinkedIn:** [Ver publicação](https://lnkd.in/p/dDZYc7Md)
- 📁 **Repositório do projeto original:** [pablohmelo02/analise_nautico](https://github.com/pablohmelo02/analise_nautico)
- 👤 **GitHub do autor:** [@pablohmelo02](https://github.com/pablohmelo02)

Todo o crédito pela ideia e pela abordagem inicial vai para ele. 👏

---

## 🚀 Funcionalidades & Insights Entregues

- **Visão Executiva & KPIs Gerais:**
  - Aproveitamento percentual, pontuação acumulada e média de pontos por jogo (`PTS/JOGO`).
  - Taxa de vitória e balanço detalhado de campanha (Vitórias, Empates e Derrotas).
  - Saldo de gols, total de gols marcados e sofridos.

- **Tabela do Brasileirão (Standings) & H2H Recente:**
  - **Classificação em Tempo Real:** Tabela completa do Brasileirão Série A com posição, pontos, jogos, saldos e escudos oficiais dos clubes.
  - **Histórico de Confronto Direto (H2H):** Card do próximo confronto exibindo o retrospecto recente do Leão contra o adversário, barras de proporção de vitórias/empates/derrotas e escudos estilizados.

- **Visualizações Interativas (Chart.js):**
  - **Evolução da Pontuação:** Gráfico de linha temporal com gradiente rubro-negro acumulando a pontuação rodada a rodada.
  - **Distribuição de Resultados:** Gráfico tipo *Doughnut* (rosca) com o aproveitamento percentual ao centro.

- **Análise de Mando de Campo ("Onde rende mais?"):**
  - Comparativo direto (Lado a Lado / VS) do desempenho no Barradão vs. como visitante.
  - Métrica de aproveitamento, pontos somados, gols pró/contra e saldo isolado por mando.

- **Indicadores Dinâmicos & Próximos Jogos:**
  - **Forma Recente:** Badges coloridas com o retrospecto das últimas 5 partidas (`V`, `E`, `D`).
  - **Gols no 2º Tempo:** Análise de poder de reação e volume de gols marcados na etapa final.
  - **Próximas Partidas:** Lista cronológica dos próximos compromissos, com datas/horários convertidos para o fuso local (`America/Bahia`) ou marcados como "A definir" quando não detalhados pela CBF.
  - **Histórico Completo:** Tabela interativa com filtros em JS puro por mando de campo (*Todos*, *Em casa*, *Fora*).

---

## 🛠️ Tecnologias & Padrões Arquiteturais

### Backend (Laravel 11 & PHP 8.2+)

- **Architecture Patterns:** Arquitetura limpa e desacoplada utilizando `Repository Pattern`, `Service Layer` e `Controller`.
- **GoF Design Patterns:**
  - `FootballMatchAdapter`: isola, normaliza e converte partidas vindas da API externa.
  - `FootballH2hAdapter`: mapeia e calcula estatísticas e recortes dos confrontos diretos (H2H).
- **Background Tasks & Task Scheduling:** Comandos Artisan dedicados (`jogos:coletar`, `classificacao:coletar` e `confrontos:coletar`) orquestrados pelo **Laravel Task Scheduler** em `routes/console.php`.
- **Centralized Configuration:** Configurações globais do Vitória (ID do time, URLs e metadados) centralizadas em `config/vitoria.php`.
- **Automated Testing:** Suíte de testes automatizados com PHPUnit/Pest configurados com `RefreshDatabase`.
- **Code Style:** Padronização de código PSR-12 garantida via **Laravel Pint**.
- **Database:** SQLite local de alta performance.

### Frontend (Blade & Vanilla Web Standards)

- **Blade Templating Engine:** Componentização modular sem poluição de estilos ou scripts embutidos.
- **CSS Grid & Flexbox:** Layout responsivo customizado com paleta rubro-negra moderna e estilização adaptativa para dispositivos móveis.
- **Chart.js:** Gráficos interativos renderizados via Canvas.
- **Vanilla JavaScript:** Filtros e manipuladores de DOM leves no cliente.

---

## 📂 Estrutura do Repositório

```text
dashboardecv/
├── app/
│   ├── Adapters/
│   │   ├── FootballH2hAdapter.php        # Adapter para dados de confronto direto (H2H)
│   │   └── FootballMatchAdapter.php      # Adapter para normalização das partidas
│   ├── Console/
│   │   └── Commands/
│   │       ├── ColetarClassificacao.php  # Coletor da tabela de classificação
│   │       ├── ColetarConfrontoDireto.php # Coletor de dados H2H do próximo jogo
│   │       └── ColetarJogos.php          # Coletor das partidas do Vitória
│   ├── Http/
│   │   └── Controllers/
│   │       └── DashboardController.php   # Controller principal do Dashboard
│   ├── Repositories/
│   │   ├── ClassificacaoRepository.php   # Abstração da tabela de classificação
│   │   ├── ConfrontoDiretoRepository.php # Abstração dos dados H2H
│   │   └── JogoRepository.php            # Abstração das partidas e busca por data
│   └── Services/
│       ├── ClassificacaoService.php      # Regras da classificação
│       ├── ConfrontoService.php          # Regras do confronto direto
│       ├── DesempenhoTimeService.php     # Regras de negócio e cálculo de KPIs
│       └── FootballApiService.php        # Cliente HTTP para consumo da API externa
├── config/
│   └── vitoria.php                       # Configurações globais e ID do clube (1782)
├── database/
│   └── database.sqlite                   # Base de dados SQLite
├── public/
│   ├── css/
│   │   └── dashboard.css                 # Estilização rubro-negra responsiva
│   └── js/
│       └── dashboard.js                  # Inicialização do Chart.js e filtros DOM
├── resources/
│   └── views/
│       └── dashboard.blade.php           # View principal componentizada
├── routes/
│   ├── console.php                       # Agendamento das tarefas (Scheduler)
│   └── web.php                           # Rotas web do sistema
└── tests/                                # Suíte de testes automatizados
```

---

## 💻 Como Rodar o Projeto Localmente

### Pré-requisitos

- PHP >= 8.2
- Composer
- Chave de API gratuita da [Football-Data.org](https://www.football-data.org/)

### Passo a Passo

1. **Clonar o repositório:**

```bash
   git clone https://github.com/SEU_USUARIO/dashboardecv.git
   cd dashboardecv
```

2. **Instalar as dependências:**

```bash
   composer install
```

3. **Configurar as variáveis de ambiente:**

```bash
   cp .env.example .env
```

   Abra o arquivo `.env` e insira a sua chave da API:

```env
   FOOTBALL_DATA_API_KEY=sua_chave_aqui
```

4. **Executar as migrações do banco de dados:**

```bash
   php artisan migrate
```

5. **Sincronizar os dados com a API** (jogos, tabela e H2H inicial):

```bash
   php artisan jogos:coletar
   php artisan classificacao:coletar
   php artisan confrontos:coletar
```

6. **Rodar a suíte de testes (opcional):**

```bash
   php artisan test
```

7. **Iniciar o servidor local:**

```bash
   php artisan serve
```

   Acesse a aplicação no navegador em: `http://localhost:8000`
