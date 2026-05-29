<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Paket;
use App\Models\Bahasa;
use App\Models\Kursus;
use App\Models\RegistrasiKursus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function paket()
    {
        $paket = Paket::all();

        return response()->json([
            'data' => $paket
        ]);
    }
    public function bahasa(Request $request)
    {
        // id_paket dikirim via query string: /bahasa?id_paket=1
        $bahasa = Bahasa::all();

        return response()->json([
            'data' => $bahasa
        ]);
    }
    // /api/registrasi  (dipanggil FE saat step 3 submit)
    public function registrasiKursus(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'id_paket' => 'required|exists:paket,id',
            'id_bahasa' => 'required|exists:bahasa,id',
            'metode_bayar' => 'required|string',
            'bukti_byr' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // 1. User baru (member)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'member',
            ]);

            // 2. Ambil paket & bahasa
            $paket = Paket::findOrFail($request->id_paket);
            $bahasa = Bahasa::findOrFail($request->id_bahasa);

            // 3. Cari / buat kursus (kombinasi paket + bahasa)
            $course = Kursus::firstOrCreate(
                [
                    'id_paket' => $paket->id,
                    'id_bahasa' => $bahasa->id,
                ],
                [
                    'deskripsi' => "Kursus {$bahasa->nama_bahasa} - Paket {$paket->nama_paket}",
                ]
            );

            $course->load('paket', 'bahasa');

            $totalBayar = $course->paket->harga;

            // 4. Ambil admin
            $admin = User::where('role', 'admin')->first();
            $idAdmin = $admin?->id ?? null;

            // 5. Upload bukti bayar
            $pathBukti = null;
            if ($request->hasFile('bukti_byr')) {
                $pathBukti = $request->file('bukti_byr')
                    ->store('foto_bukti', 'public'); // storage/app/public/foto_bukti
            }

            // 6. Simpan ke registrasi_kursus
            $registrasi = RegistrasiKursus::create([
                'id_admin' => $idAdmin,
                'id_member' => $user->id,
                'id_course' => $course->id_course,
                'tgl_trans' => now(),
                'metode_bayar' => $request->metode_bayar,
                'total_byr' => $totalBayar,
                'bukti_byr' => $pathBukti,
                'progress' => 0,
                'level' => $paket->nama_paket,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Registrasi berhasil',
                'user' => $user,
                'kursus' => $course,      // sudah include bahasa & paket
                'registrasi' => $registrasi,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat registrasi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member',       // << Tambah ini
        ]);

        return response()->json([
            'message' => 'Register success',
            'user' => $user
        ], 201);
    }

    public function kursus(Request $request)
    {
        $request->validate([
            'id_paket' => 'required|exists:paket,id', // sesuaikan kalau PK paket beda
        ]);

        $idPaket = $request->query('id_paket');

        $kursus = Kursus::with(['bahasa', 'paket'])
            ->where('id_paket', $idPaket)
            ->get();

        return response()->json([
            'data' => $kursus
        ]);
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        // cek email atau password salah
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
        // Hanya MEMBER yang wajib aktif
        if (($user->role === 'member' || $user->role === null) && is_null($user->email_verified_at)) {
            return response()->json([
                'message' => 'Akun Anda belum aktif. Silakan hubungi admin.'
            ], 403);
        }


        // generate token
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout success']);
    }
}
