<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController as ControllersUserController;
use App\Http\Controllers\BahasaController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\KursusController;
use App\Http\Controllers\KuisController;
use App\Http\Controllers\HasilTesController;
use App\Http\Controllers\UjiSertifikasiController;
use App\Http\Controllers\SoalSertifikasiController;
// use App\Http\Controllers\SoalPaketController;
use App\Http\Controllers\TemplateSertifikatController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeoriController;
use App\Http\Controllers\HasilSertifikasiController;



// Cek API
Route::get('/check', function () {
    return response()->json(['message' => 'API is working']);
});
Route::get('admin/dashboard/summary', [AdminDashboardController::class, 'summary']);
Route::get('/teori/{slug}', [TeoriController::class, 'showBySlug']);

// Auth public
Route::post('/register', [AuthController::class, 'register']);
// Registrasi kursus (ini yang dipakai FE)
Route::post('/registrasi', [AuthController::class, 'registrasiKursus']);
Route::post('/login', [AuthController::class, 'login']);
// Data master
Route::get('/paket', [AuthController::class, 'paket']);
Route::get('/bahasa', [AuthController::class, 'bahasa']);

// ==========================================
// 🔓 PUBLIC ROUTES (UNTUK MEMBER/FRONTEND)
// ==========================================
Route::get('/materi/{slug}', [MateriController::class, 'showBySlug']);
Route::get('/member/video/{slug}', [MateriController::class, 'showBySlug']);
Route::get('/kuis/{slug}', [KuisController::class, 'showBySlug']);
Route::get('/member/materi-list', [MateriController::class, 'getMateriForMember']);
Route::post('/member/level-up', [MateriController::class, 'levelUp']);
// TAMBAHAN BARU: API Submit Kuis
Route::post('/kuis/{id_kuis}/submit', [HasilTesController::class, 'submit']);

// ================= MEMBER – UJIAN SERTIFIKASI =================
// Ambil soal sertifikasi untuk member yang login
// ===== MEMBER – SERTIFIKASI =====
Route::get('/sertifikasi/soal', [HasilSertifikasiController::class, 'getSoalForMember']);
Route::post('/sertifikasi/{kode_tes}/submit', [HasilSertifikasiController::class, 'submit']);

// Versi API
Route::prefix('/admin')->group(function () {
    // ===== ADMIN – Uji Sertifikasi =====
    Route::get('sertifikasi/tes', [UjiSertifikasiController::class, 'index']);
    Route::post('sertifikasi/tes', [UjiSertifikasiController::class, 'store']);
    Route::delete('sertifikasi/tes/{kode_tes}', [UjiSertifikasiController::class, 'destroy']);
    Route::put('sertifikasi/tes/{kode_tes}', [UjiSertifikasiController::class, 'update']);


    // ===== ADMIN – Soal Sertifikasi =====
    Route::get('sertifikasi/soal', [SoalSertifikasiController::class, 'index']);
    Route::get('sertifikasi/soal/{kode_tes}', [SoalSertifikasiController::class, 'byKodeTes']);
    Route::post('sertifikasi/soal', [SoalSertifikasiController::class, 'store']);
    Route::post('sertifikasi/soal/add', [SoalSertifikasiController::class, 'addSoal']);
    Route::put('sertifikasi/soal/{id_soal}', [SoalSertifikasiController::class, 'update']);
    Route::delete('sertifikasi/soal/{id_soal}', [SoalSertifikasiController::class, 'destroy']);

    Route::get('hasil-sertifikasi', [HasilSertifikasiController::class, 'index']);

    Route::get('/kursus', [AuthController::class, 'kursus']);
    Route::get('kursus', [KursusController::class, 'index']);
    Route::get('materi', [MateriController::class, 'index']);
    Route::post('materi', [MateriController::class, 'store']);
    Route::put('materi/{id_materi}', [MateriController::class, 'update']);
    Route::delete('materi/{id_materi}', [MateriController::class, 'destroy']);
    Route::get('materi/filter', [MateriController::class, 'filter']);

    // KUIS
    Route::get('kuis', [KuisController::class, 'index']);
    Route::get('kuis/{id_kuis}', [KuisController::class, 'show']);
    Route::post('kuis', [KuisController::class, 'store']);
    Route::post('kuis/{id_kuis}/soal', [KuisController::class, 'addSoal']);
    Route::put('soal-kuis/{id}', [KuisController::class, 'updateSoal']);
    Route::delete('soal-kuis/{id}', [KuisController::class, 'deleteSoal']);
    Route::delete('kuis/{id_kuis}', [KuisController::class, 'destroy']);

    // HASIL TES
    Route::get('hasil-tes', [HasilTesController::class, 'index']);
    Route::post('kuis/{id_kuis}/submit', [HasilTesController::class, 'submit']);
    //user
    Route::get('users', [UserController::class, 'index']);
    Route::get('/status', function () {
        return response()->json(['version' => 'v1', 'status' => 'ok']);
    });
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::patch('users/{id}/toggle-verify', [UserController::class, 'toggleVerify']);
    Route::post('users', [UserController::class, 'store']);
    // === PAKET CRUD ===
    Route::get('paket', [PaketController::class, 'index']);
    Route::get('paket/{id}', [PaketController::class, 'show']);
    Route::post('paket', [PaketController::class, 'store']);
    Route::put('paket/{id}', [PaketController::class, 'update']);
    Route::delete('paket/{id}', [PaketController::class, 'destroy']);

    // === BAHASA CRUD ===
    Route::get('bahasa', [BahasaController::class, 'index']);
    Route::get('bahasa/{id}', [BahasaController::class, 'show']);
    Route::post('bahasa', [BahasaController::class, 'store']);
    Route::put('bahasa/{id}', [BahasaController::class, 'update']);
    Route::delete('bahasa/{id}', [BahasaController::class, 'destroy']);
    // Template Sertifikat
    Route::get('template-sertifikat', [TemplateSertifikatController::class, 'showByCourse']);
    Route::post('template-sertifikat', [TemplateSertifikatController::class, 'store']);
    Route::delete('template-sertifikat/{id}', [TemplateSertifikatController::class, 'destroy']);
    // === TEORI CRUD ===
    Route::get('teori', [TeoriController::class, 'index']);
    Route::get('teori/{id}', [TeoriController::class, 'show']);
    Route::post('teori', [TeoriController::class, 'store']);
    Route::put('teori/{id}', [TeoriController::class, 'update']);
    Route::delete('teori/{id}', [TeoriController::class, 'destroy']);
    // Protected routes (harus login pakai Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', function (Request $request) {
            return response()->json($request->user());
        });

        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // EDIT PROFILE
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
    });

});
