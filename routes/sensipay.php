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


Route::middleware(['web', 'auth', 'role:owner,operational_director,academic_director,finance'])
    ->prefix('sensipay')
    ->as('sensipay.')
    ->group(function () {

        Route::get('/ping', fn() => 'sensipay ok')->name('ping');

        Route::get('/students/{student}/finance', [StudentFinanceController::class, 'show'])
            ->name('students.finance');

        Route::resource('invoices', InvoiceController::class);

        Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])
            ->name('payments.store');

        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
            ->name('payments.destroy');

        Route::get('/reminders', [ReminderController::class, 'index'])
            ->name('reminders.index');

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

        Route::resource('parents', ParentManagementController::class);

        Route::post('/parents/{parent}/attach-invoice', [ParentManagementController::class, 'attachInvoice'])
            ->name('parents.attach-invoice');

        Route::delete('/parents/{parent}/invoices/{invoice}', [ParentManagementController::class, 'detachInvoice'])
            ->name('parents.detach-invoice');
    });

Route::middleware(['web', 'auth', 'role:parent'])
    ->prefix('sensipay/parent')
    ->as('sensipay.parent.')
    ->group(function () {

        Route::get('/dashboard', 
            [\App\Http\Controllers\Sensipay\ParentDashboardController::class, 'index']
        )->name('dashboard');

        Route::post('/invoices/{invoice}/pay', 
            [ParentPaymentController::class, 'store']
        )->name('invoices.pay');
    });
