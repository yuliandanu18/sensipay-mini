PATCH SENSIPAY V2 - IMPORT INVOICE & DASHBOARD

Isi zip ini:
- app/Helpers/StudentHelper.php
- app/Http/Controllers/Sensipay/InvoiceImportController.php
- app/Http/Controllers/Sensipay/AdminDashboardController.php
- resources/views/sensipay/invoices/import.blade.php
- resources/views/sensipay/invoices/import_preview.blade.php
- resources/views/sensipay/admin/dashboard.blade.php

LANGKAH PASANG:

1. Backup project sensipay-mini Anda.
2. Salin semua file ke dalam folder Laravel dengan struktur yang sama.
3. Pastikan namespace model (Student, Program, Invoice, InvoiceItem) sudah sesuai.
4. Tambahkan route berikut di routes/sensipay.php (atau sesuaikan):

   use App\Http\Controllers\Sensipay\InvoiceImportController;
   use App\Http\Controllers\Sensipay\AdminDashboardController;

   Route::middleware(['web','auth','role:owner,operational_director,academic_director,finance'])
       ->prefix('sensipay')
       ->as('sensipay.')
       ->group(function () {
           Route::get('admin/dashboard', [AdminDashboardController::class, 'index'])
               ->name('admin.dashboard');

           Route::get('invoices/import', [InvoiceImportController::class, 'showForm'])
               ->name('invoices.import.form');

           Route::post('invoices/import/preview', [InvoiceImportController::class, 'preview'])
               ->name('invoices.import.preview');

           Route::post('invoices/import/process', [InvoiceImportController::class, 'process'])
               ->name('invoices.import.process');
       });

5. Jalankan:
   php artisan optimize:clear

6. Akses:
   - /sensipay/invoices/import        -> import & preview invoice
   - /sensipay/admin/dashboard        -> dashboard sensipay

