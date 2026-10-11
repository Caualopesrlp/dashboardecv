<?php

namespace App\Http\Controllers;

use App\Repositories\JogoRepository;
use App\Services\DesempenhoTimeService;

class DashboardController extends Controller
{
    public function index(DesempenhoTimeService $service, JogoRepository $repository)
    {
        $resumo = $service->obterResumoDashboard();

        $todosJogos = $repository->obterTodosOsJogos();
        $jogos = $todosJogos->whereIn('status', ['FINISHED', 'PAUSED'])->sortByDesc('rodada');
        $proximosJogos = $todosJogos->whereNotIn('status', ['FINISHED', 'PAUSED'])->sortBy('rodada')->take(5);

        return view('dashboard', [
            'resumo' => $resumo,
            'jogos' => $jogos,
            'proximosJogos' => $proximosJogos,
        ]);
    }
}
