<?php

namespace App\Http\Controllers;

use App\Models\Kuis;
use App\Models\Materi;
use App\Models\SoalKuis;
use Illuminate\Http\Request;

class KuisController extends Controller
{
    // GET /api/admin/kuis
    public function index()
    {
        $kuis = Kuis::with([
            'materi.course.bahasa',
            'materi.course.paket',
            'soals',
        ])->get();

        return response()->json([
            'data' => $kuis,
        ]);
    }

    // GET /api/admin/kuis/{id_kuis}
    public function show($id_kuis)
    {
        $kuis = Kuis::with([
            'materi.course.bahasa',
            'materi.course.paket',
            'soals',
        ])->findOrFail($id_kuis);

        return response()->json([
            'data' => $kuis,
        ]);
    }

    // POST /api/admin/kuis
    // body:
    // {
    //   "id_materi": 4,
    //   "soal": [
    //      {
    //        "pertanyaan": "...",
    //        "opsi_a": "...",
    //        "opsi_b": "...",
    //        "opsi_c": "...",
    //        "opsi_d": "...",
    //        "jawaban_bnr": "A"
    //      },
    //      ...
    //   ]
    // }
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_materi' => 'required|exists:materi,id_materi',
            'soal' => 'required|array|min:1',
            'soal.*.pertanyaan' => 'required|string',
            'soal.*.opsi_a' => 'required|string',
            'soal.*.opsi_b' => 'required|string',
            'soal.*.opsi_c' => 'required|string',
            'soal.*.opsi_d' => 'required|string',
            'soal.*.jawaban_bnr' => 'required|in:A,B,C,D',
        ]);

        $materi = Materi::with('course')->findOrFail($data['id_materi']);

        $kuis = Kuis::create([
            'id_materi' => $materi->id_materi,
            'id_course' => $materi->id_course,
        ]);

        foreach ($data['soal'] as $s) {
            SoalKuis::create([
                'id_kuis' => $kuis->id_kuis,
                'pertanyaan' => $s['pertanyaan'],
                'opsi_a' => $s['opsi_a'],
                'opsi_b' => $s['opsi_b'],
                'opsi_c' => $s['opsi_c'],
                'opsi_d' => $s['opsi_d'],
                'jawaban_bnr' => $s['jawaban_bnr'], // "A"/"B"/"C"/"D"
            ]);
        }

        $kuis->load('materi.course.bahasa', 'materi.course.paket', 'soals');

        return response()->json([
            'message' => 'Kuis created',
            'data' => $kuis,
        ], 201);
    }

    // POST /api/admin/kuis/{id_kuis}/soal
    public function addSoal(Request $request, $id_kuis)
    {
        $kuis = Kuis::findOrFail($id_kuis);

        $data = $request->validate([
            'pertanyaan' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'jawaban_bnr' => 'required|in:A,B,C,D',
        ]);

        $soal = SoalKuis::create([
            'id_kuis' => $kuis->id_kuis,
            'pertanyaan' => $data['pertanyaan'],
            'opsi_a' => $data['opsi_a'],
            'opsi_b' => $data['opsi_b'],
            'opsi_c' => $data['opsi_c'],
            'opsi_d' => $data['opsi_d'],
            'jawaban_bnr' => $data['jawaban_bnr'],
        ]);

        return response()->json([
            'message' => 'Soal ditambahkan',
            'data' => $soal,
        ], 201);
    }

    // PUT /api/admin/soal-kuis/{id_soal_kuis}
    public function updateSoal(Request $request, $id_soal_kuis)
    {
        $soal = SoalKuis::findOrFail($id_soal_kuis);

        $data = $request->validate([
            'pertanyaan' => 'sometimes|required|string',
            'opsi_a' => 'sometimes|required|string',
            'opsi_b' => 'sometimes|required|string',
            'opsi_c' => 'sometimes|required|string',
            'opsi_d' => 'sometimes|required|string',
            'jawaban_bnr' => 'sometimes|required|in:A,B,C,D',
        ]);

        $soal->update($data);

        return response()->json([
            'message' => 'Soal diupdate',
            'data' => $soal,
        ]);
    }

    // DELETE /api/admin/soal-kuis/{id_soal_kuis}
    public function deleteSoal($id_soal_kuis)
    {
        $soal = SoalKuis::findOrFail($id_soal_kuis);
        $soal->delete();

        return response()->json([
            'message' => 'Soal dihapus',
        ]);
    }

    // DELETE /api/admin/kuis/{id_kuis}
    public function destroy($id_kuis)
    {
        $kuis = Kuis::findOrFail($id_kuis);

        $kuis->soals()->delete();
        $kuis->delete();

        return response()->json([
            'message' => 'Kuis dihapus',
        ]);
    }

    // ==========================================
    // KHUSUS UNTUK MEMBER (FRONTEND)
    // ==========================================

    public function showBySlug($slug)
    {
        // slug sekarang formatnya: "5-grammar"
        $parts = explode('-', $slug, 2);
        $idMateri = (int) $parts[0];

        if (!$idMateri) {
            return response()->json([
                'status' => 'error',
                'message' => 'Slug tidak valid.',
            ], 400);
        }

        // 1. Ambil materinya berdasarkan ID
        $materi = Materi::find($idMateri);

        if (!$materi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Materi tidak ditemukan',
            ], 404);
        }

        // 2. Ambil kuis untuk materi ini + semua soal
        $kuis = Kuis::with('soals')
            ->where('id_materi', $materi->id_materi)
            ->first();

        if (!$kuis) {
            // materi ada, tapi kuis memang belum dibuat
            return response()->json([
                'status' => 'empty',
                'message' => 'Kuis belum tersedia',
                'data' => [
                    'materi' => $materi,
                    'soal' => [],
                ],
            ], 404);
        }

        // 3. Kalau ada kuis, kirim materinya + info kuis + daftar soal
        return response()->json([
            'status' => 'success',
            'data' => [
                'materi' => $materi,
                'kuis_info' => $kuis,
                'soal' => $kuis->soals,
            ],
        ]);
    }

}
