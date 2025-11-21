<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RoleRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        return match ($user->role) {
            'owner', 'operational_director' => redirect('/sensijet/dashboard-owner'),
            'academic_director'            => redirect('/sensijet/dashboard-academic'),
            'parent'                       => redirect()->route('sensipay.parent.dashboard'),
            default                        => redirect('/'),
        };
    }
}
