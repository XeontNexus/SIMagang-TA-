<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileNoHpValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_profile_rejects_no_hp_that_is_not_valid_format(): void
    {
        $admin = User::create([
            'role' => 'admin',
            'username' => 'admin1',
            'email' => 'admin1@example.com',
            'password' => bcrypt('password123'),
            'nama_lengkap' => 'Admin Test',
        ]);

        $this->actingAs($admin);

        $response = $this->from(route('profile.edit'))->post(route('profile.update'), [
            'nama_lengkap' => 'Admin Test',
            'email' => 'admin1@example.com',
            'username' => 'admin1',
            'no_hp' => '081234567890',
        ]);

        $response->assertSessionHasErrors('no_hp');
        $response->assertRedirect();
    }

    public function test_admin_profile_rejects_names_with_numbers_or_symbols(): void
    {
        $admin = User::create([
            'role' => 'admin',
            'username' => 'admin2',
            'email' => 'admin2@example.com',
            'password' => bcrypt('password123'),
            'nama_lengkap' => 'Admin Test',
        ]);

        $this->actingAs($admin);

        $response = $this->from(route('profile.edit'))->post(route('profile.update'), [
            'nama_lengkap' => 'Admin 123',
            'email' => 'admin2@example.com',
            'username' => 'admin2',
            'no_hp' => '621234567890',
        ]);

        $response->assertSessionHasErrors('nama_lengkap');
        $response->assertRedirect();
    }

    public function test_student_profile_rejects_nisn_with_non_numeric_characters(): void
    {
        $student = User::create([
            'role' => 'siswa',
            'username' => 'student1',
            'email' => 'student1@example.com',
            'password' => bcrypt('password123'),
            'nama_lengkap' => 'Student Test',
            'nisn' => '123456',
        ]);

        $this->actingAs($student);

        $response = $this->from(route('profile.edit'))->post(route('profile.update'), [
            'nama_lengkap' => 'Student Test',
            'no_hp' => '621234567890',
            'nisn' => '12A3456',
        ]);

        $response->assertSessionHasErrors('nisn');
        $response->assertRedirect();
    }
}
