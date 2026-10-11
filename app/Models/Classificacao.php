<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classificacao extends Model
{
    protected $table = 'classificacaos';

    protected $fillable = [
        'posicao',
        'time_id',
        'nome',
        'escudo',
        'pontos',
        'jogos',
        'vitorias',
        'empates',
        'derrotas',
        'gols_pro',
        'gols_contra',
        'saldo',
    ];

    protected function casts(): array
    {
        return [
            'time_id' => 'integer',
        ];
    }
}
