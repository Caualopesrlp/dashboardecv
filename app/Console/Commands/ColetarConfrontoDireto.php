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

            $jogosRecentes = $dadosH2h['matches'] ?? [];
            $estatisticasVitoria = $this->obterResultadosRecentes($jogosRecentes);

            if ($estatisticasVitoria === null) {
                $estatisticasVitoria = $this->obterEstatisticasVitoria(
                    $dadosH2h['aggregates'] ?? [],
                    $proximoJogo->mando === 'CASA',
                );
            }

            $totalJogos = $estatisticasVitoria['vitorias']
                + $estatisticasVitoria['empates']
                + $estatisticasVitoria['derrotas'];

            if ($totalJogos === 0) {
                $this->error('A API não retornou resultados de partidas recentes para o H2H.');

                return self::FAILURE;
            }

            $confrontoRepository->salvarOuAtualizarH2h([
                'match_id' => $proximoJogo->match_id,
                'adversario_nome' => $proximoJogo->adversario,
                'total_jogos' => $totalJogos,
                'vitorias' => $estatisticasVitoria['vitorias'],
                'empates' => $estatisticasVitoria['empates'],
                'derrotas' => $estatisticasVitoria['derrotas'],
            ]);

            $this->info(
                "Retrospecto dos últimos {$totalJogos} confrontos contra {$proximoJogo->adversario} atualizado com sucesso!"
            );
        } catch (\Throwable $exception) {
            $this->error('Não foi possível atualizar o H2H: '.$exception->getMessage());
            $this->warn('O histórico H2H já armazenado no banco foi mantido.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function obterResultadosRecentes(array $jogos): ?array
    {
        $estatisticas = [
            'vitorias' => 0,
            'empates' => 0,
            'derrotas' => 0,
        ];
        $partidasContabilizadas = 0;

        foreach ($jogos as $jogo) {
            if (! in_array($jogo['status'] ?? null, ['FINISHED', 'AWARDED'], true)) {
                continue;
            }

            $idMandante = (int) ($jogo['homeTeam']['id'] ?? 0);
            $idVisitante = (int) ($jogo['awayTeam']['id'] ?? 0);
            $golsMandante = $jogo['score']['fullTime']['home'] ?? null;
            $golsVisitante = $jogo['score']['fullTime']['away'] ?? null;

            if (
                ! in_array(1782, [$idMandante, $idVisitante], true)
                || ! is_numeric($golsMandante)
                || ! is_numeric($golsVisitante)
            ) {
                continue;
            }

            $vitoriaEmCasa = $idMandante === 1782;
            $golsVitoria = (int) ($vitoriaEmCasa ? $golsMandante : $golsVisitante);
            $golsAdversario = (int) ($vitoriaEmCasa ? $golsVisitante : $golsMandante);

            if ($golsVitoria > $golsAdversario) {
                $estatisticas['vitorias']++;
            } elseif ($golsVitoria === $golsAdversario) {
                $estatisticas['empates']++;
            } else {
                $estatisticas['derrotas']++;
            }

            $partidasContabilizadas++;
        }

        return $partidasContabilizadas > 0 ? $estatisticas : null;
    }

    private function obterEstatisticasVitoria(array $aggregates, bool $vitoriaEmCasa): array
    {
        $homeTeam = $aggregates['homeTeam'] ?? [];
        $awayTeam = $aggregates['awayTeam'] ?? [];

        if ((int) ($homeTeam['id'] ?? 0) === 1782) {
            $estatisticas = $homeTeam;
        } elseif ((int) ($awayTeam['id'] ?? 0) === 1782) {
            $estatisticas = $awayTeam;
        } else {
            $estatisticas = $vitoriaEmCasa ? $homeTeam : $awayTeam;
        }

        return [
            'vitorias' => (int) ($estatisticas['wins'] ?? 0),
            'empates' => (int) ($estatisticas['draws'] ?? 0),
            'derrotas' => (int) ($estatisticas['losses'] ?? 0),
        ];
    }
}
