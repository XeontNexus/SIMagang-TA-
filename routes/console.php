<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
Schedule::command('presensi:check-alpha')->dailyAt('23:59');
Schedule::command('presensi:cleanup-old')->dailyAt('00:05');
