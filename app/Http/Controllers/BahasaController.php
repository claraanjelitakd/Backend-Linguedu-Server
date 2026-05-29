<?php

namespace App\Http\Controllers;

use App\Models\Bahasa;
use Illuminate\Http\Request;

class BahasaController extends Controller
{
    // GET /api/admin/bahasa
    public function index()
    {
        $bahasa = Bahasa::orderBy('id', 'asc')->get();

        return response()->json([
            'data' => $bahasa,
        ]);
    }

    // POST /api/admin/bahasa
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_bahasa' => 'required|string|max:255',
            'desc' => 'nullable|string',   // ⬅️ pakai desc
        ]);

        $bahasa = Bahasa::create($data);

        return response()->json([
            'message' => 'Bahasa created',
            'data' => $bahasa,
        ], 201);
    }

    // PUT /api/admin/bahasa/{id}
    public function update(Request $request, $id)
    {
        $bahasa = Bahasa::findOrFail($id);

        $data = $request->validate([
            'nama_bahasa' => 'sometimes|required|string|max:255',
            'desc' => 'nullable|string',   // ⬅️ pakai desc
        ]);

        $bahasa->update($data);

        return response()->json([
            'message' => 'Bahasa updated',
            'data' => $bahasa,
        ]);
    }

    // DELETE /api/admin/bahasa/{id}
    public function destroy($id)
    {
        $bahasa = Bahasa::findOrFail($id);

        // FK di kursus sudah ON DELETE SET NULL, jadi aman dihapus
        $bahasa->delete();

        return response()->json([
            'message' => 'Bahasa deleted',
        ]);
    }
}
