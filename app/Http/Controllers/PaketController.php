<?php

namespace App\Http\Controllers;

use App\Models\Paket;
use App\Models\Kursus;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    // GET /api/admin/paket
    public function index()
    {
        $paket = Paket::all();

        return response()->json([
            'data' => $paket,
        ]);
    }

    // GET /api/admin/paket/{id}
    public function show($id)
    {
        $paket = Paket::findOrFail($id);

        return response()->json([
            'data' => $paket,
        ]);
    }

    // POST /api/admin/paket
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_paket' => 'required|string|max:255|unique:paket,nama_paket',
            'desc'  => 'nullable|string',
            'harga'      => 'required|numeric|min:0',
        ]);

        $paket = Paket::create($data);

        return response()->json([
            'message' => 'Paket berhasil dibuat',
            'data'    => $paket,
        ], 201);
    }

    // PUT /api/admin/paket/{id}
    public function update(Request $request, $id)
    {
        $paket = Paket::findOrFail($id);

        $data = $request->validate([
            'nama_paket' => 'sometimes|required|string|max:255|unique:paket,nama_paket,' . $paket->id,
            'desc'  => 'nullable|string',
            'harga'      => 'sometimes|required|numeric|min:0',
        ]);

        $paket->update($data);

        return response()->json([
            'message' => 'Paket berhasil diupdate',
            'data'    => $paket,
        ]);
    }

    // DELETE /api/admin/paket/{id}
    public function destroy($id)
    {
        $paket = Paket::findOrFail($id);

        // Cek dulu apakah paket dipakai di tabel kursus
        $dipakai = Kursus::where('id_paket', $paket->id)->exists();
        if ($dipakai) {
            return response()->json([
                'message' => 'Paket tidak bisa dihapus karena masih digunakan di kursus.',
            ], 400);
        }

        $paket->delete();

        return response()->json([
            'message' => 'Paket berhasil dihapus',
        ]);
    }
}
