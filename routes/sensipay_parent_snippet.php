<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensipay\ParentDashboardController;
use App\Http\Controllers\Sensipay\LegacyInstallmentImportController;

/*
|--------------------------------------------------------------------------
| SNIPPET ROUTES SENSIPAY - PARENT & LEGACY INSTALLMENTS
|--------------------------------------------------------------------------
|
| File ini BUKAN route utama, hanya contoh snippet.
| Copy isinya ke dalam routes/sensipay.php Anda,
| di dalam group middleware sensipay yang sudah ada.
|
*/

Route::middleware(['web', 'auth'])
    ->prefix('sensipay')
    ->as('sensipay.')
    ->group(function () {

        // Dashboard Orang Tua Murid
        Route::get('parent/dashboard', [ParentDashboardController::class, 'index'])
            ->middleware('role:parent')
            ->name('parent.dashboard');

        // Import cicilan legacy
        Route::get('legacy-installments/import', [LegacyInstallmentImportController::class, 'showForm'])
            ->middleware('role:owner,operational_director,academic_director,finance')
            ->name('legacy-installments.import.form');

        Route::post('legacy-installments/import', [LegacyInstallmentImportController::class, 'import'])
            ->middleware('role:owner,operational_director,academic_director,finance')
            ->name('legacy-installments.import.process');
    });
