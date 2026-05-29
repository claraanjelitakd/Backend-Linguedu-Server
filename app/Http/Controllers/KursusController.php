<?php

namespace App\Http\Controllers;

use App\Models\Kursus;
use Illuminate\Http\Request;

class KursusController extends Controller
{
    // GET /api/admin/kursus
    public function index()
    {
        // include relasi bahasa & paket biar label di FE bisa: "Inggris - Intermediate"
        $kursus = Kursus::with(['bahasa', 'paket'])->get();

        return response()->json([
            'data' => $kursus,
        ]);
    }
}
