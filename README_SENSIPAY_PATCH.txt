PATCH SENSIPAY - PARENT PORTAL & APPROVAL

Isi patch ini:
- app/Models/ParentModel.php
- app/Http/Controllers/Sensipay/ParentPaymentController.php
- app/Http/Controllers/Sensipay/PaymentApprovalController.php
- resources/views/sensipay/payments/index.blade.php

LANGKAH INTEGRASI (MANUAL):

1. Copy file-file ini ke project Laravel Anda dengan struktur yang sama.

2. Tambahkan relasi parent di model Student:
   - File: app/Models/Student.php

   public function parent()
   {
       return $this->belongsTo(\App\Models\ParentModel::class, 'parent_id');
   }

3. Pastikan tabel-tabel berikut sudah ada:
   - Tabel parents (kolom: id, name, phone, email, address, notes, timestamps)
   - Kolom parent_id di tabel students
   - Kolom parent_user_id di tabel invoices

4. Tambahkan route untuk halaman approval payments & submit parent portal:

   use App\Http\Controllers\Sensipay\PaymentApprovalController;
   use App\Http\Controllers\Sensipay\ParentPaymentController;

   Route::middleware(['web', 'auth', 'role:owner,operational_director,academic_director,finance'])
       ->prefix('sensipay')
       ->as('sensipay.')
       ->group(function () {
           Route::get('/payments', [PaymentApprovalController::class, 'index'])
               ->name('payments.index');
           Route::patch('/payments/{payment}/approve', [PaymentApprovalController::class, 'approve'])
               ->name('payments.approve');
           Route::patch('/payments/{payment}/reject', [PaymentApprovalController::class, 'reject'])
               ->name('payments.reject');
       });

   // Untuk parent portal (contoh):
   Route::middleware(['web', 'auth'])
       ->prefix('sensipay')
       ->as('sensipay.')
       ->group(function () {
           Route::post('/invoices/{invoice}/parent-payment', [ParentPaymentController::class, 'store'])
               ->name('parent-payment.store');
       });

5. Sesuaikan nama layout di resources/views/sensipay/payments/index.blade.php
   Jika layout Anda bukan 'layouts.app', ganti dengan layout yang digunakan Sensipay.

6. Setelah copy:
   - php artisan optimize:clear

Catatan:
- ParentPaymentController sudah menangani:
  - normalisasi nominal (500.000, 500,000, dll -> 500000)
  - aturan sisa > 1jt vs <= 1jt
  - status payment = pending (invoice dibayar setelah APPROVE)
