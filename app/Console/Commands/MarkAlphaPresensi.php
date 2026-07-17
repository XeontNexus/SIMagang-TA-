<?php

namespace App\Console\Commands;

use App\Models\Presensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkAlphaPresensi extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'presensi:mark-alpha
                            {--date= : Tanggal target (Y-m-d). Default: kemarin.}
                            {--dry-run : Tampilkan daftar siswa yang akan di-alfa tanpa menyimpan.}';

    /**
     * The console command description.
     */
    protected $description = 'Tandai siswa aktif yang tidak presensi pada hari target sebagai Alfa.';

    public function handle(): int
    {
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : Carbon::yesterday()->startOfDay();

        $isDryRun = $this->option('dry-run');

        $this->info("Target tanggal: {$targetDate->format('d/m/Y')}");
        $this->info($isDryRun ? '[DRY-RUN mode — tidak ada perubahan yang disimpan]' : 'Mode: simpan ke database');

        // Ambil semua siswa aktif (status aktif atau proses)
        $siswaAktif = User::where('role', 'siswa')
            ->whereIn('status', ['aktif', 'proses', 'active'])
            ->pluck('id');

        if ($siswaAktif->isEmpty()) {
            $this->warn('Tidak ada siswa aktif ditemukan.');
            return self::SUCCESS;
        }

        // Cari user_id yang SUDAH presensi di tanggal target
        $sudahPresensiIds = Presensi::whereDate('tanggal', $targetDate)
            ->whereIn('user_id', $siswaAktif)
            ->pluck('user_id');

        // Siswa yang BELUM presensi
        $belumPresensiIds = $siswaAktif->diff($sudahPresensiIds);

        if ($belumPresensiIds->isEmpty()) {
            $this->info('Semua siswa aktif sudah presensi pada tanggal tersebut. Tidak ada yang perlu di-alfa.');
            return self::SUCCESS;
        }

        $belumSiswa = User::whereIn('id', $belumPresensiIds)
            ->orderBy('nama_lengkap')
            ->get(['id', 'nama_lengkap', 'status']);

        $this->table(
            ['ID', 'Nama Lengkap', 'Status Akun'],
            $belumSiswa->map(fn ($s) => [$s->id, $s->nama_lengkap, $s->status])
        );

        if ($isDryRun) {
            $this->warn("DRY-RUN: {$belumSiswa->count()} siswa AKAN ditandai alfa — tidak ada yang disimpan.");
            return self::SUCCESS;
        }

        // Buat record alfa untuk setiap siswa yang belum presensi
        $count = 0;
        foreach ($belumPresensiIds as $userId) {
            // Hindari duplikat (meskipun sudah dicek di atas, double-check aman)
            $exists = Presensi::where('user_id', $userId)
                ->whereDate('tanggal', $targetDate)
                ->exists();

            if (!$exists) {
                Presensi::create([
                    'user_id'    => $userId,
                    'tanggal'    => $targetDate->format('Y-m-d'),
                    'status'     => 'alfa',
                    'keterangan' => 'Alfa otomatis — tidak presensi pada hari ini.',
                    'jam_masuk'  => null,
                    'jam_keluar' => null,
                ]);
                $count++;
            }
        }

        $this->info("✅ {$count} siswa berhasil ditandai Alfa untuk tanggal {$targetDate->format('d/m/Y')}.");
        return self::SUCCESS;
    }
}
