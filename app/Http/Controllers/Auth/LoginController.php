<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info("xxxx");
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        Log::info($request['email']);
        $user = User::where('email', $request->email)->first();

        // Check if the password matches exactly without hashing
        if ($user && $user->password === $request->password) {

            Auth::login($user, $request->filled('remember'));
            return redirect('/dashboard')->with('success', 'Login successful!');
        }
        Log::info("Gagal Login");
        return redirect()->back()->with('error', 'Invalid username or password.');
    }
}
