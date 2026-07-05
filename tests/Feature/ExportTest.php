<?php

namespace Tests\Feature;

use App\Models\GuruPembimbing;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\User;
use App\Models\Logbook;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_all_students_excel()
    {
        $admin = User::create([
            'username' => 'admin_test',
            'email' => 'admin_test@simagang.local',
            'nama_lengkap' => 'Admin Test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $kelas = Kelas::create([
            'tingkat' => 'XII',
            'nama_kelas' => 'RPL 1',
            'jurusan_id' => $jurusan->id,
        ]);

        $guru = GuruPembimbing::create([
            'nama_guru' => 'Pak Budi',
            'status' => 'active',
        ]);

        $student = User::create([
            'username' => 'siswa_test',
            'email' => 'siswa_test@simagang.local',
            'nama_lengkap' => 'Siswa Test',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'kelas_id' => $kelas->id,
            'jurusan_id' => $jurusan->id,
            'guru_pembimbing_id' => $guru->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.export.excel'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_export_by_guru()
    {
        $admin = User::create([
            'username' => 'admin_test',
            'email' => 'admin_test@simagang.local',
            'nama_lengkap' => 'Admin Test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $guru = GuruPembimbing::create([
            'nama_guru' => 'Bu Ani: Guru', // Has a colon to test title sanitization
            'status' => 'active',
        ]);

        $student = User::create([
            'username' => 'siswa_test',
            'email' => 'siswa_test@simagang.local',
            'nama_lengkap' => 'Siswa Test',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'guru_pembimbing_id' => $guru->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.export.guru', ['guru_pembimbing_id' => $guru->id]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_export_by_kelas()
    {
        $admin = User::create([
            'username' => 'admin_test',
            'email' => 'admin_test@simagang.local',
            'nama_lengkap' => 'Admin Test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $jurusan = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $kelas = Kelas::create([
            'tingkat' => 'XII',
            'nama_kelas' => 'RPL:1', // Has a colon to test title sanitization
            'jurusan_id' => $jurusan->id,
        ]);

        $student = User::create([
            'username' => 'siswa_test',
            'email' => 'siswa_test@simagang.local',
            'nama_lengkap' => 'Siswa Test',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'kelas_id' => $kelas->id,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.export.kelas', ['kelas_id' => $kelas->id]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_export_logbook()
    {
        $admin = User::create([
            'username' => 'admin_test',
            'email' => 'admin_test@simagang.local',
            'nama_lengkap' => 'Admin Test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $student = User::create([
            'username' => 'siswa_test',
            'email' => 'siswa_test@simagang.local',
            'nama_lengkap' => 'Siswa Test',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        Logbook::create([
            'user_id' => $student->id,
            'minggu_ke' => 1,
            'tanggal_mulai' => now()->startOfWeek(),
            'tanggal_selesai' => now()->endOfWeek(),
            'kegiatan' => 'Belajar Laravel',
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.export.logbooks'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
