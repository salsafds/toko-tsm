<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User; 

class ProfileController extends Controller
{
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();
        return view('layouts.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'username' => 'sometimes|required|string|max:100|unique:users,username,' . $user->id_user . ',id_user',
            'foto_user' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'required|min:6|confirmed';
        }

        $request->validate($rules);

        $data = [];

        if ($request->filled('username') && $request->username !== $user->username) {
            $data['username'] = $request->username;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_user')) {
            if ($user->foto_user) {
                Storage::delete('public/foto_user/' . $user->foto_user);
            }
            $file = $request->file('foto_user');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/foto_user', $filename);
            $data['foto_user'] = $filename;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
    public function checkUsername(Request $request)
    {
        $username = $request->username;
        $currentUserId = Auth::id();

        $exists = DB::table('users')
            ->where('username', $username)
            ->where('id_user', '!=', $currentUserId)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function verifyOldPassword(Request $request)
    {
        $request->validate(['old_password' => 'required']);
        $user = Auth::user();

        return response()->json([
            'valid' => Hash::check($request->old_password, $user->password)
        ]);
    }
}