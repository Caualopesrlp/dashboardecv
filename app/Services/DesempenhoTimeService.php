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
            'kpis' => $this->calcularKpisGerais($jogos),
            'mando' => $this->calcularDesempenhoMando($jogos),
            'segundo_tempo' => $this->calcularAnalise2Tempo($jogos),
            'grafico' => $this->montarDadosGraficoEvolucao($jogos),
            'forma_recente' => $this->calcularFormaRecente($jogos),
        ];
    }

    private function calcularKpisGerais(Collection $jogos): array
    {
        $vitorias = $this->contarPorResultado($jogos, 'V');
        $empates = $this->contarPorResultado($jogos, 'E');
        $derrotas = $this->contarPorResultado($jogos, 'D');

        $quantidadejogos = $jogos->count();
        $pontos = $this->calcularPontos($vitorias, $empates);
        $aproveitamento = $this->calcularAproveitamento($pontos, $quantidadejogos);

        $golsPro = $jogos->sum('gols_pro_total');
        $golsContra = $jogos->sum('gols_contra_total');
        $saldo = $golsPro - $golsContra;

        $taxaVitoria = $quantidadejogos > 0 ? round(($vitorias / $quantidadejogos) * 100, 1) : 0.0;
        $mediaPontos = $quantidadejogos > 0 ? round($pontos / $quantidadejogos, 2) : 0.0;

        return [
            'vitorias' => $vitorias,
            'empates' => $empates,
            'derrotas' => $derrotas,
            'quantidadeJogos' => $quantidadejogos,
            'pontos' => $pontos,
            'aproveitamento' => round($aproveitamento, 1),
            'golsPro' => $golsPro,
            'golsContra' => $golsContra,
            'saldo' => $saldo,
            'taxaVitoria' => $taxaVitoria,
            'mediaPontos' => $mediaPontos,
        ];
    }

    private function calcularDesempenhoMando(Collection $jogos): array
    {
        $desempenhoCasa = $jogos->where('mando', 'CASA');
        $desempenhoFora = $jogos->where('mando', 'FORA');

        $vitoriasCasa = $this->contarPorResultado($desempenhoCasa, 'V');
        $empatesCasa = $this->contarPorResultado($desempenhoCasa, 'E');
        $derrotasCasa = $this->contarPorResultado($desempenhoCasa, 'D');
        $pontosCasa = $this->calcularPontos($vitoriasCasa, $empatesCasa);
        $aproveitamentoCasa = $this->calcularAproveitamento($pontosCasa, $desempenhoCasa->count());

        $golsProCasa = $desempenhoCasa->sum('gols_pro_total');
        $golsContraCasa = $desempenhoCasa->sum('gols_contra_total');

        $vitoriasFora = $this->contarPorResultado($desempenhoFora, 'V');
        $empatesFora = $this->contarPorResultado($desempenhoFora, 'E');
        $derrotasFora = $this->contarPorResultado($desempenhoFora, 'D');
        $pontosFora = $this->calcularPontos($vitoriasFora, $empatesFora);
        $aproveitamentoFora = $this->calcularAproveitamento($pontosFora, $desempenhoFora->count());

        $golsProFora = $desempenhoFora->sum('gols_pro_total');
        $golsContraFora = $desempenhoFora->sum('gols_contra_total');

        return [
            'casa' => [
                'jogos' => $desempenhoCasa->count(),
                'pontos' => $pontosCasa,
                'aproveitamento' => round($aproveitamentoCasa, 1),
                'vitorias' => $vitoriasCasa,
                'empates' => $empatesCasa,
                'derrotas' => $derrotasCasa,
                'golsPro' => $golsProCasa,
                'golsContra' => $golsContraCasa,
                'saldo' => $golsProCasa - $golsContraCasa,
            ],
            'fora' => [
                'jogos' => $desempenhoFora->count(),
                'pontos' => $pontosFora,
                'aproveitamento' => round($aproveitamentoFora, 1),
                'vitorias' => $vitoriasFora,
                'empates' => $empatesFora,
                'derrotas' => $derrotasFora,
                'golsPro' => $golsProFora,
                'golsContra' => $golsContraFora,
                'saldo' => $golsProFora - $golsContraFora,
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
            'gols_2t' => $gols2t,
            'percentual_gols_2t' => round($percentual2t, 1),
        ];
    }

    private function calcularFormaRecente(Collection $jogos): array
    {
        return $jogos
            ->values()
            ->slice(-5)
            ->pluck('resultado')
            ->values()
            ->toArray();
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
