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
    use App\Http\Controllers\Sensipay\ParentPaymentProofController;
    use App\Http\Controllers\Sensipay\AdminPaymentProofController;

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

            // DEV: generate dummy (LOCAL ONLY) – logicnya sudah dijaga di controller
            Route::post('/invoices/generate-dummy', [InvoiceController::class, 'generateDummy'])
                ->name('invoices.generate-dummy');
            Route::post('/invoices/reset-dummy', [InvoiceController::class, 'resetDummy'])
                ->name('invoices.reset-dummy');

            // ===== INVOICES & REKALKULASI =====
            Route::resource('invoices', InvoiceController::class);

            Route::post('/invoices/{invoice}/recalc-status', [InvoiceController::class, 'recalcStatus'])
                ->name('invoices.recalc-status');

            // ===== HALAMAN ADMIN PEMBAYARAN PER INVOICE =====
            Route::get('invoices/{invoice}/payments', [PaymentController::class, 'index'])
                ->name('invoices.payments.index');

            // ===== PEMBAYARAN INTERNAL (ADMIN/FINANCE SAJA) =====
            Route::post('/invoices/{invoice}/payments', [PaymentController::class, 'store'])
                ->name('payments.store');

            Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
                ->name('payments.destroy');

            // ===== APPROVAL PEMBAYARAN ORANG TUA (ADMIN) =====
            Route::get('/payments', [PaymentApprovalController::class, 'index'])
                ->name('payments.index');

            Route::post('/payments/{payment}/approve', [PaymentApprovalController::class, 'approve'])
                ->name('payments.approve');

            Route::post('/payments/{payment}/reject', [PaymentApprovalController::class, 'reject'])
                ->name('payments.reject');

            // ===== FINANCE SISWA =====
            Route::get('/students/{student}/finance', [StudentFinanceController::class, 'show'])
                ->name('students.finance');

            // ===== PENGINGAT =====
            
    // Halaman list reminder (INI SUDAH ADA, hanya biar kelihatan utuh)
            Route::get('reminders', [ReminderController::class, 'index'])
                ->name('reminders.index');

            // Kirim reminder untuk SATU invoice
            Route::post('reminders/send/{invoice}', [ReminderController::class, 'sendSingle'])
                ->name('reminders.send-single');

            // Kirim reminder untuk SEMUA invoice jatuh tempo
            Route::post('reminders/send-bulk', [ReminderController::class, 'sendBulk'])
                ->name('reminders.send-bulk');
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
                  // ===== PAYMENT PROOF MANAGEMENT =====
                
                Route::get('payment-proofs', [AdminPaymentProofController::class, 'index'])
            ->name('payment-proofs.index');

        Route::get('payment-proofs/{proof}', [AdminPaymentProofController::class, 'show'])
            ->name('payment-proofs.show');

        Route::post('payment-proofs/{proof}/approve', [AdminPaymentProofController::class, 'approve'])
            ->name('payment-proofs.approve');

        Route::post('payment-proofs/{proof}/reject', [AdminPaymentProofController::class, 'reject'])
            ->name('payment-proofs.reject');
        });

    // ==============================
    // PORTAL ORANG TUA
    // ==============================
    Route::middleware(['web', 'auth', 'role:parent'])
        ->prefix('sensipay/parent')
        ->as('sensipay.parent.')
        ->group(function () {

            // Dashboard ringkasan tagihan
            Route::get('/dashboard', [ParentDashboardController::class, 'index'])
                ->name('dashboard');

            // Detail 1 invoice untuk orang tua
            Route::get('/invoices/{invoice}', [ParentPaymentController::class, 'show'])
                ->name('invoices.show');

            // Ortu mengajukan pembayaran / upload bukti
            Route::post('/invoices/{invoice}/pay', [ParentPaymentController::class, 'store'])
                ->name('invoices.pay');

                // Ortu mengajukan pembayaran / upload bukti {PROF WHATSAPP}
Route::get('invoices/{invoice}/upload-proof', [ParentPaymentProofController::class, 'create'])
            ->name('invoices.upload-proof');

        Route::post('invoices/{invoice}/upload-proof', [ParentPaymentProofController::class, 'store'])
            ->name('invoices.upload-proof.store');
            // (Kalau nanti mau: route riwayat pengajuan, dll, bisa ditambah di sini)
        });
