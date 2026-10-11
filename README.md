<div align="center">
  <img src="https://crests.football-data.org/1782.png" alt="EC Vitória" width="100">
  <h1>Dashboard de Análise de Desempenho — EC Vitória</h1>
  <p><strong>Painel de Análise de Desempenho & Inteligência Esportiva</strong></p>
  <p>Uma aplicação web desenvolvida em Laravel para acompanhar, analisar e projetar o desempenho do Esporte Clube Vitória no Brasileirão Série A.</p>
</div>

---

<img width="1351" height="767" alt="Screenshot_1" src="https://github.com/user-attachments/assets/61f4c89b-25cb-4c76-91a4-30e6aa1f725e" />

---

## 📌 Sobre o Projeto

Este dashboard foi desenvolvido com foco o objetivo de transformar os dados brutos das partidas do Vitória em indicadores visuais, dinâmicos e acionáveis para análise esportiva.

A solução consome a **Football-Data API v4**, processa os jogos via rotas automáticas do Artisan CLI em background e renderiza um painel executivo com métricas consolidadas, análises por mando de campo, forma recente e gráficos interativos.

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

- **Visualizações Interativas (Chart.js):**
  - **Evolução da Pontuação:** Gráfico de linha temporal com gradiente rubro-negro acumulando a pontuação rodada a rodada.
  - **Distribuição de Resultados:** Gráfico tipo *Doughnut* (rosca) com o aproveitamento percentual ao centro.

- **Análise de Mando de Campo ("Onde rende mais?"):**
  - Comparativo direto (Lado a Lado / VS) do desempenho no Barradão vs. Como Visitante.
  - Métrica de aproveitamento, pontos somados, gols pró/contra e saldo isolado por mando.

- **Indicadores Dinâmicos & Próximos Jogos:**
  - **Forma Recente:** Badges coloridas com o retrospecto das últimas 5 partidas (`V`, `E`, `D`).
  - **Gols no 2º Tempo:** Análise de poder de reação e volume de gols marcados na etapa final.
  - **Próximas Partidas:** Seção condicional destacando os próximos confrontos, mandos e escudos atualizados dos adversários.
  - **Histórico Completo:** Tabela interativa com filtros em JS puro por mando de campo (*Todos*, *Em casa*, *Fora*).

---

## 🛠️ Tecnologias & Padrões Arquiteturais

### Backend (Laravel 11 & PHP 8.2+)

- **Architecture Patterns:** Arquitetura desacoplada utilizando `Repository Pattern`, `Service Layer` e `Controller`.
- **GoF Design Patterns:** `Adapter Pattern` (`FootballMatchAdapter`) para isolar, normalizar e converter a resposta estruturada da Football-Data API para o banco de dados interno.
- **Background Tasks & CLI:** `Artisan Command` (`coletar:jogos`) para sincronização e resiliência dos dados das partidas.
- **Code Style:** Padronização de código PSR-12 mantida via **Laravel Pint**.
- **Database:** SQLite local leve e rápido.

### Frontend (Blade & Vanilla Web Standards)

- **Blade Templating Engine:** Componentização modular sem poluição de estilos ou scripts embutidos.
- **CSS Grid & Flexbox:** Layout responsivo customizado com paleta rubro-negra moderna.
- **Chart.js:** Gráficos interativos renderizados via Canvas.
- **Vanilla JavaScript:** Filtros assíncronos e manipuladores de DOM no cliente sem dependências pesadas.

---

## 📂 Estrutura do Repositório

```text
dashboardecv/
├── app/
│   ├── Adapters/
│   │   └── FootballMatchAdapter.php    # Converte dados da API em entidades do domínio
│   ├── Console/
│   │   └── Commands/
│   │       └── ColetarJogos.php        # Artisan Command para automação da coleta
│   ├── Http/
│   │   └── Controllers/
│   │       └── DashboardController.php # Controller limpo responsável pela View
│   ├── Repositories/
│   │   └── JogoRepository.php          # Abstração do acesso aos dados/consultas
│   └── Services/
│       ├── DesempenhoTimeService.php   # Regras de negócio, cálculos de KPIs e análises
│       └── FootballApiService.php      # Cliente HTTP para consumo da API externa
├── database/
│   └── database.sqlite                 # Base de dados SQLite
├── public/
│   ├── css/
│   │   └── dashboard.css               # Estilização rubro-negra e componentes
│   └── js/
│       └── dashboard.js                # Inicialização do Chart.js e filtros
├── resources/
│   └── views/
│       └── dashboard.blade.php         # Layout principal do dashboard
└── routes/
    └── web.php                         # Definição das rotas web
```

---

## 💻 Como Rodar o Projeto Localmente

### Pré-requisitos

- PHP >= 8.2
- Composer
- Chave de API da [Football-Data.org](https://www.football-data.org/)

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

   Copie o arquivo `.env.example` para `.env`:

   ```bash
   cp .env.example .env
   ```

   Abra o `.env` e configure a sua chave da API:

   ```env
   FOOTBALL_DATA_API_KEY=sua_chave_aqui
   ```

4. **Executar as migrações do banco de dados:**

   ```bash
   php artisan migrate
   ```

5. **Sincronizar os jogos do Vitória:**

   Rode o comando Artisan para buscar os dados atualizados do Brasileirão Série A:

   ```bash
   php artisan coletar:jogos
   ```

6. **Iniciar o servidor local:**

   ```bash
   php artisan serve
   ```

   Acesse a aplicação no navegador em: `http://localhost:8000`
