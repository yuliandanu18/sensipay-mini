<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensipay\InvoiceController;
use App\Http\Controllers\Sensipay\PaymentController;
use App\Http\Controllers\Sensipay\StudentFinanceController;
use App\Http\Controllers\Sensipay\ParentDashboardController;
use App\Http\Controllers\Sensipay\ReminderController;

Route::prefix('sensipay')
    ->as('sensipay.')
    ->group(function () {

        // ... route sensipay lain (invoices, payments, dll)

      Route::get('sensipay/parent/dashboard', [ParentDashboardController::class, 'index'])
    ->name('sensipay.parent.dashboard');
    });

// TEST: pastikan file ini ke-load
Route::get('/sensipay/ping', function () {
    return 'sensipay ok';
});

// ROUTE UTAMA SENSIPAY
Route::middleware(['web'])
    ->prefix('sensipay')
    ->as('sensipay.')
    ->group(function () {
          // 👉 Halaman keuangan per siswa
        Route::get('/students/{student}/finance', [StudentFinanceController::class, 'show'])
            ->name('students.finance');

        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
Route::get('/students/{student}/finance', [StudentFinanceController::class, 'show'])
    ->name('sensipay.student.finance');
        Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::get('sensipay/reminders', [ReminderController::class,'index'])
    ->name('sensipay.reminders.index');
    });



