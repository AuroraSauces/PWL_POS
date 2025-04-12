<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Fungsi untuk update foto profil
     */
    public function updatePhoto(Request $request)
    {
        // Validasi file yang diupload, harus gambar dengan ukuran maksimal 2MB
        $request->validate([
            'profile_photo' => 'required|image|max:2048', // Maksimal 2MB
        ]);

        // Ambil data user yang sedang login
        $user = Auth::user();

        // Hapus foto lama jika ada dan foto tersebut masih ada di storage
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            // Menghapus foto lama dari folder public storage
            Storage::disk('public')->delete($user->profile_photo);
        }

        try {
            // Upload file foto baru ke storage public dan simpan path-nya
            $path = $request->file('profile_photo')->store('profile-photos', 'public');

            // Update path foto profil di database
            $user->profile_photo = $path;

            // Simpan perubahan data user
            $user->save();

            // Mengarahkan kembali dengan pesan sukses
            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui');
        } catch (\Exception $e) {
            // Jika terjadi error dalam proses upload atau penyimpanan
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengupload foto profil.');
        }
    }
}
