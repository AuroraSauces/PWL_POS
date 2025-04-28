<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserModel; // ✅ Gunakan UserModel, bukan User

class AuthController extends Controller
{
    public function login()
    {
        // Jika sudah login, redirect ke home
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function postlogin(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $credentials = $request->only('username', 'password');
            
            if (Auth::attempt($credentials)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'redirect' => url('/')
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Login Gagal'
            ]);
        }

        return redirect('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('login');
    }

    // ✅ TAMBAHKAN FUNGSI REGISTER (DIPERBAIKI)
    public function postRegister(Request $request)
{
    $request->validate([
        'username' => 'required|string|min:3|unique:m_user,username',
        'nama'     => 'required|string|max:100',
        'password' => 'required|min:5',
        'level_id' => 'required|integer'
    ]);

    // Simpan user ke database
    $user = UserModel::create([
        'username'  => $request->username,  // Remove 'reg_' prefix
        'nama'      => $request->nama,      // Add this line
        'password'  => $request->password,  // Remove 'reg_' prefix
        'level_id'  => $request->level_id,  // Remove 'reg_' prefix
    ]);

    if ($user) {
        return response()->json([
            'status' => true,
            'message' => 'Pendaftaran Berhasil! Silakan login.'
        ]);
    }

    return response()->json([
        'status' => false,
        'message' => 'Pendaftaran Gagal!'
    ]);
}
}
