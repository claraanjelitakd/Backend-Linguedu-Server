<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalKuis extends Model
{
    protected $table = 'soal_kuis';
    protected $primaryKey = 'id_soal_kuis';

    protected $fillable = [
        'id_kuis',
        'pertanyaan',
        'jawaban_bnr',   // berisi "A" / "B" / "C" / "D"
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
    ];

    public $timestamps = true;

    public function kuis()
    {
        return $this->belongsTo(Kuis::class, 'id_kuis', 'id_kuis');
    }
}
