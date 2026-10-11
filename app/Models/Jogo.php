<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jogo extends Model
{
    protected $fillable = [
        'match_id',
        'rodada',
        'adversario',
        'adversario_escudo',
        'mando',
        'gols_pro_1t',
        'gols_contra_1t',
        'gols_pro_total',
        'gols_contra_total',
        'resultado',
        'status',
        'data_jogo',
    ];

    protected function casts(): array
    {
        return [
            'data_jogo' => 'datetime',
        ];
    }
}
