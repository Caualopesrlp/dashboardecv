<?php

namespace App\Console\Commands;

use App\Repositories\JogoRepository;
use App\Services\FootballApiService;
use App\Adapters\FootballMatchAdapter;
use Illuminate\Console\Command;

class ColetarJogos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jogos:coletar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        FootballApiService $service,
        FootballMatchAdapter $adapter,
        JogoRepository $repository,
    ) {
        try {
            $this->info('Iniciando busca dos jogos na API.');
            $partidas = $service->buscarPartidas();

            foreach ($partidas as $partida) {
                $partidasAdaptadas = $adapter->adaptar($partida);
                $repository->salvarOuAtualizarPartida($partidasAdaptadas);
            }

            $this->info('Todos os jogos do Vitória foram atualizados com sucesso no banco!');
        } catch (\Exception $e) {
            $this->error('Aviso: Não foi possível atualizar os dados via API. Erro: ' . $e->getMessage());
            $this->warn('O banco de dados manteve a versão anterior dos jogos com segurança.');
        }

        return 0;
    }
}
