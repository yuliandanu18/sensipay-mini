<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // role user harus salah satu dari parameter middleware:
        // contoh: role:owner,operational_director,academic_director,finance
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Tidak punya akses.');
        }

        return $next($request);
    }
}
