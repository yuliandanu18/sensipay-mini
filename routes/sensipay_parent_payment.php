<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensipay\ParentPaymentController;

// Rute khusus parent untuk membuat pembayaran manual dari dashboard.
Route::middleware(['web', 'auth', 'role:parent'])
    ->prefix('sensipay/parent')
    ->as('sensipay.parent.')
    ->group(function () {
        Route::post('invoices/{invoice}/pay', [ParentPaymentController::class, 'store'])
            ->name('invoices.pay');
    });
