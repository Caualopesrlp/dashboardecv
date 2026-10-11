<?php

namespace App\Console\Commands;

use App\Repositories\ConfrontoDiretoRepository;
use App\Repositories\JogoRepository;
use App\Services\FootballApiService;
use Illuminate\Console\Command;

class ColetarConfrontoDireto extends Command
{
    protected $signature = 'confrontos:coletar';

    protected $description = 'Atualiza o histórico H2H do próximo jogo do Vitória.';

    public function handle(
        FootballApiService $service,
        JogoRepository $jogoRepository,
        ConfrontoDiretoRepository $confrontoRepository,
    ): int {
        try {
            $proximoJogo = $jogoRepository->obterTodosOsJogos()
                ->whereNotIn('status', ['FINISHED', 'PAUSED'])
                ->sortBy('rodada')
                ->first();

            if (! $proximoJogo) {
                $this->warn('Nenhum próximo jogo pendente encontrado para coletar H2H.');

                return self::SUCCESS;
            }

            if ($confrontoRepository->obterPorMatchId($proximoJogo->match_id)) {
                $this->info('O H2H do próximo jogo já está atualizado.');

                return self::SUCCESS;
            }

            $this->info('Buscando o confronto direto do próximo jogo na API.');
            $aggregates = $service->buscarConfrontoDireto($proximoJogo->match_id);

            if (empty($aggregates)) {
                $this->error('A API não retornou dados de confronto direto.');

                return self::FAILURE;
            }

            $isVitoriaHome = $proximoJogo->mando === 'CASA';
            $estatisticasVitoria = $isVitoriaHome
                ? ($aggregates['homeTeam'] ?? [])
                : ($aggregates['awayTeam'] ?? []);

            $confrontoRepository->salvarOuAtualizarH2h([
                'match_id' => $proximoJogo->match_id,
                'adversario_nome' => $proximoJogo->adversario,
                'total_jogos' => $aggregates['numberOfMatches'] ?? 0,
                'vitorias' => $estatisticasVitoria['wins'] ?? 0,
                'empates' => $aggregates['homeTeam']['draws'] ?? 0,
                'derrotas' => $estatisticasVitoria['losses'] ?? 0,
            ]);

            $this->info("Confronto direto contra {$proximoJogo->adversario} atualizado com sucesso!");
        } catch (\Throwable $exception) {
            $this->error('Não foi possível atualizar o H2H: '.$exception->getMessage());
            $this->warn('O histórico H2H já armazenado no banco foi mantido.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
