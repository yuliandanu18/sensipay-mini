<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/dashboard', function () {
    $user = Auth::user();

    if (! $user) {
        return redirect()->route('login');
    }

    if ($user->role === 'parent') {
        return redirect()->route('sensipay.parent.dashboard');
    }

    return redirect()->route('sensipay.invoices.index');
})->middleware(['auth'])->name('dashboard');

Route::get('/', function () {
    return 'Hello Jet!';
});

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
