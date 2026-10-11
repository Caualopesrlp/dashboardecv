<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EC Vitória</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    <header class="header">
        <div class="container header-content">
            <h1>🔴⬛ EC Vitória — Painel de Desempenho</h1>
            <span class="badge">Série A 2026</span>
        </div>
    </header>

    <main class="container content">
        <!-- 1. Grid de KPIs Principais -->
        <section class="kpi-grid">
            <div class="card card-highlight">
                <h3>Aproveitamento</h3>
                <p class="kpi-value">{{ $resumo['kpis']['aproveitamento'] }}%</p>
                <span>{{ $resumo['kpis']['pontos'] }} pts em {{ $resumo['kpis']['quantidadeJogos'] }} jogos</span>
            </div>

            <div class="card">
                <h3>Campanha</h3>
                <p class="kpi-value">{{ $resumo['kpis']['vitorias'] }}V - {{ $resumo['kpis']['empates'] }}E -
                    {{ $resumo['kpis']['derrotas'] }}D</p>
                <span>V / E / D</span>
            </div>

            <div class="card">
                <h3>Gols & Saldo</h3>
                <p class="kpi-value">
                    {{ $resumo['kpis']['saldo'] > 0 ? '+' . $resumo['kpis']['saldo'] : $resumo['kpis']['saldo'] }}</p>
                <span>{{ $resumo['kpis']['golsPro'] }} Pró / {{ $resumo['kpis']['golsContra'] }} Contra</span>
            </div>

            <div class="card">
                <h3>Barradão x Fora</h3>
                <p class="kpi-subvalue">Casa: {{ $resumo['mando']['casa']['aproveitamento'] }}%
                    ({{ $resumo['mando']['casa']['pontos'] }}pts)</p>
                <p class="kpi-subvalue">Fora: {{ $resumo['mando']['fora']['aproveitamento'] }}%
                    ({{ $resumo['mando']['fora']['pontos'] }}pts)</p>
            </div>
        </section>

        <!-- 2. Gráfico de Evolução -->
        <section class="chart-section card">
            <h2>Evolução da Pontuação</h2>
            <div class="chart-container">
                <canvas id="graficoEvolucao"></canvas>
            </div>
        </section>

        <!-- 3. Tabela de Jogos -->
        <section class="table-section card">
            <h2>Histórico de Partidas</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Rodada</th>
                            <th>Mando</th>
                            <th>Adversário</th>
                            <th>Placar</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jogos as $jogo)
                            <tr>
                                <td>R{{ $jogo->rodada }}</td>
                                <td><span
                                        class="badge-mando {{ strtolower($jogo->mando) }}">{{ $jogo->mando }}</span>
                                </td>
                                <td>{{ $jogo->adversario }}</td>
                                <td>
                                    @if ($jogo->status === 'FINISHED')
                                        {{ $jogo->mando === 'CASA' ? $jogo->gols_pro_total : $jogo->gols_contra_total }}
                                        x
                                        {{ $jogo->mando === 'CASA' ? $jogo->gols_contra_total : $jogo->gols_pro_total }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-res {{ strtolower($jogo->resultado ?? 'pendente') }}">
                                        {{ $jogo->resultado ?? 'Agendado' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Nenhum jogo cadastrado até o momento.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        window.chartData = @json($resumo['grafico']);
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>

</html>
