<?php

namespace App\Http\Controllers;

use App\Repositories\JogoRepository;
use App\Services\DesempenhoTimeService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(DesempenhoTimeService $service, JogoRepository $repository)
    {
        $resumo = $service->obterResumoDashboard();
        $jogos = $repository->obterTodosOsJogos();

        return view('dashboard', [
            'resumo' => $resumo,
            'jogos' => $jogos
        ]);
    }
}
