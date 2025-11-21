<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensipay\ParentDashboardController;

/*
|--------------------------------------------------------------------------
| Routes khusus parent Sensipay
|--------------------------------------------------------------------------
|
| Jangan lupa panggil file ini dari routes/web.php:
|
|   require base_path('routes/sensipay_parent_routes.php');
|
*/

Route::middleware(['web', 'auth'])
    ->prefix('sensipay')
    ->as('sensipay.')
    ->group(function () {
        Route::get('parent/dashboard', [ParentDashboardController::class, 'index'])
            ->name('parent.dashboard');
    });
