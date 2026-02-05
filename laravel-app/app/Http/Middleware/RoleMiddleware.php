<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            // Not logged in → redirect to the login page
            return redirect()->route('login.form')->with('error', 'Please login first.');
        }

        if (Auth::user()->role !== $role) {
            // Logged in but wrong role → stop, don't redirect to another page
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
