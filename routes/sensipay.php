<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensipay\InvoiceController;
use App\Http\Controllers\Sensipay\PaymentController;
use App\Http\Controllers\Sensipay\StudentFinanceController;
use App\Http\Controllers\Sensipay\ParentDashboardController;
use App\Http\Controllers\Sensipay\ReminderController;
use App\Http\Controllers\Sensipay\InvoiceImportController;
use App\Http\Controllers\Sensipay\LegacyCustomerInvoiceImportController;
use App\Http\Controllers\Sensipay\LegacyInstallmentImportController;

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
Route::middleware(['web', 'auth', 'role:owner,operational_director,academic_director,finance'])
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
    Route::get('invoices/import', [InvoiceImportController::class, 'showForm'])
            ->name('invoices.import.form');

        Route::post('invoices/import/preview', [InvoiceImportController::class, 'preview'])
            ->name('invoices.import.preview');

        Route::post('invoices/import/process', [InvoiceImportController::class, 'process'])
            ->name('invoices.import.process');
              Route::get('legacy-import', [LegacyCustomerInvoiceImportController::class, 'showForm'])
            ->name('legacy-import.form');

        Route::post('legacy-import/process', [LegacyCustomerInvoiceImportController::class, 'process'])
            ->name('legacy-import.process');
             Route::get('legacy-installments/import', [LegacyInstallmentImportController::class, 'showForm'])
            ->name('legacy-installments.import.form');

        Route::post('legacy-installments/process', [LegacyInstallmentImportController::class, 'process'])
            ->name('legacy-installments.import.process');
            
    });



