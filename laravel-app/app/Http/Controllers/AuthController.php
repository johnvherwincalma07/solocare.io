<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by username (case-insensitive)
        $user = User::whereRaw('LOWER(username) = ?', [strtolower($request->username)])->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            // --- LOG SUCCESS LOGIN ---
            AuditLog::create([
                'user'   => $user->first_name . ' ' . $user->last_name,
                'action' => "Logged in to the system",
                'module' => 'Authentication',
                'status' => 'Success'
            ]);

            // Redirect based on role
            if ($user->role === 'super_admin') {
                return redirect()->route('super.admin.dashboard');
            } elseif ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }

        // --- LOG FAILED LOGIN ---
        AuditLog::create([
            'user'   => $request->username, // we don’t have first/last for failed login
            'action' => "Attempted to log in with invalid credentials",
            'module' => 'Authentication',
            'status' => 'Failed'
        ]);

        return redirect()->back()
                        ->with('login_error', 'Invalid username or password.')
                        ->withInput($request->only('username'));
    }

    // Logout user
    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user'   => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                'action' => "Logged out of the system",
                'module' => 'Authentication',
                'status' => 'Success'
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
