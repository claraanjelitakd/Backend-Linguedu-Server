<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kuis extends Model
{
    protected $table = 'kuis';
    protected $primaryKey = 'id_kuis';

    protected $fillable = [
        'id_materi',
        'id_course',
    ];

    public $timestamps = true;

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'id_materi', 'id_materi');
    }

    public function course()
    {
        return $this->belongsTo(Kursus::class, 'id_course', 'id_course');
    }

    public function soals()
    {
        return $this->hasMany(SoalKuis::class, 'id_kuis', 'id_kuis');
    }
}

