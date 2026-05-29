<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjiSertifikasi extends Model
{
    protected $table = 'uji_sertifikasi';
    protected $primaryKey = 'kode_tes';

    protected $fillable = [
        'id_course',
        'tgl',
        'kkm',      // ← skor user nanti di tabel hasil_sertifikasi
    ];

    public $timestamps = true;

    public function course()
    {
        return $this->belongsTo(Kursus::class, 'id_course', 'id_course');
    }

    public function soalSertifikasi()
    {
        return $this->hasMany(SoalSertifikasi::class, 'kode_tes', 'kode_tes');
    }

    public function sertifikat()
    {
        // satu uji bisa punya banyak sertifikat (per member),
        // tapi kalau kamu mau 1:1 silakan pakai hasOne.
        return $this->hasMany(Sertifikat::class, 'kode_tes', 'kode_tes');
    }
    public function hasil()
    {
        return $this->hasMany(HasilSertifikasi::class, 'kode_tes', 'kode_tes');
    }
}
