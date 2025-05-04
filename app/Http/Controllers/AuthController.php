<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserModel;

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

    public function postRegister(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:3|unique:m_user,username',
            'nama'     => 'required|string|max:100',
            'password' => 'required|min:5',
            'level_id' => 'required|integer'
        ]);

        // Simpan user ke database dengan password terenkripsi
        $user = UserModel::create([
            'username'  => $request->username,
            'nama'      => $request->nama,
            'password'  => bcrypt($request->password), // Menggunakan bcrypt untuk konsistensi dengan API
            'level_id'  => $request->level_id,
            'image'     => 'Default_pfp.jpg', // Menambahkan gambar default
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

    // Fungsi untuk memperbarui password yang belum terenkripsi
    public function fixLegacyPasswords()
    {
        $users = UserModel::all();
        $count = 0;

        foreach ($users as $user) {
            // Cek apakah password belum di-hash (panjang kurang dari 40 karakter biasanya)
            if (strlen($user->password) < 40) {
                $user->password = bcrypt($user->password);
                $user->save();
                $count++;
            }
        }

        return "Updated passwords for {$count} users";
    }
}
