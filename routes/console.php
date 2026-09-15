<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// En fin de journée, une fois la dernière audience (16h) largement écoulée
Schedule::command('audiences:marquer-echues')->dailyAt('20:00');
