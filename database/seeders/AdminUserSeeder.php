<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\GuruPembimbing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password'),
            'email' => 'admin@simagang.com',
            'nama_lengkap' => 'Administrator',
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create siswa (student) users
        User::create([
            'username' => 'siswa1',
            'password' => Hash::make('password'),
            'email' => 'siswa1@simagang.com',
            'nama_lengkap' => 'Andi Pratama',
            'role' => 'siswa',
            'status' => 'active',
        ]);

        User::create([
            'username' => 'siswa2',
            'password' => Hash::make('password'),
            'email' => 'siswa2@simagang.com',
            'nama_lengkap' => 'Dewi Lestari',
            'role' => 'siswa',
            'status' => 'active',
        ]);

        User::create([
            'username' => 'siswa3',
            'password' => Hash::make('password'),
            'email' => 'siswa3@simagang.com',
            'nama_lengkap' => 'Rizky Ramadhan',
            'role' => 'siswa',
            'status' => 'active',
        ]);

        // Create sample guru pembimbing
        GuruPembimbing::create([
            'nama_guru' => 'Budi Santoso, S.Pd',
            'nip' => '198001012005011001',
            'no_hp' => '081234567890',
            'email' => 'budi.santoso@sekolah.sch.id',
            'alamat' => 'Jl. Pendidikan No. 1, Jakarta',
            'status' => 'active',
        ]);

        GuruPembimbing::create([
            'nama_guru' => 'Siti Aminah, S.Pd',
            'nip' => '198502152010012002',
            'no_hp' => '082345678901',
            'email' => 'siti.aminah@sekolah.sch.id',
            'alamat' => 'Jl. Pelajar No. 2, Jakarta',
            'status' => 'active',
        ]);
    }
}
