<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleRedirectController;
use Illuminate\Support\Facades\Auth;


// ======================
// ROOT: selalu arahkan ke login / dashboard
// ======================
Route::get('/', function () {
    // Kalau sudah login → lempar ke dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Kalau belum login → lempar ke halaman login standar
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = Auth::user();

    if (! $user) {
        return redirect()->route('login');
    }

 // Jika orang tua murid → lempar ke dashboard parent Sensipay
    if ($user->role === 'parent') {
        return redirect()->route('sensipay.parent.dashboard');
    }

     // Selain parent (owner, operational_director, academic_director, finance, dll)
    // lempar ke dashboard utama Sensipay (admin)
    return redirect()->route('sensipay.invoices.index');
})->middleware(['auth'])->name('dashboard');



Route::get('/', function () {
    return 'Hello Jet!';
});

Route::get('/home', RoleRedirectController::class)
    ->middleware('auth')
    ->name('home');
    

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
});
require base_path('routes/sensipay.php');
require base_path('routes/sensijet.php');
require base_path('routes/sensipay_parent_routes.php');
require base_path('routes/sensipay_invoice_reminders.php');

require __DIR__.'/auth.php';

