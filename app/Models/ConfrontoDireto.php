<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfrontoDireto extends Model
{
    protected $table = 'confronto_diretos';

    protected $fillable = [
        'match_id',
        'adversario_nome',
        'total_jogos',
        'vitorias',
        'empates',
        'derrotas',
    ];
}
