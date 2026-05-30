<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop surat_izins table jika ada
        Schema::dropIfExists('surat_izins');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: fitur surat_izin sudah dihapus
    }
};
