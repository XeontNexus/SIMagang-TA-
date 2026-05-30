<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',
        'keterangan',
        'bukti_foto',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_keluar',
        'longitude_keluar',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_keluar' => 'decimal:8',
        'longitude_keluar' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeHadir($query)
    {
        return $query->where('status', 'hadir');
    }

    public function scopeIzin($query)
    {
        return $query->where('status', 'izin');
    }

    public function scopeSakit($query)
    {
        return $query->where('status', 'sakit');
    }

    public function scopeAlpha($query)
    {
        return $query->where('status', 'alpha');
    }

    /**
     * Hitung persentase kecocokan lokasi presensi masuk dengan lokasi magang terdaftar
     *
     * @return array|null
     */
    public function calculateKecocokan()
    {
        if ($this->status !== 'hadir' || !$this->latitude_masuk || !$this->longitude_masuk) {
            return null;
        }

        $user = $this->user;
        if (!$user || !$user->latitude || !$user->longitude) {
            return null;
        }

        $radiusHijau = (float) \App\Models\Setting::get('radius_hijau', 30);
        $radiusKuning = (float) \App\Models\Setting::get('radius_kuning', 70);

        $distance = \App\Helpers\LocationHelper::calculateDistance(
            (float) $this->latitude_masuk,
            (float) $this->longitude_masuk,
            (float) $user->latitude,
            (float) $user->longitude
        );

        if ($distance <= $radiusHijau) {
            $kecocokan = 100 - ($distance / $radiusHijau) * 10;
        } elseif ($distance <= $radiusKuning) {
            $kecocokan = 90 - (($distance - $radiusHijau) / ($radiusKuning - $radiusHijau)) * 20;
        } else {
            $kecocokan = max(0, 70 - (($distance - $radiusKuning) / $radiusKuning) * 50);
        }

        return [
            'percentage' => round($kecocokan, 1),
            'distance' => $distance,
            'zone' => \App\Helpers\LocationHelper::getAttendanceZone($distance, $radiusHijau, $radiusKuning)
        ];
    }
}
