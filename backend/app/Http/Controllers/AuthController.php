<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);

        // Cek apakah user sudah teregistrasi
        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Jika user sudah terdaftar, langsung login
            return $this->login($request);
        }

        // Mendaftarkan user baru
        $user = new User();
        $user->email = $request->input('email');
        $user->role = 'customer';
        $user->save();

        return $this->login($request);
     
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Mencari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Jika user tidak ada, kembalikan error
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Data tidak valid'],
            ]);
        }

        // Secara otomatis login dan buat token
        $token = $user->createToken('jooragan')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }
        return response()->json(['message' => 'Anda berhasil logout']);
    }

    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }
}
