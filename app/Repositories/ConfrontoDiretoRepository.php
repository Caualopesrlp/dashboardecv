<?php

namespace App\Repositories;

use App\Models\ConfrontoDireto;

class ConfrontoDiretoRepository
{
    public function salvarOuAtualizarH2h(array $dados)
    {
        ConfrontoDireto::updateOrCreate(
            ['match_id' => $dados['match_id']],

            [
                'adversario_nome' => $dados['adversario_nome'],
                'total_jogos' => $dados['total_jogos'],
                'vitorias' => $dados['vitorias'],
                'empates' => $dados['empates'],
                'derrotas' => $dados['derrotas'],
            ]
        );
    }

    public function obterPorMatchId(int $matchId): ?ConfrontoDireto
    {
        return ConfrontoDireto::where('match_id', $matchId)->first();
    }
}
