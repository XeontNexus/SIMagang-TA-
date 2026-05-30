<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presence_distance_settings', function (Blueprint $table) {
            $table->id();
            $table->double('jarak_maksimal')->default(500)->comment('Jarak maksimal presensi dalam meter');
            $table->string('satuan')->default('meter')->comment('Satuan jarak (meter, km)');
            $table->text('deskripsi')->nullable()->comment('Deskripsi pengaturan');
            $table->boolean('aktif')->default(true)->comment('Status pengaturan');
            $table->timestamps();
        });

        // Insert default setting
        DB::table('presence_distance_settings')->insert([
            'jarak_maksimal' => 500,
            'satuan' => 'meter',
            'deskripsi' => 'Jarak maksimal yang diizinkan untuk melakukan presensi dari lokasi magang',
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presence_distance_settings');
    }
};
