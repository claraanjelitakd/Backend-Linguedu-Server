<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrasiKursus extends Model
{
    protected $table = 'registrasi_kursus';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_admin',
        'id_member',
        'id_course',
        'tgl_trans',
        'metode_bayar',
        'total_byr',
        'bukti_byr',
        'progress',
        'level'
    ];

    public $timestamps = true;
    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'id_member', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Kursus::class, 'id_course', 'id_course');
    }
    
}
