<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuruPembimbing extends Model
{
    use HasFactory;

    protected $table = 'guru_pembimbings';

    protected $fillable = [
        'nama_guru',
        'nip',
        'no_hp',
        'email',
        'alamat',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
