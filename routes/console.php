<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Tandai siswa yang tidak presensi kemarin sebagai Alfa, berjalan tengah malam (WIB = UTC+7)
// Dijalankan pada 00:01 WIB = 17:01 UTC sehari sebelumnya
Schedule::command('presensi:mark-alpha')->dailyAt('00:01')->timezone('Asia/Jakarta');

// Cleanup presensi lama (>7 hari)
Schedule::command('presensi:cleanup-old')->dailyAt('00:05')->timezone('Asia/Jakarta');
