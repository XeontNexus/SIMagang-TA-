<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template');
            $table->longText('isi_template');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // Add template_id to surat_izins table
        Schema::table('surat_izins', function (Blueprint $table) {
            $table->foreignId('template_id')->nullable()->constrained('surat_templates')->onDelete('set null');
            $table->longText('isi_surat')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('surat_izins', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\SuratTemplate::class);
            $table->dropColumn('template_id');
            $table->dropColumn('isi_surat');
        });

        Schema::dropIfExists('surat_templates');
    }
};
