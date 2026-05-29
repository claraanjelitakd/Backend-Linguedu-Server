<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paket extends Model
{
    use HasFactory;

    protected $table = 'paket';

    protected $fillable = [
        'nama_paket',
        'desc',
        'harga',
    ];

    public function kursus()
    {
        // foreign key di tabel kursus = id_paket, ref ke paket.id
        return $this->hasMany(Kursus::class, 'id_paket', 'id');
    }
}
