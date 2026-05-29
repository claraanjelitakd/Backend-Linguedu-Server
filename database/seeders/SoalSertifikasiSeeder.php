<?php

namespace App\Http\Controllers;

use App\Models\SoalSertifikasi;
use App\Models\UjiSertifikasi;
use App\Models\RegistrasiKursus;
use App\Models\HasilSertifikasi;
use Illuminate\Http\Request;

class SoalSertifikasiController extends Controller
{
    // GET /api/admin/sertifikasi/soal
    public function index()
    {
        $soal = SoalSertifikasi::orderBy('kode_tes')->orderBy('id_soal')->get();

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
            'data' => $soal,
        ]);
    }

    // =======================
    //  BARU: UNTUK MEMBER
    // =======================
    // GET /api/sertifikasi/soal?id_member=123
    public function forMember(Request $request)
    {
        $request->validate([
            'id_member' => 'required|integer',
        ]);

        $idMember = (int) $request->id_member;

        // 1. Cari kursus aktif / terakhir yang diregistrasi member ini
        $registrasi = RegistrasiKursus::where('id_member', $idMember)
            ->orderByDesc('created_at')
            ->first();

        if (!$registrasi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kamu belum terdaftar di kursus apa pun.',
            ], 404);
        }

        $idCourse = $registrasi->id_course;

        // 2. Cari Uji Sertifikasi untuk course ini
        $uji = UjiSertifikasi::with(['course.bahasa', 'course.paket'])
            ->where('id_course', $idCourse)
            ->first();

        if (!$uji) {
            return response()->json([
                'status' => 'error',
                'message' => 'Uji sertifikasi untuk kursus ini belum disiapkan admin.',
            ], 404);
        }

        // 3. Cek apakah member sudah pernah ikut ujian ini
        $hasil = HasilSertifikasi::where('id_member', $idMember)
            ->where('kode_tes', $uji->kode_tes)
            ->first();

        $alreadyTaken = (bool) $hasil;

        // 4. Kalau sudah pernah ikut, boleh kirim ringkasan hasil saja
        if ($alreadyTaken) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'kode_tes' => $uji->kode_tes,
                    'kkm' => $uji->kkm ?? 70,
                    'course' => $uji->course,
                    'already_taken' => true,
                    'hasil' => $hasil,
                    'soal' => [], // kita kosongkan, supaya tidak bisa ujian lagi
                ],
            ]);
        }

        // 5. Ambil semua soal sertifikasi
        $soal = SoalSertifikasi::where('kode_tes', $uji->kode_tes)
            ->orderBy('id_soal')
            ->get();

        if ($soal->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Belum ada soal untuk ujian sertifikasi ini.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'kode_tes' => $uji->kode_tes,
                'kkm' => $uji->kkm ?? 70,
                'course' => $uji->course,
                'already_taken' => false,
                'hasil' => null,
                'soal' => $soal,
            ],
        ]);
    }

    // ==== method lain (store, addSoal, update, destroy) tetap seperti punyamu ====

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_tes' => 'required|exists:uji_sertifikasi,kode_tes',
            'soal' => 'required|array|min:1',
            'soal.*.pertanyaan' => 'required|string',
            'soal.*.opsi_a' => 'required|string',
            'soal.*.opsi_b' => 'required|string',
            'soal.*.opsi_c' => 'required|string',
            'soal.*.opsi_d' => 'required|string',
            'soal.*.jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        $created = [];

        foreach ($data['soal'] as $s) {
            $created[] = SoalSertifikasi::create([
                'kode_tes' => $data['kode_tes'],
                'pertanyaan' => $s['pertanyaan'],
                'opsi_a' => $s['opsi_a'],
                'opsi_b' => $s['opsi_b'],
                'opsi_c' => $s['opsi_c'],
                'opsi_d' => $s['opsi_d'],
                'jawaban_benar' => $s['jawaban_benar'],
            ]);
        }

        return response()->json([
            'message' => 'Soal sertifikasi dibuat',
            'data' => $created,
        ], 201);
    }

    public function addSoal(Request $request)
    {
        $data = $request->validate([
            'kode_tes' => 'required|exists:uji_sertifikasi,kode_tes',
            'pertanyaan' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        $soal = SoalSertifikasi::create($data);

        return response()->json([
            'message' => 'Soal sertifikasi ditambahkan',
            'data' => $soal,
        ], 201);
    }

    public function update(Request $request, $id_soal)
    {
        $soal = SoalSertifikasi::findOrFail($id_soal);

        $data = $request->validate([
            'pertanyaan' => 'sometimes|required|string',
            'opsi_a' => 'sometimes|required|string',
            'opsi_b' => 'sometimes|required|string',
            'opsi_c' => 'sometimes|required|string',
            'opsi_d' => 'sometimes|required|string',
            'jawaban_benar' => 'sometimes|required|in:A,B,C,D',
        ]);

        $soal->update($data);

        return response()->json([
            'message' => 'Soal sertifikasi diupdate',
            'data' => $soal,
        ]);
    }

    public function destroy($id_soal)
    {
        $soal = SoalSertifikasi::findOrFail($id_soal);
        $soal->delete();

        return response()->json([
            'message' => 'Soal sertifikasi dihapus',
        ]);
    }
}
