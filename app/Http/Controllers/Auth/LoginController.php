<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request)
    {
        // 1) Validate input
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2) Fetch user by username
        $user = User::where('username', $data['username'])->first();

        // 3) Verify password
        if ($user && $user->password === $data['password']) {
            // 4) Log them in
            Auth::login($user, $request->filled('remember'));

           
            switch ($user->role) {
                case 'mitra':
                    return redirect()->route('mitra.dashboard');
                case 'marketing':
                    return redirect()->route('marketing.dashboard');
                case 'admin':
                    return redirect()->route('dashboard');
                default:
                    return redirect('/');
            }
        }

        // 6) On failure
        return redirect()->back()
                         ->withInput($request->only('username'))
                         ->with('error', 'Invalid username or password.');
    }
}
