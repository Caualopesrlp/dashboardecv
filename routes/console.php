<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('jogos:coletar')->hourly()->withoutOverlapping();
Schedule::command('classificacao:coletar')->daily()->withoutOverlapping();
Schedule::command('confrontos:coletar')->hourly()->withoutOverlapping();
