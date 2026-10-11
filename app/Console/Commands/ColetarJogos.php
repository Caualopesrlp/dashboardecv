<?php

namespace App\Console\Commands;

use App\Adapters\FootballMatchAdapter;
use App\Repositories\ClassificacaoRepository;
use App\Repositories\JogoRepository;
use App\Services\FootballApiService;
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
    protected $description = 'Atualiza os jogos do Vitória e a classificação do Brasileirão.';

    /**
     * Execute the console command.
     */
    public function handle(
        FootballApiService $service,
        FootballMatchAdapter $adapter,
        JogoRepository $repository,
        ClassificacaoRepository $classificacaoRepository,
    ): int {
        try {
            $this->info('Iniciando busca dos jogos na API.');
            $partidas = $service->buscarPartidas();

            foreach ($partidas as $partida) {
                $partidasAdaptadas = $adapter->adaptar($partida);
                $repository->salvarOuAtualizarPartida($partidasAdaptadas);
            }

            $this->info('Todos os jogos do Vitória foram atualizados com sucesso no banco!');
        } catch (\Exception $e) {
            $this->error('Aviso: Não foi possível atualizar os dados via API. Erro: '.$e->getMessage());
            $this->warn('O banco de dados manteve a versão anterior dos jogos com segurança.');
        }

        try {
            $this->info('Iniciando busca da classificação na API.');
            $tabela = $service->buscarClassificacao();

            foreach ($tabela as $item) {
                $classificacaoRepository->salvarOuAtualizarTime([
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

            $this->info('Classificação do Brasileirão atualizada com sucesso no banco!');
        } catch (\Exception $e) {
            $this->error('Aviso: Não foi possível atualizar a classificação via API. Erro: '.$e->getMessage());
            $this->warn('A classificação já armazenada no banco foi mantida.');
        }

        return 0;
    }
}
