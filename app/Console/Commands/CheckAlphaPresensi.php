<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Presensi;
use Carbon\Carbon;

class CheckAlphaPresensi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:check-alpha';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek dan set status alpha untuk siswa yang tidak presensi hari ini';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        // Ambil semua siswa aktif
        $students = User::where('role', 'siswa')->where('status', 'aktif')->get();
        
        $count = 0;
        foreach ($students as $student) {
            // Cek apakah siswa tersebut sudah memiliki data presensi hari ini
            $hasPresensi = Presensi::where('user_id', $student->id)
                                   ->whereDate('tanggal', $today)
                                   ->exists();
                                   
            if (!$hasPresensi) {
                // Buat data presensi dengan status alpha
                Presensi::create([
                    'user_id' => $student->id,
                    'tanggal' => $today,
                    'status' => 'alpha',
                    'keterangan' => 'Tidak melakukan presensi (Otomatis Alpha)',
                ]);
                $count++;
            }
        }
        
        $this->info("Berhasil menandai {$count} siswa sebagai Alpha untuk tanggal {$today}");
    }
}
