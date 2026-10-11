<?php

namespace Tests\Feature;

use App\Models\Jogo;
use App\Repositories\JogoRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JogoRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_only_future_statuses_ordered_by_match_datetime(): void
    {
        Jogo::create($this->dadosJogo(1001, 8, 'TIMED', '2026-11-20 20:00:00'));
        Jogo::create($this->dadosJogo(1002, 9, 'SCHEDULED', '2026-10-15 20:00:00'));
        Jogo::create($this->dadosJogo(1003, 1, 'POSTPONED', '2026-10-12 20:00:00'));
        Jogo::create($this->dadosJogo(1004, 2, 'CANCELLED', '2026-10-13 20:00:00'));

        $proximosJogos = app(JogoRepository::class)->obterProximosJogos();

        $this->assertSame([1002, 1001], $proximosJogos->pluck('match_id')->all());
    }

    private function dadosJogo(int $matchId, int $rodada, string $status, string $dataJogo): array
    {
        return [
            'match_id' => $matchId,
            'rodada' => $rodada,
            'adversario' => 'Adversário',
            'adversario_escudo' => null,
            'mando' => 'CASA',
            'gols_pro_1t' => null,
            'gols_contra_1t' => null,
            'gols_pro_total' => null,
            'gols_contra_total' => null,
            'resultado' => null,
            'status' => $status,
            'data_jogo' => $dataJogo,
        ];
    }
}
