<?php

namespace App\Http\Controllers;

use App\Models\Kursus;
use App\Models\Paket;
use App\Models\Bahasa;
use App\Models\TemplateSertifikat;
use Illuminate\Http\Request;

class TemplateSertifikatController extends Controller
{
    // GET /api/admin/template-sertifikat?id_paket=1&id_bahasa=2
    public function showByCourse(Request $request)
    {
        $data = $request->validate([
            'id_paket' => 'required|exists:paket,id',
            'id_bahasa' => 'required|exists:bahasa,id',
        ]);

        // Ambil paket & bahasa buat deskripsi
        $paket = Paket::findOrFail($data['id_paket']);
        $bahasa = Bahasa::findOrFail($data['id_bahasa']);

        // ⬇️ INI BAGIAN PENTING: AUTO BUAT KURSUS
        $course = Kursus::with(['paket', 'bahasa'])->firstOrCreate(
            [
                'id_paket' => $data['id_paket'],
                'id_bahasa' => $data['id_bahasa'],
            ],
            [
                'deskripsi' => "Kursus {$bahasa->nama_bahasa} - Paket {$paket->nama_paket}",
            ]
        );

        // Cari template sertifikat untuk course ini (kalau ada)
        $template = TemplateSertifikat::where('id_course', $course->id_course)->first();

        return response()->json([
            'course' => $course,
            'data' => $template,  // bisa null kalau belum ada
        ]);
    }

    // POST /api/admin/template-sertifikat
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_paket' => 'required|exists:paket,id',
            'id_bahasa' => 'required|exists:bahasa,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'nama_penandatangan' => 'required|string|max:255',
            'jabatan_penandatangan' => 'nullable|string|max:255',
        ]);

        $paket = Paket::findOrFail($data['id_paket']);
        $bahasa = Bahasa::findOrFail($data['id_bahasa']);

        // ⬇️ AUTO BUAT KURSUS JUGA DI SINI (kalau belum ada)
        $course = Kursus::with(['paket', 'bahasa'])->firstOrCreate(
            [
                'id_paket' => $data['id_paket'],
                'id_bahasa' => $data['id_bahasa'],
            ],
            [
                'deskripsi' => "Kursus {$bahasa->nama_bahasa} - Paket {$paket->nama_paket}",
            ]
        );

        // Simpan / update template untuk course ini
        $template = TemplateSertifikat::updateOrCreate(
            ['id_course' => $course->id_course],
            [
                'judul' => $data['judul'],
                'deskripsi' => $data['deskripsi'] ?? '',
                'nama_penandatangan' => $data['nama_penandatangan'],
                'jabatan_penandatangan' => $data['jabatan_penandatangan'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Template sertifikat disimpan',
            'course' => $course,
            'data' => $template,
        ], 201);
    }
}
