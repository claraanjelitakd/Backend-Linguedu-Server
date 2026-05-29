<?php

namespace App\Http\Controllers;

use App\Models\Teori;
use App\Models\Materi;
use Illuminate\Http\Request;

class TeoriController extends Controller
{
    /**
     * GET /api/admin/teori
     * List semua teori + info materi + paket + bahasa
     */
    public function index()
    {
        $teori = Teori::with([
            'materi.course.bahasa',
            'materi.course.paket',
        ])->get();

        return response()->json([
            'data' => $teori,
        ]);
    }

    /**
     * GET /api/admin/teori/{id}
     */
    public function show($id)
    {
        $teori = Teori::with([
            'materi.course.bahasa',
            'materi.course.paket',
        ])->findOrFail($id);

        return response()->json([
            'data' => $teori,
        ]);
    }

    /**
     * POST /api/admin/teori
     * body:
     * {
     *   "id_materi": 4,
     *   "overview": "...",
     *   "kenapa_penting": "...",
     *   "konsep_dasar": "...",
     *   "contoh_praktik": "...",
     *   "ringkasan": "..."
     * }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id_materi' => 'required|exists:materi,id_materi',
            'overview' => 'nullable|string',
            'kenapa_penting' => 'nullable|string',
            'konsep_dasar' => 'nullable|string',
            'contoh_praktik' => 'nullable|string',
            'ringkasan' => 'nullable|string',
        ]);

        // Supaya 1 materi cuma punya 1 teori:
        $teori = Teori::updateOrCreate(
            ['id_materi' => $data['id_materi']], // key
            $data                                      // value yg diupdate
        );

        $teori->load('materi.course.bahasa', 'materi.course.paket');

        return response()->json([
            'message' => 'Teori disimpan',
            'data' => $teori,
        ], 201);
    }

    /**
     * PUT /api/admin/teori/{id}
     */
    public function update(Request $request, $id)
    {
        $teori = Teori::findOrFail($id);

        $data = $request->validate([
            'overview' => 'sometimes|nullable|string',
            'kenapa_penting' => 'sometimes|nullable|string',
            'konsep_dasar' => 'sometimes|nullable|string',
            'contoh_praktik' => 'sometimes|nullable|string',
            'ringkasan' => 'sometimes|nullable|string',
        ]);

        $teori->update($data);
        $teori->load('materi.course.bahasa', 'materi.course.paket');

        return response()->json([
            'message' => 'Teori diupdate',
            'data' => $teori,
        ]);
    }

    /**
     * DELETE /api/admin/teori/{id}
     */
    public function destroy($id)
    {
        $teori = Teori::findOrFail($id);
        $teori->delete();

        return response()->json([
            'message' => 'Teori dihapus',
        ]);
    }

    /**
     * GET /api/teori/{slug}
     * 👉 KHUSUS MEMBER (mirip KuisController::showBySlug)
     * - slug diambil dari judul materi
     * - contoh: "introduction-to-programming"
     */
    public function showBySlug($slug)
    {
        // slug kita sekarang formatnya: "5-grammar"
        $parts = explode('-', $slug, 2);
        $idMateri = (int) $parts[0];

        if (!$idMateri) {
            return response()->json([
                'status' => 'error',
                'message' => 'Slug tidak valid.',
            ], 400);
        }

        // ambil materi + relasi teori (lihat relasi di model Materi)
        $materi = Materi::with('teori')->find($idMateri);

        if (!$materi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Materi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'materi' => $materi,
                'teori' => $materi->teori,   // baris di tabel teori (id_materi = 5)
            ],
        ]);
    }
}
