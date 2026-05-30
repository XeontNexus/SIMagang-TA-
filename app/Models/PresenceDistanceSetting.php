<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresenceDistanceSetting extends Model
{
    use HasFactory;

    protected $table = 'presence_distance_settings';

    protected $fillable = [
        'jarak_maksimal',
        'satuan',
        'deskripsi',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];
}
