<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitras';

    protected $fillable = [
        'nama_mitra',
        'alamat',
        'kontak',
        'gmap_link',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Relationship: Mitra memiliki banyak siswa
     */
    public function siswa()
    {
        return $this->hasMany(User::class, 'mitra_id');
    }

    /**
     * Format lokasi untuk display
     */
    public function getFormattedLocationAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->latitude}, {$this->longitude}";
        }
        return $this->alamat;
    }

    /**
     * Cek apakah lokasi sudah tersedia
     */
    public function hasCoordinates()
    {
        return !empty($this->latitude) && !empty($this->longitude);
    }
}
