<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Sensipay\InvoiceController;
use App\Http\Controllers\Sensipay\PaymentController;
use App\Http\Controllers\Sensipay\StudentFinanceController;
use App\Http\Controllers\Sensipay\ReminderController;
use App\Http\Controllers\Sensipay\InvoiceImportController;
use App\Http\Controllers\Sensipay\LegacyCustomerInvoiceImportController;
use App\Http\Controllers\Sensipay\LegacyInstallmentImportController;
use App\Http\Controllers\Sensipay\ParentManagementController;
use App\Http\Controllers\Sensipay\ParentPaymentController;
use App\Http\Controllers\Sensipay\PaymentApprovalController;
use App\Http\Controllers\Sensipay\ParentDashboardController;

// TEST: ping sensipay (tanpa middleware apa-apa)
Route::get('/sensipay/ping', function () {
    return 'sensipay ok';
});

// ==============================
// ADMIN / INTERNAL JET
// ==============================
Route::middleware(['web', 'auth', 'role:owner,operational_director,academic_director,finance'])
    ->prefix('sensipay')
    ->as('sensipay.')
    ->group(function () {

        // Ping dalam group (optional)
        Route::get('/ping', fn () => 'sensipay ok')->name('ping');

        // ===== FINANCE SISWA =====
        Route::get('/students/{student}/finance', [StudentFinanceController::class, 'show'])
            ->name('students.finance');

        // ===== INVOICE & PEMBAYARAN INTERNAL =====
        Route::resource('invoices', InvoiceController::class);

        Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])
            ->name('payments.store');

        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
            ->name('payments.destroy');

        // ===== APPROVAL PEMBAYARAN ORANG TUA (ADMIN) =====
        Route::get('/payments', [PaymentApprovalController::class, 'index'])
            ->name('payments.index');

        Route::post('/payments/{payment}/approve', [PaymentApprovalController::class, 'approve'])
            ->name('payments.approve');   // full: sensipay.payments.approve

        Route::post('/payments/{payment}/reject', [PaymentApprovalController::class, 'reject'])
            ->name('payments.reject');    // full: sensipay.payments.reject

        // ===== PENGINGAT =====
        Route::get('/reminders', [ReminderController::class, 'index'])
            ->name('reminders.index');

        // ===== IMPORT =====
        Route::get('/invoices/import', [InvoiceImportController::class, 'showForm'])
            ->name('invoices.import.form');
        Route::post('/invoices/import/preview', [InvoiceImportController::class, 'preview'])
            ->name('invoices.import.preview');
        Route::post('/invoices/import/process', [InvoiceImportController::class, 'process'])
            ->name('invoices.import.process');

        Route::get('/legacy-import', [LegacyCustomerInvoiceImportController::class, 'showForm'])
            ->name('legacy-import.form');
        Route::post('/legacy-import/process', [LegacyCustomerInvoiceImportController::class, 'process'])
            ->name('legacy-import.process');

        Route::get('/legacy-installments/import', [LegacyInstallmentImportController::class, 'showForm'])
            ->name('legacy-installments.import.form');
        Route::post('/legacy-installments/import', [LegacyInstallmentImportController::class, 'import'])
            ->name('legacy-installments.import.process');

        // ===== PARENTS MANAGEMENT =====
        Route::resource('parents', ParentManagementController::class);

        Route::post('/parents/{parent}/attach-invoice', [ParentManagementController::class, 'attachInvoice'])
            ->name('parents.attach-invoice');

        Route::delete('/parents/{parent}/invoices/{invoice}', [ParentManagementController::class, 'detachInvoice'])
            ->name('parents.detach-invoice');
    });

// ==============================
// PORTAL ORANG TUA
// ==============================
Route::middleware(['web', 'auth', 'role:parent'])
    ->prefix('sensipay/parent')
    ->as('sensipay.parent.')
    ->group(function () {

        Route::get('/dashboard', [ParentDashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('/invoices/{invoice}/pay', [ParentPaymentController::class, 'store'])
            ->name('invoices.pay');
    });
