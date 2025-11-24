<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RoleRedirectController;


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('home')
        : view('home');
});

Route::get('/home', RoleRedirectController::class)
    ->middleware('auth')
    ->name('home');

// Dashboard utama setelah login
Route::get('/dashboard', function () {
    $user = Auth::user();

    if (! $user) {
        return redirect()->route('login');
    }

    if ($user->role === 'parent') {
        // Orang tua murid -> lempar ke portal parent Sensipay
        return redirect()->route('sensipay.parent.dashboard');
    }

    // Owner / direksi / finance -> lempar ke dashboard admin Sensipay
    return redirect()->route('sensipay.invoices.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ROUTES MODUL
require base_path('routes/sensipay.php');
require base_path('routes/sensijet.php');
require base_path('routes/sensipay_invoice_reminders.php');

require __DIR__.'/auth.php';
