<?php

namespace App\Services;

use App\Repositories\JogoRepository;
use Illuminate\Database\Eloquent\Collection;

class DesempenhoTimeService
{
    protected JogoRepository $repository;

    public function __construct(JogoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obterResumoDashboard(): array
    {
        $jogos = $this->repository->obterJogosFinalizados();

        return [
            'kpis'          => $this->calcularKpisGerais($jogos),
            'mando'         => $this->calcularDesempenhoMando($jogos),
            'segundo_tempo' => $this->calcularAnalise2Tempo($jogos),
            'grafico'       => $this->montarDadosGraficoEvolucao($jogos),
        ];
    }

    private function calcularKpisGerais(Collection $jogos): array
    {
        $vitorias = $this->contarPorResultado($jogos, 'V');
        $empates  = $this->contarPorResultado($jogos, 'E');
        $derrotas = $this->contarPorResultado($jogos, 'D');

        $quantidadejogos = $jogos->count();

        $pontos         = $this->calcularPontos($vitorias, $empates);
        $aproveitamento = $this->calcularAproveitamento($pontos, $quantidadejogos);

        $golsPro    = $jogos->sum('gols_pro_total');
        $golsContra = $jogos->sum('gols_contra_total');
        $saldo      = $golsPro - $golsContra;

        return [
            'vitorias'        => $vitorias,
            'empates'         => $empates,
            'derrotas'        => $derrotas,
            'quantidadeJogos' => $quantidadejogos,
            'pontos'          => $pontos,
            'aproveitamento'  => round($aproveitamento, 1),
            'golsPro'         => $golsPro,
            'golsContra'      => $golsContra,
            'saldo'           => $saldo,
        ];
    }

    private function calcularDesempenhoMando(Collection $jogos): array
    {
        $desempenhoCasa = $jogos->where('mando', 'CASA');
        $desempenhoFora = $jogos->where('mando', 'FORA');

        $vitoriasCasa       = $this->contarPorResultado($desempenhoCasa, 'V');
        $empatesCasa        = $this->contarPorResultado($desempenhoCasa, 'E');
        $pontosCasa         = $this->calcularPontos($vitoriasCasa, $empatesCasa);
        $aproveitamentoCasa = $this->calcularAproveitamento($pontosCasa, $desempenhoCasa->count());

        $vitoriasFora       = $this->contarPorResultado($desempenhoFora, 'V');
        $empatesFora        = $this->contarPorResultado($desempenhoFora, 'E');
        $pontosFora         = $this->calcularPontos($vitoriasFora, $empatesFora);
        $aproveitamentoFora = $this->calcularAproveitamento($pontosFora, $desempenhoFora->count());

        return [
            'casa' => [
                'jogos'          => $desempenhoCasa->count(),
                'pontos'         => $pontosCasa,
                'aproveitamento' => round($aproveitamentoCasa, 1),
            ],
            'fora' => [
                'jogos'          => $desempenhoFora->count(),
                'pontos'         => $pontosFora,
                'aproveitamento' => round($aproveitamentoFora, 1),
            ],
        ];
    }

    private function calcularAnalise2Tempo(Collection $jogos): array
    {
        $golsProTotal = $jogos->sum('gols_pro_total');

        $gols2t = $jogos->sum(function ($jogo) {
            return $jogo->gols_pro_total - $jogo->gols_pro_1t;
        });

        $percentual2t = $golsProTotal > 0
            ? ($gols2t / $golsProTotal) * 100
            : 0.0;

        return [
            'gols_2t'            => $gols2t,
            'percentual_gols_2t' => round($percentual2t, 1),
        ];
    }

    private function montarDadosGraficoEvolucao(Collection $jogos): array
    {
        $labels = [];
        $pontosAcumulados = [];
        $pontuacaoAtual = 0;

        foreach ($jogos as $jogo) {
            if ($jogo->resultado === 'V') {
                $pontuacaoAtual += 3;
            } elseif ($jogo->resultado === 'E') {
                $pontuacaoAtual += 1;
            }

            $labels[] = "R{$jogo->rodada}";
            $pontosAcumulados[] = $pontuacaoAtual;
        }

        return [
            'labels' => $labels,
            'pontos' => $pontosAcumulados,
        ];
    }

    private function contarPorResultado(Collection $jogos, string $tipo): int
    {
        return $jogos->where('resultado', $tipo)->count();
    }

    private function calcularPontos(int $vitorias, int $empates): int
    {
        return ($vitorias * 3) + $empates;
    }

    private function calcularAproveitamento(int $pontos, int $jogosDisputados): float
    {
        if ($jogosDisputados <= 0) {
            return 0.0;
        }

        return ($pontos / ($jogosDisputados * 3)) * 100;
    }
}
