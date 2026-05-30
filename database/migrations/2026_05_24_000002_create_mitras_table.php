<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create mitras table jika belum ada
        if (!Schema::hasTable('mitras')) {
            Schema::create('mitras', function (Blueprint $table) {
                $table->id();
                $table->string('nama_mitra', 150);
                $table->string('alamat', 255)->nullable();
                $table->string('kontak', 20)->nullable();
                $table->text('gmap_link')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->timestamps();
                $table->index('nama_mitra');
            });
        }

        // Add mitra_id foreign key ke users table jika belum ada
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'mitra_id')) {
                $table->unsignedBigInteger('mitra_id')->nullable()->after('mitra_magang');
                $table->foreign('mitra_id')
                    ->references('id')
                    ->on('mitras')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'mitra_id')) {
                $table->dropForeign(['mitra_id']);
                $table->dropColumn('mitra_id');
            }
        });

        if (Schema::hasTable('mitras')) {
            Schema::dropIfExists('mitras');
        }
    }
};
