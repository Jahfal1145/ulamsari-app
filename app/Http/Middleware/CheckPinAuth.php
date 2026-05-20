<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPinAuth
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (!session("auth_{$role}")) {
            session(['pin_role' => $role]);
            return redirect()->route('pin.show');
        }

        return $next($request);
    }
}