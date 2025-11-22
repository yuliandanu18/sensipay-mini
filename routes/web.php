<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleRedirectController;

Route::get('/', function () {
    return 'Hello Jet!';
});

Route::get('/home', RoleRedirectController::class)
    ->middleware('auth')
    ->name('home');
    
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

