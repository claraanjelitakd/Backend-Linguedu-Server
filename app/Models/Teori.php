<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teori extends Model
{
    use HasFactory;

    protected $table = 'teori';      // nama tabel
    protected $primaryKey = 'id';    // pk (default Laravel, sebenernya boleh nggak ditulis)

    protected $fillable = [
        'id_materi',
        'overview',
        'kenapa_penting',
        'konsep_dasar',
        'contoh_praktik',
        'ringkasan',
    ];

    // Relasi: Teori belongsTo Materi
    public function materi()
    {
        return $this->belongsTo(Materi::class, 'id_materi', 'id_materi');
    }
}
