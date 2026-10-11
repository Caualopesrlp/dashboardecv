document.addEventListener('DOMContentLoaded', function () {

    // =========================================================================
    // 1. GRÁFICO DE LINHA — Evolução de Pontos
    // =========================================================================
    const ctxLine = document.getElementById('graficoEvolucao');

    if (ctxLine && window.chartData && window.chartData.labels) {
        const lineCtx = ctxLine.getContext('2d');

        const gradient = lineCtx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(204, 20, 24, 0.35)');
        gradient.addColorStop(1, 'rgba(204, 20, 24, 0.00)');

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: window.chartData.labels,
                datasets: [{
                    label: 'Pontos Acumulados',
                    data: window.chartData.pontos,
                    borderColor: '#E82028',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#E82028',
                    pointBorderColor: '#17171E',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#E82028',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E1E27',
                        titleColor: '#EBEBF0',
                        bodyColor: '#E82028',
                        borderColor: '#2C2C3A',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return `Pontuação: ${context.parsed.y} pts`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#1E1E27', drawBorder: false },
                        ticks: {
                            color: '#7A7A8A',
                            font: { size: 10 },
                            maxTicksLimit: 20,
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#1E1E27', drawBorder: false },
                        ticks: {
                            color: '#7A7A8A',
                            precision: 0,
                            font: { size: 10 },
                        }
                    }
                }
            }
        });
    } else {
        console.warn('Dados do gráfico de linha não encontrados.');
    }

    // =========================================================================
    // 2. GRÁFICO DONUT — Distribuição de Resultados
    // =========================================================================
    const ctxDonut = document.getElementById('graficoDonut');

    if (ctxDonut && window.kpiData) {
        const { vitorias, empates, derrotas } = window.kpiData;

        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Vitórias', 'Empates', 'Derrotas'],
                datasets: [{
                    data: [vitorias, empates, derrotas],
                    backgroundColor: [
                        'rgba(22, 163, 74, 0.85)',
                        'rgba(217, 119, 6, 0.85)',
                        'rgba(220, 38, 38, 0.85)',
                    ],
                    borderColor: '#17171E',
                    borderWidth: 3,
                    hoverOffset: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E1E27',
                        titleColor: '#EBEBF0',
                        bodyColor: '#7A7A8A',
                        borderColor: '#2C2C3A',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: true,
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${context.parsed} (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else {
        console.warn('Dados do donut não encontrados.');
    }

    // =========================================================================
    // 3. TABS — Filtro por mando na tabela de histórico
    // =========================================================================
    const tabs = document.querySelectorAll('.tab[data-filter]');
    const rows = document.querySelectorAll('#tabelaJogos tbody tr[data-mando]');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            // Atualiza estado visual das tabs
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;

            // Filtra linhas da tabela
            rows.forEach(row => {
                const show = filter === 'todos' || row.dataset.mando === filter;
                row.style.display = show ? '' : 'none';
            });
        });
    });

});
