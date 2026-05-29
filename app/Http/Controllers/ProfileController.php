<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $userId = session('user')['id'];

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email'
        ]);

        $user = User::findOrFail($userId);
        $user->update($validated);

        // update session juga
        session(['user' => $user]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);
    }
}
