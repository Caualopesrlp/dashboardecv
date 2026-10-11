<?php

namespace App\Console\Commands;

use App\Adapters\FootballH2hAdapter;
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
        FootballH2hAdapter $adapter,
        JogoRepository $jogoRepository,
        ConfrontoDiretoRepository $confrontoRepository,
    ): int {
        try {
            $proximoJogo = $jogoRepository->obterProximosJogos(1)->first();

            if (! $proximoJogo) {
                $this->warn('Nenhum próximo jogo pendente encontrado para coletar H2H.');

                return self::SUCCESS;
            }

            $confrontoExistente = $confrontoRepository->obterPorMatchId($proximoJogo->match_id);

            if ($confrontoExistente) {
                $resultadosExistentes = (int) $confrontoExistente->vitorias
                    + (int) $confrontoExistente->empates
                    + (int) $confrontoExistente->derrotas;

                if ($resultadosExistentes === (int) $confrontoExistente->total_jogos) {
                    $this->info('O H2H do próximo jogo já está atualizado.');

                    return self::SUCCESS;
                }

                $this->warn('O H2H salvo não fecha com o total de partidas; consultando a API novamente.');
            }

            $this->info('Buscando o confronto direto do próximo jogo na API.');
            $dadosH2h = $service->buscarConfrontoDireto($proximoJogo->match_id);

            if (empty($dadosH2h)) {
                $this->error('A API não retornou dados de confronto direto.');

                return self::FAILURE;
            }

            $dadosAdaptados = $adapter->adaptar(
                $dadosH2h,
                (int) $proximoJogo->match_id,
                $proximoJogo->adversario,
                $proximoJogo->mando === 'CASA',
            );

            if ($dadosAdaptados === null) {
                $this->error('A API não retornou resultados de partidas recentes para o H2H.');

                return self::FAILURE;
            }

            $confrontoRepository->salvarOuAtualizarH2h($dadosAdaptados);

            $this->info(
                "Retrospecto dos últimos {$dadosAdaptados['total_jogos']} confrontos contra {$proximoJogo->adversario} atualizado com sucesso!"
            );
        } catch (\Throwable $exception) {
            $this->error('Não foi possível atualizar o H2H: '.$exception->getMessage());
            $this->warn('O histórico H2H já armazenado no banco foi mantido.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
