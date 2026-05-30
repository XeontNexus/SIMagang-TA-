<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if foreign key doesn't already exist before adding
            if (Schema::hasTable('guru_pembimbings')) {
                $table->foreign('guru_pembimbing_id')
                    ->references('id')
                    ->on('guru_pembimbings')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
            
            if (Schema::hasTable('kelas')) {
                $table->foreign('kelas_id')
                    ->references('id')
                    ->on('kelas')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
            
            if (Schema::hasTable('jurusans')) {
                $table->foreign('jurusan_id')
                    ->references('id')
                    ->on('jurusans')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys if they exist
            $table->dropForeignIfExists(['guru_pembimbing_id']);
            $table->dropForeignIfExists(['kelas_id']);
            $table->dropForeignIfExists(['jurusan_id']);
        });
    }
};
