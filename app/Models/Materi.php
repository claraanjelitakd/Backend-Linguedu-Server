<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';
    protected $primaryKey = 'id_materi';

    protected $fillable = [
        'id_course',
        'level',
        'judul',
        'tipe',
        'url_video',
        'teks_teori',
        'video_path',
    ];
    protected $appends = ['video_url'];  // ⬅️ biar API otomatis kirim URL lengkap

    public function course()
    {
        return $this->belongsTo(Kursus::class, 'id_course', 'id_course');
    }
    public function teori()
    {
        return $this->hasOne(\App\Models\Teori::class, 'id_materi', 'id_materi');
    }
    // ⬅️ URL lengkap untuk dipakai di FE
    public function getVideoUrlAttribute()
    {
        return $this->video_path
            ? asset('storage/' . $this->video_path)
            : null;
    }

}
