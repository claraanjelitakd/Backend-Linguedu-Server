<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kursus extends Model
{
    protected $table = 'kursus';
    protected $primaryKey = 'id_course';

    protected $fillable = [
        'id_bahasa',
        'id_paket',
        'deskripsi',
    ];

    public function bahasa()
    {
        return $this->belongsTo(Bahasa::class, 'id_bahasa', 'id');
    }

    public function paket()
    {
        return $this->belongsTo(Paket::class, 'id_paket', 'id');
    }

    public function materi()
    {
        return $this->hasMany(Materi::class, 'id_course', 'id_course');
    }
}
