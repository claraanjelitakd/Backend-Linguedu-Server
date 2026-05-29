<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bahasa extends Model
{
    use HasFactory;

    protected $table = 'bahasa';
    // karena di DB tidak ada kolom "id_bahasa", pake default "id" saja:
    // boleh tulis:
    // protected $primaryKey = 'id';
    // atau baris ini DIHAPUS sekalian (Laravel default-nya pakai 'id')

    protected $fillable = [
        'nama_bahasa',
        'desc',
    ];

    public function kursus()
    {
        // FK di tabel kursus = id_bahasa, referensi ke bahasa.id
        return $this->hasMany(Kursus::class, 'id_bahasa', 'id');
    }
}
