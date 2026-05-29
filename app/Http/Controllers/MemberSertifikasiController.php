<?php

namespace App\Http\Controllers;

use App\Models\RegistrasiKursus;
use App\Models\UjiSertifikasi;
use Illuminate\Http\Request;

class MemberSertifikasiController extends Controller
{
    // GET /api/member/sertifikasi/{kode_tes}
    // hanya boleh diakses kalau progress = 3
    public function showTes(Request $request, $kode_tes)
    {
        $user = $request->user(); // dari Sanctum / auth

        $uji = UjiSertifikasi::with('materi.course', 'soalSertifikasi')
            ->findOrFail($kode_tes);

        // Cek apakah user sudah registrasi di course ini & progress = 3
        $registrasi = RegistrasiKursus::where('id_member', $user->id)
            ->where('id_course', $uji->id_course)
            ->where('progress', 3)
            ->first();

        if (!$registrasi) {
            return response()->json([
                'message' => 'Anda belum menyelesaikan semua level. Sertifikasi hanya bisa diakses jika progress = 3.',
            ], 403);
        }

        return response()->json([
            'message' => 'Tes sertifikasi dapat diakses',
            'data' => [
                'uji' => $uji,
                'soal' => $uji->soalSertifikasi,
                'course' => $uji->course,
                'materi' => $uji->materi,
            ],
        ]);
    }
}
