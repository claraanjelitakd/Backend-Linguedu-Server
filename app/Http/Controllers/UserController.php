<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // $users = User::all();

        // return response()->json($users);
        // ikutkan registrasi terakhir (buat bukti_byr)
        $users = User::with('latestRegistrasi')->get();

        return response()->json($users);

    }
    // PATCH /api/admin/users/{id}/toggle-verify
    public function toggleVerify($id)
    {
        $user = User::findOrFail($id);

        // flip: kalau sudah ada email_verified_at → jadikan null, kalau null → now()
        $user->email_verified_at = $user->email_verified_at ? null : now();
        $user->save();

        return response()->json([
            'message' => 'Status verifikasi diubah',
            'user' => $user,
        ]);
    }

    // PUT /api/admin/users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'member'])],
            // dari FE kita kirim "is_active": true/false
            'is_active' => ['nullable', 'boolean'],
            // opsional: kalau admin mau ganti password dari halaman ini
            'password' => ['nullable', 'min:6'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];

        // handle status aktif (email_verified_at)
        if (array_key_exists('is_active', $data)) {
            $user->email_verified_at = $data['is_active'] ? now() : null;
        }

        // kalau password diisi, baru diubah
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'User berhasil diupdate',
            'user' => $user,
        ]);
    }
    // POST /api/admin/users  → untuk create dari halaman admin
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => ['required', Rule::in(['admin', 'member'])],
            'is_active' => 'nullable|boolean',
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = $data['role'];

        // aktif / belum aktif → mapping ke email_verified_at
        $user->email_verified_at = $request->boolean('is_active') ? now() : null;

        $user->save();

        return response()->json([
            'message' => 'User berhasil dibuat',
            'user' => $user,
        ], 201);
    }


    // DELETE /api/admin/users/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // opsional: jangan biarkan hapus diri sendiri / admin utama
        if ($user->role === 'admin' && $user->id == 1) {
            return response()->json([
                'message' => 'User admin utama tidak boleh dihapus'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus',
        ]);
    }
}

