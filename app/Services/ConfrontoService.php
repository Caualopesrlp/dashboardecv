<?php

namespace App\Services;

use App\Repositories\ConfrontoDiretoRepository;

class ConfrontoService
{
    private ConfrontoDiretoRepository $repository;

    public function __construct(ConfrontoDiretoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obterH2hProximoJogo(?int $matchId): ?array
    {
        if (! $matchId) {
            return null;
        }

        $confronto = $this->repository->obterPorMatchId($matchId);

        if (! $confronto) {
            return null;
        }

        return [
            'adversario_nome' => $confronto->adversario_nome,
            'total_jogos' => $confronto->total_jogos,
            'vitorias' => $confronto->vitorias,
            'empates' => $confronto->empates,
            'derrotas' => $confronto->derrotas,
        ];
    }
}
