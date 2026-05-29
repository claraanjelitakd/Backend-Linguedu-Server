<?php

namespace App\Http\Controllers;

use App\Models\UjiSertifikasi;
use App\Models\Kursus;
use App\Models\Paket;
use App\Models\Bahasa;
use Illuminate\Http\Request;

class UjiSertifikasiController extends Controller
{
    // GET /api/admin/sertifikasi/tes
    public function index()
    {
        $uji = UjiSertifikasi::with([
            'course.bahasa',
            'course.paket',
            'soalSertifikasi',
        ])->get();

        $uji->each(function ($item) {
            $item->jumlah_soal = $item->soalSertifikasi->count();
        });

        return response()->json([
            'data' => $uji,
        ]);
    }

    // POST /api/admin/sertifikasi/tes
    // body:
    // {
    //   "id_paket": 1,
    //   "id_bahasa": 2
    // }
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_paket'  => 'required|exists:paket,id',
            'id_bahasa' => 'required|exists:bahasa,id',
            // tidak ada field kkm lagi
        ]);

        $paket  = Paket::findOrFail($data['id_paket']);
        $bahasa = Bahasa::findOrFail($data['id_bahasa']);

        // Cari / buat kursus untuk kombinasi paket + bahasa
        $kursus = Kursus::firstOrCreate(
            [
                'id_paket'  => $paket->id,
                'id_bahasa' => $bahasa->id,
            ],
            [
                'deskripsi' => "Kursus {$bahasa->nama_bahasa} - Paket {$paket->nama_paket}",
            ]
        );

        // Satu course = satu ujian sertifikasi
        // KKM selalu 70
        $uji = UjiSertifikasi::firstOrCreate(
            ['id_course' => $kursus->id_course],
            [
                'tgl' => now()->toDateString(),
                'kkm' => 70,
            ]
        );

        $uji->load('course.bahasa', 'course.paket', 'soalSertifikasi');

        return response()->json([
            'message' => 'Uji sertifikasi dibuat / sudah ada',
            'data'    => $uji,
        ], 201);
    }

    // DELETE /api/admin/sertifikasi/tes/{kode_tes}
    public function destroy($kode_tes)
    {
        $uji = UjiSertifikasi::with('soalSertifikasi')->findOrFail($kode_tes);

        $uji->soalSertifikasi()->delete();
        $uji->delete();

        return response()->json([
            'message' => 'Uji sertifikasi dihapus',
        ]);
    }
}
