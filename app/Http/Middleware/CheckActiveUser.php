<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next)
    {

        //  إذا كان مسجل دخول ولكن حسابه معطل
        if (! Auth::user()->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'حسابك معطل حالياً. يرجى التواصل مع الإدارة.');
        }

        return $next($request);
    }
}
