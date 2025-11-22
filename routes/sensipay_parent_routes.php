<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensipay\ParentDashboardController;

Route::middleware(['auth', 'role:parent'])
    ->prefix('sensipay')
    ->as('sensipay.')
    ->group(function () {
        Route::get('parent/dashboard', [ParentDashboardController::class, 'index'])
            ->name('parent.dashboard');
    });
