<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FootballApiService
{
    private string $apiKey;

    private string $baseUrl;

    private int $vitoriaId = 1782;

    public function __construct()
    {
        $this->apiKey = env('FOOTBALL_DATA_API_KEY');
        $this->baseUrl = 'https://api.football-data.org/v4';
    }

    public function buscarPartidas(): array
    {
        $response = Http::withHeaders([
            'X-Auth-Token' => $this->apiKey,
        ])->get("{$this->baseUrl}/teams/{$this->vitoriaId}/matches");

        if ($response->failed()) {
            throw new \Exception('Falha ao comunicar com a API do Football-Data: '.$response->status());
        }

        return $response->json()['matches'] ?? [];
    }
}
