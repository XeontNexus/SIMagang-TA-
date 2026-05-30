<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status', 20)->default('menunggu')->change();
        });

        User::where('role', 'siswa')->chunkById(100, function ($students) {
            foreach ($students as $student) {
                $newStatus = match ($student->status) {
                    'inactive', 'completed', 'pending', 'rejected' => $student->status,
                    default => $student->isProfileComplete() ? 'aktif' : (
                        in_array($student->status, ['active', 'proses']) ? 'proses' : 'menunggu'
                    ),
                };
                if ($newStatus !== $student->status) {
                    $student->update(['status' => $newStatus]);
                }
            }
        });
    }

    public function down(): void
    {
        DB::table('users')->where('status', 'menunggu')->update(['status' => 'active']);
        DB::table('users')->where('status', 'proses')->update(['status' => 'active']);
        DB::table('users')->where('status', 'aktif')->update(['status' => 'active']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'completed', 'pending'])->default('active')->change();
        });
    }
};
