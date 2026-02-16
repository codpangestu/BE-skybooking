<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index()
    {
        try {
            $users = User::paginate(10);
            return response()->json([
                'success' => true,
                'message' => 'Daftar pengguna berhasil dimuat',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar pengguna: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the role of a user.
     */
    public function updateRole(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:admin,passenger'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($id);

            if ($request->user()->id == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat mengubah role Anda sendiri.'
                ], 403);
            }

            $user->update(['role' => $request->role]);

            return response()->json([
                'success' => true,
                'message' => 'Role pengguna berhasil diperbarui menjadi ' . $request->role,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui role pengguna: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a user (Optional admin feature).
     */
    public function destroy($id, Request $request)
    {
        try {
            $user = User::findOrFail($id);

            if ($request->user()->id == $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus diri sendiri.'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pengguna: ' . $e->getMessage()
            ], 500);
        }
    }
}
