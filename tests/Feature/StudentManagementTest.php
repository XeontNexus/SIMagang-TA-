<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function getAdmin()
    {
        return User::create([
            'username' => 'admin_test',
            'email' => 'admin_test@simagang.local',
            'nama_lengkap' => 'Admin Test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    public function test_student_creation_defaults_to_belum_dinotifikasi()
    {
        $admin = $this->getAdmin();

        $response = $this->actingAs($admin)
            ->post(route('admin.students.store'), [
                'nama_lengkap' => 'Siswa Baru',
                'username' => 'siswabaru',
                'no_hp' => '081234567890',
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('admin.students.index'));
        $this->assertDatabaseHas('users', [
            'username' => 'siswabaru',
            'status' => 'belum_dinotifikasi',
            'role' => 'siswa',
        ]);
    }

    public function test_student_bulk_delete()
    {
        $admin = $this->getAdmin();

        $student1 = User::create([
            'username' => 'siswa1',
            'email' => 'siswa1@simagang.local',
            'nama_lengkap' => 'Siswa Satu',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'status' => 'belum_dinotifikasi',
        ]);

        $student2 = User::create([
            'username' => 'siswa2',
            'email' => 'siswa2@simagang.local',
            'nama_lengkap' => 'Siswa Dua',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'status' => 'belum_dinotifikasi',
        ]);

        $student3 = User::create([
            'username' => 'siswa3',
            'email' => 'siswa3@simagang.local',
            'nama_lengkap' => 'Siswa Tiga',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'status' => 'belum_dinotifikasi',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.students.bulk-delete'), [
                'ids' => [$student1->id, $student2->id],
            ]);

        $response->assertRedirect(route('admin.students.index'));
        
        $this->assertDatabaseMissing('users', ['id' => $student1->id]);
        $this->assertDatabaseMissing('users', ['id' => $student2->id]);
        $this->assertDatabaseHas('users', ['id' => $student3->id]);
    }

    public function test_mark_as_notified_transitions_status()
    {
        $admin = $this->getAdmin();

        $student = User::create([
            'username' => 'siswa1',
            'email' => 'siswa1@simagang.local',
            'nama_lengkap' => 'Siswa Satu',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'status' => 'belum_dinotifikasi',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.students.mark-as-notified', $student));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'status' => 'menunggu',
        ]);
    }
}
