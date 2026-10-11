<?php

namespace App\Adapters;

class FootballMatchAdapter
{
    public function adaptar(array $dados): array
    {
        $isHome = ($dados['homeTeam']['id'] == 1782);

        $mando = $isHome ? 'CASA' : 'FORA';
        $adversario = $isHome ? $dados['awayTeam']['shortName'] : $dados['homeTeam']['shortName'];
        $adversarioEscudo = $isHome ? ($dados['awayTeam']['crest'] ?? null) : ($dados['homeTeam']['crest'] ?? null);

        $status = $dados['status'];
        $golsPro1t = null;
        $golsContra1t = null;
        $golsProTotal = null;
        $golsContraTotal = null;
        $resultado = null;

        if (in_array($status, ['FINISHED', 'PAUSED']) && isset($dados['score']['fullTime']['home'])) {
            $golsHomeTotal = $dados['score']['fullTime']['home'];
            $golsAwayTotal = $dados['score']['fullTime']['away'];

            $golsProTotal = $isHome ? $golsHomeTotal : $golsAwayTotal;
            $golsContraTotal = $isHome ? $golsAwayTotal : $golsHomeTotal;

            $golsPro1t = $isHome ? $dados['score']['halfTime']['home'] : $dados['score']['halfTime']['away'];
            $golsContra1t = $isHome ? $dados['score']['halfTime']['away'] : $dados['score']['halfTime']['home'];

            if ($golsProTotal > $golsContraTotal) {
                $resultado = 'V';
            } elseif ($golsProTotal == $golsContraTotal) {
                $resultado = 'E';
            } else {
                $resultado = 'D';
            }
        }

        return [
            'match_id' => $dados['id'],
            'rodada' => $dados['matchday'],
            'adversario' => $adversario,
            'adversario_escudo' => $adversarioEscudo,
            'mando' => $mando,
            'gols_pro_1t' => $golsPro1t,
            'gols_contra_1t' => $golsContra1t,
            'gols_pro_total' => $golsProTotal,
            'gols_contra_total' => $golsContraTotal,
            'resultado' => $resultado,
            'status' => $status,
            'data_jogo' => $dados['utcDate'],
        ];
    }
}
