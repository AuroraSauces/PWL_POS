<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;

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
        ]);

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
        ]);

        if ($request->has('password')) {
            $validatedData['password'] = $request->password;
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

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }
}
