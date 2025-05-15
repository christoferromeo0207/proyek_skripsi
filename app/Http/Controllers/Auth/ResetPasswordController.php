<?php



namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    // Show the reset password form
    public function showResetForm($token)
    {
        return view('auth.reset_password', ['token' => $token]);
    }

    // Handle password update
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        $status = Password::reset(
            $request->only('password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->password = Hash::make($request->password);
                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('/')->with('status', 'Password reset successful');
        } else {
            return back()->withErrors(['email' => 'Failed to reset password']);
        }
    }
}
