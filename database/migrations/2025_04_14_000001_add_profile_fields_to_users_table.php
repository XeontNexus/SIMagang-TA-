<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kelas')->nullable()->after('jurusan');
            $table->string('pembimbing_lapangan')->nullable()->after('alamat_magang');
            $table->text('gmap_magang')->nullable()->after('pembimbing_lapangan');
            $table->string('nisn', 20)->nullable()->after('no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'pembimbing_lapangan', 'gmap_magang', 'nisn']);
        });
    }
};
