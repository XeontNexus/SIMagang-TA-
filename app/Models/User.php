<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'role',
        'no_hp',
        'nisn',
        'institusi',
        'mitra_magang',
        'jurusan',
        'kelas',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'foto_profile',
        'alamat_magang',
        'latitude',
        'longitude',
        'pembimbing_lapangan',
        'no_hp_pembimbing_lapangan',
        'gmap_magang',
        'guru_pembimbing_id',
        'no_hp_guru_pembimbing',
        'kelas_id',
        'jurusan_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    // Relationships
    public function presensis()
    {
        return $this->hasMany(Presensi::class);
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    public function jadwalPresensis()
    {
        return $this->hasMany(JadwalPresensi::class);
    }

    public function guruPembimbing()
    {
        return $this->belongsTo(GuruPembimbing::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    // Scopes
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isStudent(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Email internal (tidak ditampilkan ke user) untuk kebutuhan database.
     */
    public static function internalEmailFromUsername(string $username): string
    {
        return strtolower($username) . '@simagang.local';
    }

    public function isProfileComplete(): bool
    {
        return (bool) (
            $this->no_hp
            && $this->institusi
            && $this->jurusan_id
            && $this->kelas_id
            && $this->mitra_magang
            && $this->alamat_magang
            && $this->pembimbing_lapangan
            && $this->tanggal_mulai
            && $this->tanggal_selesai
            && $this->guru_pembimbing_id
            && $this->gmap_magang
            && $this->latitude
            && $this->longitude
        );
    }

    /**
     * Sinkronkan status siswa: menunggu → proses (setelah login) → aktif (data lengkap).
     */
    public function syncStudentStatus(bool $afterLogin = false): void
    {
        if (!$this->isSiswa()) {
            return;
        }

        if (in_array($this->status, ['inactive', 'completed', 'pending', 'rejected'], true)) {
            return;
        }

        if ($this->isProfileComplete()) {
            if ($this->status !== 'aktif') {
                $this->update(['status' => 'aktif']);
            }
            return;
        }

        if ($afterLogin || in_array($this->status, ['proses', 'active', 'aktif'], true)) {
            if ($this->status !== 'proses') {
                $this->update(['status' => 'proses']);
            }
        }
    }
}
