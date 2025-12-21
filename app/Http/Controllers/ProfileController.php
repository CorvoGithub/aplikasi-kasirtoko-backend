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
            'store_name' => 'required|string|max:255|unique:users,store_name,'.$user->id,
            'phone'      => 'nullable|string|max:15',
            'address'    => 'nullable|string',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validation for image
        ]);

        // Handle Image Upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists (optional, but good practice)
            // if ($user->avatar && Storage::exists('public/' . $user->avatar)) {
            //    Storage::delete('public/' . $user->avatar);
            // }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $request->name;
        $user->store_name = $request->store_name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $user // Returns updated user with avatar path
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