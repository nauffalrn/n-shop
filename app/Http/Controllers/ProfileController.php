<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show_profile()
    {
        $user = Auth::user();
        return view('user.show_profile', compact('user')); 
    }

    public function edit_profile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:8|confirmed',
            'current_password' => 'required_with:password'
        ]);

        $user = User::find(Auth::id());

        // Validasi password lama jika ingin ganti password
        if($request->password) {
            if(!Hash::check($request->current_password, $user->password)) {
                return Redirect::back()->with('error', 'Password lama tidak benar!');
            }
        }

        // Handle avatar upload
        $avatarPath = $user->avatar;
        if($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if($user->avatar) {
                Storage::disk('local')->delete('public/' . $user->avatar);
            }
            
            $file = $request->file('avatar');
            $avatarPath = time() . '_avatar_' . $user->id . '.' . $file->getClientOriginalExtension();
            Storage::disk('local')->put('public/' . $avatarPath, file_get_contents($file));
        }

        // Update user
        $updateData = [
            'name' => $request->name,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'avatar' => $avatarPath
        ];

        if($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return Redirect::back()->with('success', 'Profile berhasil diupdate!');
    }
}