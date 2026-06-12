<?php

namespace App\Console\Commands;

use App\Services\PresensiRetentionService;
use Illuminate\Console\Command;

class CleanupOldPresensi extends Command
{
    protected $signature = 'presensi:cleanup-old';

    protected $description = 'Hapus data presensi lebih dari 7 hari beserta file bukti foto';

    public function handle(): int
    {
        $deleted = PresensiRetentionService::cleanupExpired();
        $cutoff = PresensiRetentionService::earliestVisibleDate();

        $this->info("Berhasil menghapus {$deleted} data presensi sebelum {$cutoff->format('d/m/Y')}.");

        return self::SUCCESS;
    }
}
