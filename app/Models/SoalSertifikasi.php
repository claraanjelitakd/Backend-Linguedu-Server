<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalSertifikasi extends Model
{
    protected $table = 'soal_sertifikasi';
    protected $primaryKey = 'id_soal';

    protected $fillable = [
        'kode_tes',
        'pertanyaan',
        'jawaban_benar', // isinya "A" / "B" / "C" / "D"
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
    ];

    public $timestamps = true;

    public function uji()
    {
        // kalau kode_tes di uji_sertifikasi sama dengan di soal_sertifikasi
        return $this->belongsTo(UjiSertifikasi::class, 'kode_tes', 'kode_tes');
    }
}
