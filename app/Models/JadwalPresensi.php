<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPresensi extends Model
{
    use HasFactory;

    protected $table = 'jadwal_presensis';

    protected $fillable = [
        'user_id',
        'hari',
        'jam_masuk',
        'jam_keluar',
        'bulan_mulai',
        'bulan_selesai',
    ];

    protected $casts = [
        'jam_masuk' => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
        'bulan_mulai' => 'date',
        'bulan_selesai' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
