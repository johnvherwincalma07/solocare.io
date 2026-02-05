<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPasswordOtpMail; // Make sure this Mailable exists

class ForgotPasswordController extends Controller
{
    // Step 1: Show the forgot password form
    public function showRequestForm()
    {
        return view('auth.forgot-password'); // Blade for entering email
    }

    // Step 2: Send OTP to user email
public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $user = User::where('email', $request->email)->first();

    $otp = rand(100000, 999999);

    $user->otp = $otp;
    $user->otp_expires_at = now()->addMinutes(10);
    $user->save();

    Mail::to($user->email)->send(new ForgotPasswordOtpMail($otp));

    session(['password_reset_email' => $user->email]);

    // 🔥 Return to same modal page so success message appears
    return redirect()->route('password.request')
                     ->with('otp_sent', true)
                     ->with('status', 'OTP sent successfully');
}


    // Step 3: Show OTP verification form
    public function showOtpForm()
    {
        $email = session('password_reset_email'); // Retrieve email from session
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Please enter your email first.']);
        }
        return view('auth.otp-verify', compact('email'));
    }

    // Step 4: Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('otp_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP'])->withInput();
        }

        // OTP is valid → redirect to reset password page
        $token = base64_encode($user->email); // Simple token for demo
        return redirect()->route('password.reset', ['token' => $token]);
    }

    // Step 5: Show reset password form
    public function showResetForm($token)
    {
        $email = base64_decode($token); // Decode email from token
        return view('auth.reset-password', compact('email', 'token'));
    }

    // Step 6: Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->password = $request->password; // Mutator in User model hashes it
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Clear session email
        session()->forget('password_reset_email');

        return redirect()->route('home')->with('reset_success', 'Password reset successfully! You can now login.');
    }
}
