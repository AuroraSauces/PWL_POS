<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        return UserModel::all();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:m_user,username',
            'password' => 'required|string|min:5',
            'level_id' => 'required|exists:m_level,level_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Hash password
        $validatedData['password'] = bcrypt($validatedData['password']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();
            $validatedData['image'] = $imageName;

            // Store image in profile-photos directory
            $image->store('public/profile-photos');
        } else {
            // Use default profile picture
            $validatedData['image'] = 'Default_pfp.jpg';
        }

        $user = UserModel::create($validatedData);
        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = UserModel::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = UserModel::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $validatedData = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:m_user,username,' . $user->user_id . ',user_id',
            'level_id' => 'sometimes|exists:m_level,level_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->has('password')) {
            $validatedData['password'] = bcrypt($request->password);
        }

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if it's not the default one
            if ($user->image !== 'Default_pfp.jpg' && Storage::exists('public/profile-photos/' . $user->image)) {
                Storage::delete('public/profile-photos/' . $user->image);
            }

            $image = $request->file('image');
            $imageName = $image->hashName();
            $validatedData['image'] = $imageName;

            // Store the new image
            $image->store('public/profile-photos');
        }

        $user->update($validatedData);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = UserModel::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        // Delete user's image if it's not the default one
        if ($user->image !== 'Default_pfp.jpg' && Storage::exists('public/profile-photos/' . $user->image)) {
            Storage::delete('public/profile-photos/' . $user->image);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }
}
