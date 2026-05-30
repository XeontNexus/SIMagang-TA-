<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusans';

    protected $fillable = [
        'nama_jurusan',
        'kode_jurusan',
        'deskripsi',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
