<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules\Password as PasswordRule;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /**
     * Show the reset password form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->email;

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Handle the password reset.
     */
    public function reset(Request $request)
    {
        // Validate input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        // Use Laravel's built-in password broker
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Password reset successful — redirect with success
            return redirect('/')
                ->with('status', 'Password reset successful!');
        } else {
            // Token invalid or expired
            return back()->withErrors(['email' => __($status)]);
        }
    }
}
