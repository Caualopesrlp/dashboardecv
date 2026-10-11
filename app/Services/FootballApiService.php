<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class FootballApiService
{
    private string $apiKey;

    private string $baseUrl;

    private int $vitoriaId;

    public function __construct()
    {
        $this->apiKey = config('services.football_data.api_key');
        $this->baseUrl = 'https://api.football-data.org/v4';
        $this->vitoriaId = (int) config('vitoria.api_team_id');
    }

    public function buscarPartidas(): array
    {
        $response = Http::withHeaders([
            'X-Auth-Token' => $this->apiKey,
        ])->get("{$this->baseUrl}/teams/{$this->vitoriaId}/matches");

        if ($response->failed()) {
            throw new Exception('Falha ao comunicar com a API do Football-Data: '.$response->status());
        }

        return $response->json()['matches'] ?? [];
    }

    public function buscarClassificacao(): array
    {
        $response = Http::withHeaders([
            'X-Auth-Token' => $this->apiKey,
        ])->get("{$this->baseUrl}/competitions/BSA/standings");

        if ($response->failed()) {
            throw new Exception('Falha ao comunicar com a API do Football-Data: '.$response->status());
        }

        return $response->json()['standings'][0]['table'] ?? [];
    }

    public function buscarConfrontoDireto(int $matchId): array
    {
        $response = Http::withHeaders([
            'X-Auth-Token' => $this->apiKey,
        ])->get("{$this->baseUrl}/matches/{$matchId}/head2head");

        if ($response->failed()) {
            throw new Exception('Falha ao comunicar com a API do Football-Data: '.$response->status());
        }

        return $response->json() ?? [];
    }
}
