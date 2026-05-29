<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilSertifikasi extends Model
{
    protected $table = 'hasil_sertifikasi';
    protected $primaryKey = 'id_hasil';
    public $timestamps = true;

    protected $fillable = [
        'kode_tes',
        'id_member',
        'id_course',
        'skor',
        'tanggal',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function uji()
    {
        return $this->belongsTo(UjiSertifikasi::class, 'kode_tes', 'kode_tes');
    }

    public function course()
    {
        return $this->belongsTo(\App\Models\Kursus::class, 'id_course', 'id_course');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_member', 'id');
    }
}
