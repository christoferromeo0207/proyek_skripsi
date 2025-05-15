<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    // Show the forgot password form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    // Send the password reset link to the user's email
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'username' => 'required|exists:users,username',
        ]);

        $status = Password::sendResetLink(
            $request->only('username')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Password reset link sent!');
        } else {
            return back()->withErrors(['username' => 'Username not found']);
        }
    }
}
