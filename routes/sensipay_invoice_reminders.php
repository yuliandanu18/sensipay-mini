<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensipay\InvoiceReminderController;

/*
|--------------------------------------------------------------------------
| Routes reminder tagihan Sensipay (admin / owner)
|--------------------------------------------------------------------------
|
| Panggil file ini dari routes/web.php:
|
|   require base_path('routes/sensipay_invoice_reminders.php');
|
*/

Route::middleware(['web', 'auth', 'role:owner,operational_director,academic_director'])
    ->prefix('sensipay/admin')
    ->as('sensipay.admin.')
    ->group(function () {
        Route::get('invoice-reminders', [InvoiceReminderController::class, 'index'])
            ->name('invoice-reminders.index');
    });
