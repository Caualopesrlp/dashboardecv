<?php

namespace App\Adapters;

class FootballH2hAdapter
{
    private int $vitoriaTeamId;

    public function __construct(?int $vitoriaTeamId = null)
    {
        $this->vitoriaTeamId = $vitoriaTeamId ?? (int) config('vitoria.api_team_id');
    }

    public function adaptar(
        array $dadosH2h,
        int $matchId,
        string $adversarioNome,
        bool $vitoriaEmCasa,
    ): ?array {
        $estatisticas = $this->obterResultadosRecentes($dadosH2h['matches'] ?? []);

        if ($estatisticas === null) {
            $estatisticas = $this->obterEstatisticasAgregadas(
                $dadosH2h['aggregates'] ?? [],
                $vitoriaEmCasa,
            );
        }

        $totalJogos = $estatisticas['vitorias']
            + $estatisticas['empates']
            + $estatisticas['derrotas'];

        if ($totalJogos === 0) {
            return null;
        }

        return [
            'match_id' => $matchId,
            'adversario_nome' => $adversarioNome,
            'total_jogos' => $totalJogos,
            'vitorias' => $estatisticas['vitorias'],
            'empates' => $estatisticas['empates'],
            'derrotas' => $estatisticas['derrotas'],
        ];
    }

    private function obterResultadosRecentes(array $jogos): ?array
    {
        $estatisticas = [
            'vitorias' => 0,
            'empates' => 0,
            'derrotas' => 0,
        ];
        $partidasContabilizadas = 0;

        foreach ($jogos as $jogo) {
            if (! is_array($jogo) || ! in_array($jogo['status'] ?? null, ['FINISHED', 'AWARDED'], true)) {
                continue;
            }

            $idMandante = (int) ($jogo['homeTeam']['id'] ?? 0);
            $idVisitante = (int) ($jogo['awayTeam']['id'] ?? 0);
            $golsMandante = $jogo['score']['fullTime']['home'] ?? null;
            $golsVisitante = $jogo['score']['fullTime']['away'] ?? null;

            if (
                ! in_array($this->vitoriaTeamId, [$idMandante, $idVisitante], true)
                || ! is_numeric($golsMandante)
                || ! is_numeric($golsVisitante)
            ) {
                continue;
            }

            $vitoriaEmCasa = $idMandante === $this->vitoriaTeamId;
            $golsVitoria = (int) ($vitoriaEmCasa ? $golsMandante : $golsVisitante);
            $golsAdversario = (int) ($vitoriaEmCasa ? $golsVisitante : $golsMandante);

            if ($golsVitoria > $golsAdversario) {
                $estatisticas['vitorias']++;
            } elseif ($golsVitoria === $golsAdversario) {
                $estatisticas['empates']++;
            } else {
                $estatisticas['derrotas']++;
            }

            $partidasContabilizadas++;
        }

        return $partidasContabilizadas > 0 ? $estatisticas : null;
    }

    private function obterEstatisticasAgregadas(array $aggregates, bool $vitoriaEmCasa): array
    {
        $homeTeam = is_array($aggregates['homeTeam'] ?? null) ? $aggregates['homeTeam'] : [];
        $awayTeam = is_array($aggregates['awayTeam'] ?? null) ? $aggregates['awayTeam'] : [];

        if ((int) ($homeTeam['id'] ?? 0) === $this->vitoriaTeamId) {
            $estatisticas = $homeTeam;
        } elseif ((int) ($awayTeam['id'] ?? 0) === $this->vitoriaTeamId) {
            $estatisticas = $awayTeam;
        } else {
            $estatisticas = $vitoriaEmCasa ? $homeTeam : $awayTeam;
        }

        return [
            'vitorias' => (int) ($estatisticas['wins'] ?? 0),
            'empates' => (int) ($estatisticas['draws'] ?? 0),
            'derrotas' => (int) ($estatisticas['losses'] ?? 0),
        ];
    }
}
