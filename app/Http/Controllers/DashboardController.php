<?php

namespace App\Http\Controllers;

use App\Repositories\JogoRepository;
use App\Services\ClassificacaoService;
use App\Services\DesempenhoTimeService;

class DashboardController extends Controller
{
    public function index(DesempenhoTimeService $service, JogoRepository $repository, ClassificacaoService $serviceClassificacao)
    {
        $resumo = $service->obterResumoDashboard();

        $todosJogos = $repository->obterTodosOsJogos();
        $jogos = $todosJogos->whereIn('status', ['FINISHED', 'PAUSED'])->sortByDesc('rodada');
        $proximosJogos = $todosJogos->whereNotIn('status', ['FINISHED', 'PAUSED'])->sortBy('rodada')->take(5);

        $classificacao = $serviceClassificacao->obterTabelaClassificacao();

        return view('dashboard', [
            'resumo' => $resumo,
            'jogos' => $jogos,
            'proximosJogos' => $proximosJogos,
            'classificacao' => $classificacao,
        ]);
    }
}
