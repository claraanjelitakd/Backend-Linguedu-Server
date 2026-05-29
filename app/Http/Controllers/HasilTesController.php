<?php

namespace App\Http\Controllers;

use App\Models\HasilTes;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\RegistrasiKursus;
use App\Models\RiwayatMateri; // ✅ TAMBAHAN 1: Import Model ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HasilTesController extends Controller
{
    // GET /api/admin/hasil-tes?member=1&course=2
    // List hasil tes (bisa difilter)
    public function index(Request $request)
    {
        $query = HasilTes::with('kuis.materi.course');

        if ($request->filled('member')) {
            $query->where('id_member', $request->member);
        }

        if ($request->filled('course')) {
            $query->where('id_course', $request->course);
        }

        $hasil = $query->orderByDesc('tanggal')->get();

        return response()->json([
            'data' => $hasil,
        ]);
    }

    // POST /api/admin/kuis/{id_kuis}/submit
    public function submit(Request $request, $id_kuis)
    {
        $kuis = Kuis::with('soals')->findOrFail($id_kuis);

        $data = $request->validate([
            'id_member' => 'required|integer',
            'answers' => 'required|array|min:1',
            'answers.*.id_soal_kuis' => 'required|integer',
            'answers.*.jawaban' => 'required|string',
        ]);

        $idMember = $data['id_member'];

        // mapping soal -> jawaban benar (A/B/C/D)
        $soalMap = $kuis->soals->keyBy('id_soal_kuis');

        $benar = 0;
        $total = max(1, $kuis->soals->count());

        foreach ($data['answers'] as $ans) {
            $idSoal = $ans['id_soal_kuis'];
            $jawab = strtoupper(trim($ans['jawaban'])); // A/B/C/D

            if (!isset($soalMap[$idSoal])) {
                continue; // soal tidak ada di kuis ini
            }

            $kunci = strtoupper(trim($soalMap[$idSoal]->jawaban_bnr)); // A/B/C/D

            if ($jawab === $kunci) {
                $benar++;
            }
        }


        $skorFloat = ($benar / $total) * 100;
        $skor = (int) round($skorFloat);

        // Batas kelulusan (bisa diubah, misal 60 atau 70)
        $isLulus = $skor >= 60;
        $desc = $isLulus ? 'Lulus' : 'Tidak lulus';

        DB::beginTransaction();

        try {
            // simpan hasil tes
            $hasil = HasilTes::create([
                'id_kuis' => $kuis->id_kuis,
                'id_member' => $idMember,
                'id_admin' => null, // kalau mau suatu saat isi admin penilai
                'id_course' => $kuis->id_course,
                'skor' => $skor,
                'tanggal' => now()->toDateString(),
                'desc' => $desc,
            ]);

            // update progress registrasi_kursus
            $this->updateProgress($idMember, $kuis->id_course);

            // ✅ TAMBAHAN 2: UPDATE RIWAYAT MATERI JIKA LULUS
            if ($isLulus) {
                RiwayatMateri::updateOrCreate(
                    [
                        'id_member' => $idMember,          // ← sekarang nyambung ke users.id
                        'id_materi' => $kuis->id_materi,   // pastikan kolom ini benar
                    ],
                    [
                        'has_passed_quiz' => true,
                        'is_completed' => true
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Hasil tes disimpan',
                'data' => [
                    'hasil' => $hasil,
                    'benar' => $benar,
                    'total' => $total,
                    'skor' => $skor,
                    'status' => $desc,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menyimpan hasil tes',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hitung dan update progress di registrasi_kursus
     * berdasarkan jumlah materi yang sudah punya hasil_tes
     */
    private function updateProgress(int $idMember, int $idCourse): void
    {
        // total materi di course ini
        $totalMateri = Materi::where('id_course', $idCourse)->count();

        if ($totalMateri === 0) {
            RegistrasiKursus::where('id_member', $idMember)
                ->where('id_course', $idCourse)
                ->update(['progress' => 0]);
            return;
        }

        // materi yang sudah pernah dikerjakan kuis-nya oleh member ini
        $completedMateri = HasilTes::join('kuis', 'hasil_tes.id_kuis', '=', 'kuis.id_kuis')
            ->where('hasil_tes.id_member', $idMember)
            ->where('kuis.id_course', $idCourse)
            ->distinct('kuis.id_materi')
            ->count('kuis.id_materi');

        $progress = (int) floor(($completedMateri / $totalMateri) * 100);

        RegistrasiKursus::where('id_member', $idMember)
            ->where('id_course', $idCourse)
            ->update(['progress' => $progress]);
    }
}
