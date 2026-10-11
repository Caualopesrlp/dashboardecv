<?php

namespace App\Console\Commands;

use App\Adapters\FootballMatchAdapter;
use App\Repositories\JogoRepository;
use App\Services\FootballApiService;
use Illuminate\Console\Command;

class ColetarJogos extends Command
{
    protected $signature = 'jogos:coletar';

    protected $description = 'Atualiza os jogos do Vitória no banco de dados.';

    public function handle(
        FootballApiService $service,
        FootballMatchAdapter $adapter,
        JogoRepository $repository,
    ): int {
        try {
            $this->info('Iniciando busca dos jogos na API.');
            $partidas = $service->buscarPartidas();

            foreach ($partidas as $partida) {
                $partidaAdaptada = $adapter->adaptar($partida);
                $repository->salvarOuAtualizarPartida($partidaAdaptada);
            }

            $this->info('Todos os jogos do Vitória foram atualizados com sucesso no banco!');
        } catch (\Exception $exception) {
            $this->error('Não foi possível atualizar os jogos via API: '.$exception->getMessage());
            $this->warn('Os dados de jogos já armazenados foram mantidos.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
