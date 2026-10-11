<?php

namespace App\Console\Commands;

use App\Repositories\ClassificacaoRepository;
use App\Services\FootballApiService;
use Illuminate\Console\Command;

class ColetarClassificacao extends Command
{
    protected $signature = 'classificacao:coletar';

    protected $description = 'Atualiza a classificação do Brasileirão no banco de dados.';

    public function handle(
        FootballApiService $service,
        ClassificacaoRepository $repository,
    ): int {
        try {
            $this->info('Iniciando busca da classificação na API.');
            $tabela = $service->buscarClassificacao();

            foreach ($tabela as $item) {
                $repository->salvarOuAtualizarTime([
                    'posicao' => $item['position'],
                    'time_id' => $item['team']['id'],
                    'nome' => $item['team']['shortName'] ?? $item['team']['name'],
                    'escudo' => $item['team']['crest'] ?? null,
                    'pontos' => $item['points'],
                    'jogos' => $item['playedGames'],
                    'vitorias' => $item['won'],
                    'empates' => $item['draw'],
                    'derrotas' => $item['lost'],
                    'gols_pro' => $item['goalsFor'],
                    'gols_contra' => $item['goalsAgainst'],
                    'saldo' => $item['goalDifference'],
                ]);
            }

            $this->info('Classificação do Brasileirão atualizada com sucesso!');
        } catch (\Throwable $exception) {
            $this->error('Não foi possível atualizar a classificação: '.$exception->getMessage());
            $this->warn('A classificação já armazenada no banco foi mantida.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
