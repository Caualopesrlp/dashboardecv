<?php

namespace App\Repositories;

use App\Models\Jogo;
use Illuminate\Database\Eloquent\Collection;

class JogoRepository
{
    private const STATUS_JOGOS_FUTUROS = ['SCHEDULED', 'TIMED'];

    public function salvarOuAtualizarPartida(array $dados): Jogo
    {
        return Jogo::updateOrCreate(
            ['match_id' => $dados['match_id']],
            [
                'rodada' => $dados['rodada'],
                'adversario' => $dados['adversario'],
                'adversario_escudo' => $dados['adversario_escudo'] ?? null,
                'mando' => $dados['mando'],
                'gols_pro_1t' => $dados['gols_pro_1t'] ?? null,
                'gols_contra_1t' => $dados['gols_contra_1t'] ?? null,
                'gols_pro_total' => $dados['gols_pro_total'] ?? null,
                'gols_contra_total' => $dados['gols_contra_total'] ?? null,
                'resultado' => $dados['resultado'] ?? null,
                'status' => $dados['status'],
                'data_jogo' => $dados['data_jogo'],
            ]
        );
    }

    public function obterJogosFinalizados(): Collection
    {
        return Jogo::whereIn('status', ['FINISHED', 'PAUSED'])
            ->whereNotNull('resultado')
            ->orderBy('rodada', 'asc')
            ->get();
    }

    public function obterTodosOsJogos(): Collection
    {
        return Jogo::orderBy('rodada', 'asc')->get();
    }

    public function obterProximosJogos(int $limite = 5): Collection
    {
        return Jogo::whereIn('status', self::STATUS_JOGOS_FUTUROS)
            ->orderBy('data_jogo', 'asc')
            ->orderBy('rodada', 'asc')
            ->limit($limite)
            ->get();
    }
}
