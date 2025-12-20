<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Update Profile Info (Name, Store, Address, Phone)
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'       => 'required|string|max:255',
            'store_name' => [
                'required', 'string', 'max:255',
                Rule::unique('users')->ignore($user->id), // Unique, but ignore current user
            ],
            'phone'      => [
                'nullable', 'string', 'max:15',
                Rule::unique('users')->ignore($user->id), // Unique, but ignore current user
            ],
            'address'    => 'nullable|string',
        ], [
            'store_name.unique' => 'Nama toko sudah digunakan oleh pengguna lain.',
            'phone.unique'      => 'Nomor telepon sudah terdaftar.',
        ]);

        $user->update([
            'name'       => $request->name,
            'store_name' => $request->store_name,
            'phone'      => $request->phone,
            'address'    => $request->address,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $user
        ]);
    }

    // Change Password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.min'              => 'Password baru minimal 6 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password saat ini salah.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.'
        ]);
    }
}