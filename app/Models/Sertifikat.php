<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';
    protected $primaryKey = 'kode_hasil_sertif';

    protected $fillable = [
        'id_admin',
        'id_course',
        'id_member',
        'kode_tes',
        'format', // bisa path file pdf di storage
    ];

    public $timestamps = true;

    public function member()
    {
        return $this->belongsTo(User::class, 'id_member', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Kursus::class, 'id_course', 'id_course');
    }

    public function uji()
    {
        return $this->belongsTo(UjiSertifikasi::class, 'kode_tes', 'kode_tes');
    }
}
