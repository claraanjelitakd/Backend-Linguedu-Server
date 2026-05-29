<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateSertifikat extends Model
{
    protected $table = 'template_sertifikat';

    protected $fillable = [
        'id_course',
        'judul',
        'deskripsi',
        'nama_penandatangan',
        'jabatan_penandatangan',
    ];

    public $timestamps = true;

    public function course()
    {
        return $this->belongsTo(Kursus::class, 'id_course', 'id_course')
            ->with('paket', 'bahasa');
    }
}
