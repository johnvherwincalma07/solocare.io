<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{

    public function change(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // ❌ Wrong current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        // ✅ DO NOT HASH HERE
        $user->password = $request->new_password;
        $user->save();

        // 🔒 Logout after change
        Auth::logout();

        return redirect('/')
            ->with('status', 'Password changed successfully. Please login again.');
    }
}
