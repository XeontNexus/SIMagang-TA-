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
        // 1. Copy data from student_notifications to notifications if student_notifications exists
        if (Schema::hasTable('student_notifications')) {
            DB::table('student_notifications')->orderBy('id')->chunk(100, function ($notifications) {
                $insertData = [];
                foreach ($notifications as $notif) {
                    $insertData[] = [
                        'user_id' => $notif->user_id,
                        'title' => $notif->title,
                        'message' => $notif->message,
                        'type' => in_array($notif->type, ['success', 'info', 'warning', 'danger']) ? $notif->type : 'info',
                        'icon' => 'fa-bell',
                        'link' => null,
                        'related_type' => null,
                        'related_id' => null,
                        'read_at' => $notif->read_at,
                        'created_at' => $notif->created_at,
                        'updated_at' => $notif->updated_at,
                    ];
                }

                if (!empty($insertData)) {
                    DB::table('notifications')->insert($insertData);
                }
            });

            // 2. Drop the legacy table
            Schema::dropIfExists('student_notifications');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('student_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
