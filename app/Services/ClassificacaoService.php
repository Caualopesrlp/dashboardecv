<?php

namespace App\Services;

use App\Repositories\ClassificacaoRepository;

class ClassificacaoService
{
    private ClassificacaoRepository $repository;

    public function __construct(ClassificacaoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function obterTabelaClassificacao(): array
    {
        return $this->repository->obterTabelaOrdenada()->map(function ($time) {
            return [
                'posicao' => $time->posicao,
                'time_id' => $time->time_id,
                'nome' => $time->nome,
                'escudo' => $time->escudo,
                'pontos' => $time->pontos,
                'jogos' => $time->jogos,
                'vitorias' => $time->vitorias,
                'empates' => $time->empates,
                'derrotas' => $time->derrotas,
                'gols_pro' => $time->gols_pro,
                'gols_contra' => $time->gols_contra,
                'saldo' => $time->saldo,
                'is_vitoria' => (int) $time->time_id === (int) config('vitoria.api_team_id'),
            ];
        })->all();
    }
}
