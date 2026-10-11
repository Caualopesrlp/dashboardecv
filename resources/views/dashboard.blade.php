<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EC Vitória — Painel de Desempenho</title>
    <link rel="icon" type="image/png" href="https://crests.football-data.org/1782.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

    <!-- ===== HEADER ===== -->
    <header class="header">
        <div class="container header-content">
            <div class="header-brand">
                <img src="https://crests.football-data.org/1782.png"
                     alt="EC Vitória"
                     class="team-logo"
                     onerror="this.style.display='none'">
                <div class="header-text">
                    <span class="header-competition">BRASILEIRÃO · SÉRIE A · 2026</span>
                    <h1>Painel de Desempenho</h1>
                    <span class="header-subtitle">Visão executiva da campanha do Vitória no campeonato</span>
                </div>
            </div>
            <div class="header-meta">
                <div class="meta-item">
                    <span class="meta-label">JOGOS DISPUTADOS</span>
                    <span class="meta-value">{{ $resumo['kpis']['quantidadeJogos'] }}</span>
                </div>
                <div class="meta-divider"></div>
                <div class="meta-item">
                    <span class="meta-label">PONTOS</span>
                    <span class="meta-value accent">{{ $resumo['kpis']['pontos'] }}</span>
                </div>
            </div>
        </div>
    </header>

    <main class="container content">

        <!-- ===== KPIs ===== -->
        <section class="kpi-grid">

            <!-- Aproveitamento -->
            <div class="card card-highlight">
                <div class="card-label">APROVEITAMENTO <span class="label-tag">GERAL</span></div>
                <div class="kpi-value">{{ $resumo['kpis']['aproveitamento'] }}%</div>
                <div class="kpi-sub">
                    {{ $resumo['kpis']['pontos'] }} de {{ $resumo['kpis']['quantidadeJogos'] * 3 }} pontos possíveis
                </div>
            </div>

            <!-- Campanha -->
            <div class="card">
                <div class="card-label">CAMPANHA
                    <span class="label-tag">{{ $resumo['kpis']['vitorias'] }}V · {{ $resumo['kpis']['empates'] }}E · {{ $resumo['kpis']['derrotas'] }}D</span>
                </div>
                <div class="campanha-blocks">
                    <div class="campanha-block win">
                        <span class="block-num">{{ $resumo['kpis']['vitorias'] }}</span>
                        <span class="block-label">Vitórias</span>
                    </div>
                    <div class="campanha-block draw">
                        <span class="block-num">{{ $resumo['kpis']['empates'] }}</span>
                        <span class="block-label">Empates</span>
                    </div>
                    <div class="campanha-block loss">
                        <span class="block-num">{{ $resumo['kpis']['derrotas'] }}</span>
                        <span class="block-label">Derrotas</span>
                    </div>
                </div>
                <div class="kpi-sub">Vitória 3pts · Empate 1pt · Derrota 0pt</div>
            </div>

            <!-- Média por jogo -->
            <div class="card">
                <div class="card-label">MÉDIA POR JOGO <span class="label-tag">PTS / JOGO</span></div>
                <div class="kpi-value">{{ $resumo['kpis']['mediaPontos'] }}</div>
                <div class="kpi-sub">
                    Taxa de vitória: <strong>{{ $resumo['kpis']['taxaVitoria'] }}%</strong>
                </div>
            </div>

            <!-- Saldo de Gols -->
            <div class="card">
                <div class="card-label">SALDO DE GOLS
                    <span class="label-tag {{ $resumo['kpis']['saldo'] >= 0 ? 'tag-pos' : 'tag-neg' }}">GOLS</span>
                </div>
                <div class="kpi-value {{ $resumo['kpis']['saldo'] >= 0 ? 'text-win' : 'text-loss' }}">
                    {{ $resumo['kpis']['saldo'] > 0 ? '+' . $resumo['kpis']['saldo'] : $resumo['kpis']['saldo'] }}
                </div>
                <div class="kpi-sub">
                    <span class="gol-stat">{{ $resumo['kpis']['golsPro'] }} marcados</span>
                    <span class="gol-sep">·</span>
                    <span class="gol-stat">{{ $resumo['kpis']['golsContra'] }} sofridos</span>
                </div>
            </div>

        </section>

        <!-- ===== GRÁFICOS ===== -->
        <section class="charts-row">

            <!-- Evolução de pontos -->
            <div class="card chart-main-card">
                <div class="card-header-flex">
                    <h2>Evolução da Pontuação</h2>
                    <div class="chart-meta">
                        <span class="legend-dot red-dot"></span>
                        <span class="legend-label">Vitória</span>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="graficoEvolucao"></canvas>
                </div>
            </div>

            <!-- Coluna lateral: Donut + Forma -->
            <div class="charts-side">

                <!-- Donut resultados -->
                <div class="card">
                    <h2>Resultados</h2>
                    <div class="donut-wrapper">
                        <canvas id="graficoDonut"></canvas>
                        <div class="donut-center">
                            <span class="donut-pct">{{ $resumo['kpis']['aproveitamento'] }}%</span>
                            <span class="donut-label">aprov.</span>
                        </div>
                    </div>
                    <div class="donut-legend">
                        <div class="donut-item">
                            <span class="dot win-dot"></span>
                            <span>Vitórias</span>
                            <strong>{{ $resumo['kpis']['vitorias'] }}</strong>
                        </div>
                        <div class="donut-item">
                            <span class="dot draw-dot"></span>
                            <span>Empates</span>
                            <strong>{{ $resumo['kpis']['empates'] }}</strong>
                        </div>
                        <div class="donut-item">
                            <span class="dot loss-dot"></span>
                            <span>Derrotas</span>
                            <strong>{{ $resumo['kpis']['derrotas'] }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Forma recente + 2º Tempo -->
                <div class="card">
                    <div class="card-label">FORMA RECENTE</div>
                    <div class="forma-badges">
                        @forelse($resumo['forma_recente'] as $res)
                            <span class="forma-badge {{ strtolower($res) }}">{{ $res }}</span>
                        @empty
                            <span class="kpi-sub">Sem dados</span>
                        @endforelse
                    </div>

                    <div class="divider"></div>

                    <div class="card-label">GOLS NO 2º TEMPO</div>
                    <div class="segundo-tempo-row">
                        <span class="kpi-value-sm">{{ $resumo['segundo_tempo']['gols_2t'] }}</span>
                        <span class="kpi-sub">gols ({{ $resumo['segundo_tempo']['percentual_gols_2t'] }}% do total marcado)</span>
                    </div>
                </div>

            </div>
        </section>

        <!-- ===== MANDO DE CAMPO ===== -->
        <section class="mando-section">
            <div class="section-heading">
                <h2>Onde rende mais?</h2>
                <span class="section-sub">Desempenho por mando de campo</span>
            </div>

            <div class="mando-grid">

                <!-- Casa -->
                <div class="card mando-card">
                    <div class="mando-top">
                        <div class="mando-identity">
                            <img src="https://crests.football-data.org/1782.png"
                                 alt="Vitória"
                                 class="mando-logo"
                                 onerror="this.style.display='none'">
                            <div>
                                <div class="mando-label">Em casa</div>
                                <div class="mando-pct">{{ $resumo['mando']['casa']['aproveitamento'] }}%</div>
                            </div>
                        </div>
                        <div class="mando-pts-badge">{{ $resumo['mando']['casa']['pontos'] }} pts</div>
                    </div>

                    <div class="mando-stats">
                        <div class="mstat">
                            <span>Jogos</span>
                            <strong>{{ $resumo['mando']['casa']['jogos'] }}</strong>
                        </div>
                        <div class="mstat win">
                            <span>Vitórias</span>
                            <strong>{{ $resumo['mando']['casa']['vitorias'] }}</strong>
                        </div>
                        <div class="mstat draw">
                            <span>Empates</span>
                            <strong>{{ $resumo['mando']['casa']['empates'] }}</strong>
                        </div>
                        <div class="mstat loss">
                            <span>Derrotas</span>
                            <strong>{{ $resumo['mando']['casa']['derrotas'] }}</strong>
                        </div>
                        <div class="mstat">
                            <span>Gols pró</span>
                            <strong>{{ $resumo['mando']['casa']['golsPro'] }}</strong>
                        </div>
                        <div class="mstat">
                            <span>Gols contra</span>
                            <strong>{{ $resumo['mando']['casa']['golsContra'] }}</strong>
                        </div>
                        <div class="mstat">
                            <span>Saldo</span>
                            <strong class="{{ $resumo['mando']['casa']['saldo'] >= 0 ? 'text-win' : 'text-loss' }}">
                                {{ $resumo['mando']['casa']['saldo'] > 0 ? '+' . $resumo['mando']['casa']['saldo'] : $resumo['mando']['casa']['saldo'] }}
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- VS -->
                <div class="vs-divider">VS</div>

                <!-- Fora -->
                <div class="card mando-card">
                    <div class="mando-top">
                        <div class="mando-identity">
                            <img src="https://crests.football-data.org/1782.png"
                                 alt="Vitória"
                                 class="mando-logo mando-logo-away"
                                 onerror="this.style.display='none'">
                            <div>
                                <div class="mando-label">Como visitante</div>
                                <div class="mando-pct">{{ $resumo['mando']['fora']['aproveitamento'] }}%</div>
                            </div>
                        </div>
                        <div class="mando-pts-badge">{{ $resumo['mando']['fora']['pontos'] }} pts</div>
                    </div>

                    <div class="mando-stats">
                        <div class="mstat">
                            <span>Jogos</span>
                            <strong>{{ $resumo['mando']['fora']['jogos'] }}</strong>
                        </div>
                        <div class="mstat win">
                            <span>Vitórias</span>
                            <strong>{{ $resumo['mando']['fora']['vitorias'] }}</strong>
                        </div>
                        <div class="mstat draw">
                            <span>Empates</span>
                            <strong>{{ $resumo['mando']['fora']['empates'] }}</strong>
                        </div>
                        <div class="mstat loss">
                            <span>Derrotas</span>
                            <strong>{{ $resumo['mando']['fora']['derrotas'] }}</strong>
                        </div>
                        <div class="mstat">
                            <span>Gols pró</span>
                            <strong>{{ $resumo['mando']['fora']['golsPro'] }}</strong>
                        </div>
                        <div class="mstat">
                            <span>Gols contra</span>
                            <strong>{{ $resumo['mando']['fora']['golsContra'] }}</strong>
                        </div>
                        <div class="mstat">
                            <span>Saldo</span>
                            <strong class="{{ $resumo['mando']['fora']['saldo'] >= 0 ? 'text-win' : 'text-loss' }}">
                                {{ $resumo['mando']['fora']['saldo'] > 0 ? '+' . $resumo['mando']['fora']['saldo'] : $resumo['mando']['fora']['saldo'] }}
                            </strong>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ===== PRÓXIMAS PARTIDAS ===== -->
        @if($proximosJogos->count() > 0)
        <section class="card proximos-section">
            <h2>Próximas Partidas</h2>
            <div class="proximos-list">
                @foreach($proximosJogos as $jogo)
                <div class="proximo-item">
                    <span class="proximo-rodada">R{{ $jogo->rodada }}</span>
                    <div class="proximo-match">
                        <img src="https://crests.football-data.org/1782.png"
                             alt="Vitória"
                             class="proximo-crest"
                             onerror="this.style.display='none'">
                        <span class="proximo-vs">{{ $jogo->mando === 'CASA' ? 'vs' : 'em' }}</span>
                        @if($jogo->adversario_escudo)
                        <img src="{{ $jogo->adversario_escudo }}"
                             alt="{{ $jogo->adversario }}"
                             class="proximo-crest"
                             onerror="this.style.display='none'">
                        @endif
                        <span class="proximo-adversario">{{ $jogo->adversario }}</span>
                    </div>
                    <span class="badge-mando {{ strtolower($jogo->mando) }}">
                        {{ $jogo->mando === 'CASA' ? 'Casa' : 'Fora' }}
                    </span>
                    <span class="proximo-data">{{ $jogo->data_jogo->format('d/m · H:i') }}</span>
                </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- ===== CLASSIFICAÇÃO DO BRASILEIRÃO ===== -->
        <section class="card classificacao-section">
            <div class="card-header-flex">
                <div>
                    <h2>Classificação do Brasileirão</h2>
                    <span class="section-sub">Série A · temporada 2026</span>
                </div>
                <span class="classificacao-legenda"><span class="classificacao-destaque"></span> EC Vitória</span>
            </div>

            <div class="table-responsive">
                <table id="tabelaClassificacao" class="classificacao-table">
                    <thead>
                        <tr>
                            <th scope="col">Pos.</th>
                            <th scope="col">Clube</th>
                            <th scope="col" title="Pontos">Pts</th>
                            <th scope="col" title="Jogos">PJ</th>
                            <th scope="col" title="Vitórias">V</th>
                            <th scope="col" title="Empates">E</th>
                            <th scope="col" title="Derrotas">D</th>
                            <th scope="col" title="Gols pró">GP</th>
                            <th scope="col" title="Gols contra">GC</th>
                            <th scope="col" title="Saldo de gols">SG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classificacao as $time)
                        <tr class="{{ $time['is_vitoria'] ? 'classificacao-vitoria' : '' }}">
                            <td><strong class="classificacao-posicao">{{ $time['posicao'] }}</strong></td>
                            <td>
                                <div class="classificacao-clube">
                                    @if($time['escudo'])
                                    <img src="{{ $time['escudo'] }}"
                                         alt=""
                                         class="classificacao-escudo"
                                         loading="lazy"
                                         onerror="this.style.display='none'">
                                    @endif
                                    <span>{{ $time['nome'] }}</span>
                                </div>
                            </td>
                            <td><strong class="classificacao-pontos">{{ $time['pontos'] }}</strong></td>
                            <td>{{ $time['jogos'] }}</td>
                            <td>{{ $time['vitorias'] }}</td>
                            <td>{{ $time['empates'] }}</td>
                            <td>{{ $time['derrotas'] }}</td>
                            <td>{{ $time['gols_pro'] }}</td>
                            <td>{{ $time['gols_contra'] }}</td>
                            <td class="{{ $time['saldo'] > 0 ? 'text-win' : ($time['saldo'] < 0 ? 'text-loss' : '') }}">
                                {{ $time['saldo'] > 0 ? '+' . $time['saldo'] : $time['saldo'] }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="empty-state">Classificação indisponível.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ===== HISTÓRICO DE PARTIDAS ===== -->
        <section class="card table-section">
            <div class="card-header-flex">
                <h2>Histórico de Partidas</h2>
                <div class="tab-group">
                    <button class="tab active" data-filter="todos">Todos</button>
                    <button class="tab" data-filter="casa">Em casa</button>
                    <button class="tab" data-filter="fora">Fora</button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="tabelaJogos">
                    <thead>
                        <tr>
                            <th>Rod.</th>
                            <th>Data</th>
                            <th>Mando</th>
                            <th>Adversário</th>
                            <th>Placar</th>
                            <th>1º Tempo</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jogos as $jogo)
                        <tr data-mando="{{ strtolower($jogo->mando) }}">
                            <td><strong class="rodada-num">R{{ $jogo->rodada }}</strong></td>
                            <td class="text-muted">{{ $jogo->data_jogo->format('d/m') }}</td>
                            <td>
                                <span class="badge-mando {{ strtolower($jogo->mando) }}">
                                    {{ $jogo->mando === 'CASA' ? 'Casa' : 'Fora' }}
                                </span>
                            </td>
                            <td>
                                <div class="adversario-cell">
                                    @if($jogo->adversario_escudo)
                                    <img src="{{ $jogo->adversario_escudo }}"
                                         alt="{{ $jogo->adversario }}"
                                         class="crest-sm"
                                         onerror="this.style.display='none'">
                                    @endif
                                    <span>{{ $jogo->adversario }}</span>
                                </div>
                            </td>
                            <td class="placar-cell">
                                @if($jogo->status === 'FINISHED')
                                    <strong>
                                        {{ $jogo->mando === 'CASA' ? $jogo->gols_pro_total : $jogo->gols_contra_total }}
                                        ×
                                        {{ $jogo->mando === 'CASA' ? $jogo->gols_contra_total : $jogo->gols_pro_total }}
                                    </strong>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-muted">
                                @if($jogo->gols_pro_1t !== null)
                                    {{ $jogo->mando === 'CASA' ? $jogo->gols_pro_1t : $jogo->gols_contra_1t }}
                                    ×
                                    {{ $jogo->mando === 'CASA' ? $jogo->gols_contra_1t : $jogo->gols_pro_1t }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($jogo->resultado)
                                    <span class="badge-res {{ strtolower($jogo->resultado) }}">
                                        {{ $jogo->resultado === 'V' ? 'Vitória' : ($jogo->resultado === 'E' ? 'Empate' : 'Derrota') }}
                                    </span>
                                @else
                                    <span class="badge-res pendente">Agendado</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="empty-state">Nenhum jogo cadastrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <script>
        window.chartData = @json($resumo['grafico']);
        window.kpiData   = @json($resumo['kpis']);
    </script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
</body>

</html>
