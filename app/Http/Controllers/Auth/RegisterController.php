<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        // Validate the registration input
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the new user and hash the password
        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password, 
        ]);

        // Redirect to the login page after registration
        return redirect()->route('login')->with('success', 'Registration successful, please log in!');
    }
}
