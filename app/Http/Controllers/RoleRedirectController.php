<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RoleRedirectController extends Controller
{
   public function __invoke(Request $request)
{
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return view('home'); // yang tadi sudah kita desain
}

}
