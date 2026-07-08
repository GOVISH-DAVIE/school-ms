<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAccountantMiddleware
{
    /**
     * Allow both Admin (role 2) and Accountant (role 4) — finance module access.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && in_array($user->role_id, [2, 4]) && $user->account_status != 'disable') {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Access denied or your account is disabled.');
    }
}
