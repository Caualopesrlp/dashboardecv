<?php

namespace App\Repositories;

use App\Models\Jogo;

class JogoRepository
{
    public function salvarOuAtualizarPartida(array $dados): Jogo
    {
        return Jogo::updateOrCreate(
            ['match_id' => $dados['match_id']],
            [
                'rodada'            => $dados['rodada'],
                'adversario'        => $dados['adversario'],
                'adversario_escudo' => $dados['adversario_escudo'] ?? null,
                'mando'             => $dados['mando'],
                'gols_pro_1t'       => $dados['gols_pro_1t'] ?? null,
                'gols_contra_1t'    => $dados['gols_contra_1t'] ?? null,
                'gols_pro_total'    => $dados['gols_pro_total'] ?? null,
                'gols_contra_total' => $dados['gols_contra_total'] ?? null,
                'resultado'         => $dados['resultado'] ?? null,
                'status'            => $dados['status'],
                'data_jogo'         => $dados['data_jogo'],
            ]
        );
    }
}
