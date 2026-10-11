<?php

namespace App\Http\Controllers;

use App\Repositories\JogoRepository;
use App\Services\ClassificacaoService;
use App\Services\ConfrontoService;
use App\Services\DesempenhoTimeService;

class DashboardController extends Controller
{
    public function __construct(
        private DesempenhoTimeService $desempenhoService,
        private JogoRepository $jogoRepository,
        private ClassificacaoService $classificacaoService,
        private ConfrontoService $confrontoService
    ) {}

    public function index()
    {
        $todosJogos = $this->jogoRepository->obterTodosOsJogos();

        $jogos = $todosJogos->whereIn('status', ['FINISHED', 'PAUSED'])->sortByDesc('rodada');
        $proximosJogos = $this->jogoRepository->obterProximosJogos();
        $temporada = now('America/Bahia')->year;

        $proximoJogo = $proximosJogos->first();
        $confronto = $this->confrontoService->obterH2hProximoJogo($proximoJogo?->match_id);

        if ($confronto && $proximoJogo) {
            $confronto['adversario_escudo'] = $proximoJogo->adversario_escudo;
        }

        return view('dashboard', [
            'resumo' => $this->desempenhoService->obterResumoDashboard(),
            'jogos' => $jogos,
            'proximosJogos' => $proximosJogos,
            'temporada' => $temporada,
            'classificacao' => $this->classificacaoService->obterTabelaClassificacao(),
            'confronto' => $confronto,
        ]);
    }
}
