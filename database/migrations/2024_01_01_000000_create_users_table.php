<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('nama_lengkap', 100);
            $table->string('email', 100)->unique();
            $table->enum('role', ['admin', 'siswa'])->default('siswa');
            $table->string('no_hp', 20)->nullable();
            $table->string('institusi', 100)->nullable();
            $table->string('jurusan', 50)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['active', 'inactive', 'completed', 'pending'])->default('active');
            $table->string('foto_profile', 255)->nullable();
            $table->string('alamat_magang', 255)->nullable();
            $table->unsignedBigInteger('guru_pembimbing_id')->nullable();
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->unsignedBigInteger('jurusan_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
