document.addEventListener('DOMContentLoaded', function () {
    // 1. Verifica se os dados enviados pelo PHP/Blade existem
    if (!window.chartData || !window.chartData.labels) {
        console.warn('Dados do gráfico não encontrados ou em formato inválido.');
        return;
    }

    // 2. Captura o elemento canvas do HTML
    const ctx = document.getElementById('graficoEvolucao');
    if (!ctx) return;

    // 3. Estilização do gradiente sob a linha do gráfico
    const chartContext = ctx.getContext('2d');
    const gradient = chartContext.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(139, 0, 0, 0.4)'); // Vermelho topo
    gradient.addColorStop(1, 'rgba(139, 0, 0, 0.0)'); // Transparente base

    // 4. Instancia e configura o Chart.js
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: window.chartData.labels,
            datasets: [{
                label: 'Pontos Acumulados',
                data: window.chartData.pontos,
                borderColor: '#E50914',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.3, // Curva suave na linha
                pointBackgroundColor: '#FFFFFF',
                pointBorderColor: '#8B0000',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // Oculta a legenda padrão para visual mais limpo
                },
                tooltip: {
                    backgroundColor: '#1A1A1E',
                    titleColor: '#F1F1F1',
                    bodyColor: '#E50914',
                    borderColor: '#2D2D32',
                    borderWidth: 1,
                    padding: 10,
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
                    grid: {
                        color: '#2D2D32',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#A0A0A0',
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#2D2D32',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#A0A0A0',
                        precision: 0,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
});
