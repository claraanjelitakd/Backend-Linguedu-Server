<?php

namespace App\Http\Controllers;

use App\Models\HasilSertifikasi;
use App\Models\RegistrasiKursus;
use App\Models\UjiSertifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilSertifikasiController extends Controller
{
    // ===== ADMIN: LIST HASIL SERTIFIKASI =====
    // GET /api/admin/hasil-sertifikasi?member=1&course=2
    public function index(Request $request)
    {
        $q = HasilSertifikasi::with(['course.bahasa', 'course.paket', 'uji']);

        if ($request->filled('member')) {
            $q->where('id_member', $request->member);
        }

        if ($request->filled('course')) {
            $q->where('id_course', $request->course);
        }

        return response()->json([
            'data' => $q->orderByDesc('tanggal')->get(),
        ]);
    }

    // ===== MEMBER: AMBIL SOAL UNTUK KURSUS AKTIF =====
    // GET /api/sertifikasi/soal?id_member=4
    public function getSoalForMember(Request $request)
    {
        $data = $request->validate([
            'id_member' => 'required|integer',
        ]);
        $idMember = $data['id_member'];

        // Ambil kursus terakhir yang diikuti member ini
        $reg = RegistrasiKursus::where('id_member', $idMember)
            ->orderByDesc('created_at')
            ->first();

        if (!$reg) {
            return response()->json([
                'message' => 'Kamu belum terdaftar di kursus manapun.',
            ], 404);
        }

        $idCourse = $reg->id_course;

        // Cari uji sertifikasi untuk course ini
        $uji = UjiSertifikasi::with(['course.bahasa', 'course.paket', 'soalSertifikasi'])
            ->where('id_course', $idCourse)
            ->first();

        if (!$uji) {
            return response()->json([
                'message' => 'Belum ada ujian sertifikasi untuk kursus ini. Silakan hubungi admin.',
            ], 404);
        }

        // Cek apakah member sudah pernah ikut ujian ini
        $hasil = HasilSertifikasi::where('kode_tes', $uji->kode_tes)
            ->where('id_member', $idMember)
            ->first();

        $kkm = $uji->kkm ?? $uji->skor ?? 70; // aman kalau di DB masih nama kolom "skor"

        return response()->json([
            'data' => [
                'kode_tes' => $uji->kode_tes,
                'kkm' => $kkm,
                'course' => $uji->course,
                'soal' => $uji->soalSertifikasi,
                'already_taken' => (bool) $hasil,
                'hasil' => $hasil,
            ],
        ]);
    }

    // ===== MEMBER: SUBMIT UJIAN (HANYA SEKALI) =====
    // POST /api/sertifikasi/{kode_tes}/submit
    public function submit(Request $request, $kode_tes)
    {
        $uji = UjiSertifikasi::with('soalSertifikasi')->findOrFail($kode_tes);

        $data = $request->validate([
            'id_member' => 'required|integer',
            'answers' => 'required|array|min:1',
            'answers.*.id_soal' => 'required|integer',
            'answers.*.jawaban' => 'required|string',
        ]);

        $idMember = $data['id_member'];

        // Cek kalau sudah pernah ikut → tolak
        $existing = HasilSertifikasi::where('kode_tes', $uji->kode_tes)
            ->where('id_member', $idMember)
            ->first();

        if ($existing) {
            $kkm = $uji->kkm ?? $uji->skor ?? 70;

            return response()->json([
                'message' => 'Ujian sertifikasi hanya bisa diikuti sekali.',
                'data' => [
                    'skor' => $existing->skor,
                    'status' => $existing->status,
                    'kkm' => $kkm,
                    'already_taken' => true,
                ],
            ], 422);
        }

        // Hitung nilai
        $mapSoal = $uji->soalSertifikasi->keyBy('id_soal');
        $total = max(1, $uji->soalSertifikasi->count());
        $benar = 0;

        foreach ($data['answers'] as $ans) {
            $idSoal = $ans['id_soal'];
            $jawab = strtoupper(trim($ans['jawaban'])); // A/B/C/D

            if (!isset($mapSoal[$idSoal])) {
                continue;
            }

            $kunci = strtoupper(trim($mapSoal[$idSoal]->jawaban_benar));
            if ($jawab === $kunci) {
                $benar++;
            }
        }

        $skorFloat = ($benar / $total) * 100;
        $skor = (int) round($skorFloat);

        $kkm = $uji->kkm ?? $uji->skor ?? 70;
        $status = $skor >= $kkm ? 'Lulus' : 'Tidak lulus';

        DB::beginTransaction();
        try {
            $hasil = HasilSertifikasi::create([
                'kode_tes' => $uji->kode_tes,
                'id_member' => $idMember,
                'id_course' => $uji->id_course,
                'skor' => $skor,
                'tanggal' => now()->toDateString(),
                'status' => $status,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Hasil ujian sertifikasi disimpan.',
                'data' => [
                    'id_hasil' => $hasil->id_hasil,
                    'skor' => $skor,
                    'status' => $status,
                    'kkm' => $kkm,
                    'benar' => $benar,
                    'total' => $total,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan hasil ujian sertifikasi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
