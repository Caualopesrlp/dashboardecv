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
        $proximosJogos = $todosJogos->whereNotIn('status', ['FINISHED', 'PAUSED'])->sortBy('rodada')->take(5);

        $proximoJogo = $proximosJogos->first();

        return view('dashboard', [
            'resumo' => $this->desempenhoService->obterResumoDashboard(),
            'jogos' => $jogos,
            'proximosJogos' => $proximosJogos,
            'classificacao' => $this->classificacaoService->obterTabelaClassificacao(),
            'confronto' => $this->confrontoService->obterH2hProximoJogo($proximoJogo?->match_id),
        ]);
    }
}
