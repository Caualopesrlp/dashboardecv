<?php

namespace Tests\Unit;

use App\Adapters\FootballH2hAdapter;
use PHPUnit\Framework\TestCase;

class FootballH2hAdapterTest extends TestCase
{
    public function test_adapts_recent_finished_matches_from_vitoria_perspective(): void
    {
        $adapter = new FootballH2hAdapter(1782);
        $dadosH2h = [
            'matches' => [
                $this->partida('FINISHED', 1782, 10, 2, 0),
                $this->partida('FINISHED', 10, 1782, 1, 1),
                $this->partida('FINISHED', 10, 1782, 2, 1),
                $this->partida('TIMED', 1782, 10, 1, 0),
                $this->partida('FINISHED', 20, 30, 3, 0),
            ],
        ];

        $dadosAdaptados = $adapter->adaptar($dadosH2h, 12345, 'Flamengo', false);

        $this->assertSame([
            'match_id' => 12345,
            'adversario_nome' => 'Flamengo',
            'total_jogos' => 3,
            'vitorias' => 1,
            'empates' => 1,
            'derrotas' => 1,
        ], $dadosAdaptados);
    }

    public function test_falls_back_to_team_aggregates_when_recent_matches_are_unavailable(): void
    {
        $adapter = new FootballH2hAdapter(1782);
        $dadosH2h = [
            'aggregates' => [
                'homeTeam' => [
                    'id' => 1782,
                    'wins' => 4,
                    'draws' => 2,
                    'losses' => 3,
                ],
                'awayTeam' => [
                    'id' => 10,
                    'wins' => 3,
                    'draws' => 2,
                    'losses' => 4,
                ],
            ],
        ];

        $dadosAdaptados = $adapter->adaptar($dadosH2h, 12345, 'Flamengo', true);

        $this->assertSame([
            'match_id' => 12345,
            'adversario_nome' => 'Flamengo',
            'total_jogos' => 9,
            'vitorias' => 4,
            'empates' => 2,
            'derrotas' => 3,
        ], $dadosAdaptados);
    }

    public function test_returns_null_when_the_api_has_no_usable_h2h_results(): void
    {
        $adapter = new FootballH2hAdapter(1782);

        $this->assertNull($adapter->adaptar([], 12345, 'Flamengo', false));
    }

    private function partida(
        string $status,
        int $idMandante,
        int $idVisitante,
        int $golsMandante,
        int $golsVisitante,
    ): array {
        return [
            'status' => $status,
            'homeTeam' => ['id' => $idMandante],
            'awayTeam' => ['id' => $idVisitante],
            'score' => [
                'fullTime' => [
                    'home' => $golsMandante,
                    'away' => $golsVisitante,
                ],
            ],
        ];
    }
}
