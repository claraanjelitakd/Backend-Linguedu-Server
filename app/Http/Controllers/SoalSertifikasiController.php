<?php

namespace App\Http\Controllers;

use App\Models\SoalSertifikasi;
use Illuminate\Http\Request;

class SoalSertifikasiController extends Controller
{
    // GET /api/admin/sertifikasi/soal
    public function index()
    {
        $soal = SoalSertifikasi::orderBy('kode_tes')
            ->orderBy('id_soal')
            ->get();

        return response()->json([
            'data' => $soal,
        ]);
    }

    // GET /api/admin/sertifikasi/soal/{kode_tes}
    public function byKodeTes($kode_tes)
    {
        $soal = SoalSertifikasi::where('kode_tes', $kode_tes)
            ->orderBy('id_soal')
            ->get();

        return response()->json([
            'kode_tes' => (int) $kode_tes,
            'data'     => $soal,
        ]);
    }

    // POST /api/admin/sertifikasi/soal  (buat banyak sekaligus – jarang kepakai)
    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_tes'           => 'required|exists:uji_sertifikasi,kode_tes',
            'soal'               => 'required|array|min:1',
            'soal.*.pertanyaan'  => 'required|string',
            'soal.*.opsi_a'      => 'required|string',
            'soal.*.opsi_b'      => 'required|string',
            'soal.*.opsi_c'      => 'required|string',
            'soal.*.opsi_d'      => 'required|string',
            'soal.*.jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        $created = [];

        foreach ($data['soal'] as $s) {
            $created[] = SoalSertifikasi::create([
                'kode_tes'      => $data['kode_tes'],
                'pertanyaan'    => $s['pertanyaan'],
                'opsi_a'        => $s['opsi_a'],
                'opsi_b'        => $s['opsi_b'],
                'opsi_c'        => $s['opsi_c'],
                'opsi_d'        => $s['opsi_d'],
                'jawaban_benar' => $s['jawaban_benar'],
            ]);
        }

        return response()->json([
            'message' => 'Soal sertifikasi dibuat',
            'data'    => $created,
        ], 201);
    }

    // POST /api/admin/sertifikasi/soal/add  (dipakai di Blade admin kamu)
    public function addSoal(Request $request)
    {
        $data = $request->validate([
            'kode_tes'      => 'required|exists:uji_sertifikasi,kode_tes',
            'pertanyaan'    => 'required|string',
            'opsi_a'        => 'required|string',
            'opsi_b'        => 'required|string',
            'opsi_c'        => 'required|string',
            'opsi_d'        => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        $soal = SoalSertifikasi::create($data);

        return response()->json([
            'message' => 'Soal sertifikasi ditambahkan',
            'data'    => $soal,
        ], 201);
    }

    // PUT /api/admin/sertifikasi/soal/{id_soal}
    public function update(Request $request, $id_soal)
    {
        $soal = SoalSertifikasi::findOrFail($id_soal);

        $data = $request->validate([
            'pertanyaan'    => 'sometimes|required|string',
            'opsi_a'        => 'sometimes|required|string',
            'opsi_b'        => 'sometimes|required|string',
            'opsi_c'        => 'sometimes|required|string',
            'opsi_d'        => 'sometimes|required|string',
            'jawaban_benar' => 'sometimes|required|in:A,B,C,D',
        ]);

        $soal->update($data);

        return response()->json([
            'message' => 'Soal sertifikasi diupdate',
            'data'    => $soal,
        ]);
    }

    // DELETE /api/admin/sertifikasi/soal/{id_soal}
    public function destroy($id_soal)
    {
        $soal = SoalSertifikasi::findOrFail($id_soal);
        $soal->delete();

        return response()->json([
            'message' => 'Soal sertifikasi dihapus',
        ]);
    }
}
