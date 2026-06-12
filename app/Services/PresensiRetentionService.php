<?php

namespace App\Services;

use App\Models\Presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PresensiRetentionService
{
    public const RETENTION_DAYS = 7;

    public static function earliestVisibleDate(): Carbon
    {
        return Carbon::today()->subDays(self::RETENTION_DAYS - 1);
    }

    public static function cleanupExpired(): int
    {
        $cutoff = self::earliestVisibleDate();
        $deleted = 0;

        Presensi::whereDate('tanggal', '<', $cutoff)->each(function (Presensi $presensi) use (&$deleted) {
            if ($presensi->bukti_foto && Storage::disk('public')->exists($presensi->bukti_foto)) {
                Storage::disk('public')->delete($presensi->bukti_foto);
            }
            $presensi->delete();
            $deleted++;
        });

        return $deleted;
    }
}
