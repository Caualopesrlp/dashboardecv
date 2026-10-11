<?php

namespace App\Repositories;

use App\Models\Classificacao;
use Illuminate\Database\Eloquent\Collection;

class ClassificacaoRepository
{
    public function salvarOuAtualizarTime(array $dados): Classificacao
    {
        return Classificacao::updateOrCreate(
            ['time_id' => $dados['time_id']],
            [
                'posicao' => $dados['posicao'],
                'nome' => $dados['nome'],
                'escudo' => $dados['escudo'] ?? null,
                'pontos' => $dados['pontos'],
                'jogos' => $dados['jogos'],
                'vitorias' => $dados['vitorias'],
                'empates' => $dados['empates'],
                'derrotas' => $dados['derrotas'],
                'gols_pro' => $dados['gols_pro'],
                'gols_contra' => $dados['gols_contra'],
                'saldo' => $dados['saldo'],
            ]
        );
    }

    public function obterTabelaOrdenada(): Collection
    {
        return Classificacao::orderBy('posicao', 'asc')->get();
    }
}
