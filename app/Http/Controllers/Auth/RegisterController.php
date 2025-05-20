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
            // 1) Validate everything
            $data = $request->validate([
                'name'              => 'required|string|max:255',
                'username'          => 'required|string|max:255|unique:users,username',
                'email'             => 'required|email|max:255|unique:users,email',
                'password'          => 'required|string|min:8|confirmed',
                'jabatan'           => 'nullable|string|max:100',
                'tgl_lahir'         => 'nullable|date',
                'tgl_masuk'         => 'nullable|date',
                'tempat_lahir'      => 'nullable|string|max:100',
                'no_telp'           => 'nullable|string|max:20',
            ]);

            // 2) Create (password mutator will hash automatically)
            User::create($data);

            // 3) Redirect & flash
            return redirect()
                ->route('login')
                ->with('success', 'Registration successful! Please log in.');
        }
}
