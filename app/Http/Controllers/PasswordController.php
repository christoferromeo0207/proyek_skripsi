<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    // Menampilkan form forgot password
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    // Memeriksa apakah username valid
    public function checkUsername(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,username',
        ]);

        // Jika username valid, arahkan ke form reset password
        return view('auth.reset_password', ['username' => $request->username]);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required|confirmed|min:8', 
        ]);

        // Temukan user berdasarkan username
        $user = User::where('username', $request->username)->first();

        // Simpan password baru tanpa hashing
        $user->password = $request->password;
        $user->save();

        // Redirect ke login dengan pesan sukses
        return redirect()->route('/')->with('status', 'Password berhasil diperbarui!');
    }
}
