<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilTes extends Model
{
    protected $table = 'hasil_tes';
    protected $primaryKey = 'id_hasil';

    protected $fillable = [
        'id_kuis',
        'id_member',
        'id_admin',
        'id_course',
        'skor',
        'tanggal',
        'desc',
    ];

    public $timestamps = true;

    // Relasi ke kuis
    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'id_kuis', 'id_kuis');
    }
    public function member()
    {
        // member di sini sebenarnya user dengan role "member"
        return $this->belongsTo(User::class, 'id_member', 'id');
    }
}
