PATCH SENSIPAY V3 - IMPORT CUSTOMER + INVOICE LAMA

Catatan penting tentang 'admin' di URL:
- Route dashboard menggunakan path: /sensipay/admin/dashboard
- Kata 'admin' di sini hanya sebagai segmen URL dan nama route (sensipay.admin.dashboard),
  BUKAN nama role user. Role yang dipakai tetap:
    - owner
    - operational_director
    - academic_director
    - finance

Fitur patch ini:
- Import data customer (orang tua) dari CSV.
- Otomatis membuat akun user dengan role `student_parent` berbasis email.
- Menghasilkan password acak untuk tiap user baru dan ditampilkan di hasil import.
- Mengimport invoice lama dan mengaitkannya ke siswa & program yang sudah ada.

File yang disertakan:
- app/Http/Controllers/Sensipay/LegacyCustomerInvoiceImportController.php
- resources/views/sensipay/imports/legacy_customers_invoices.blade.php
- resources/views/sensipay/imports/legacy_customers_invoices_result.blade.php

FORMAT CSV YANG DISARANKAN (delimiter ; ):
parent_name;parent_email;student_name;program_name;total_amount;paid_amount;status;invoice_code
Bunda A;bunda.a@example.com;Andi;JET INTENSIF LABSCHOOL;5000000;2500000;unpaid;INV-001
Bunda B;bunda.b@example.com;Sinta;JET REGULER 7 SMP;3000000;3000000;paid;INV-002

LANGKAH PASANG PATCH:

1. Salin file-file berikut ke project Laravel Anda dengan struktur yang sama:
   - app/Http/Controllers/Sensipay/LegacyCustomerInvoiceImportController.php
   - resources/views/sensipay/imports/legacy_customers_invoices.blade.php
   - resources/views/sensipay/imports/legacy_customers_invoices_result.blade.php

2. Pastikan Anda sudah memiliki:
   - Model: User, Student, Program, Invoice, InvoiceItem
   - Helper StudentHelper (PATCH V2) untuk mencari siswa berdasarkan nama.

3. Tambahkan route berikut di routes/sensipay.php (di dalam group middleware sensipay):

   use App\Http\Controllers\Sensipay\LegacyCustomerInvoiceImportController;

   Route::get('legacy-import', [LegacyCustomerInvoiceImportController::class, 'showForm'])
       ->name('legacy-import.form');

   Route::post('legacy-import/process', [LegacyCustomerInvoiceImportController::class, 'process'])
       ->name('legacy-import.process');

4. Jalankan:
   php artisan optimize:clear

5. Akses halaman import di browser:
   /sensipay/legacy-import

   Setelah upload dan proses, sistem akan menampilkan:
   - Daftar user baru (email + password) untuk dibagikan ke orang tua.
   - Daftar user existing berdasarkan email.
   - Daftar invoice lama yang berhasil dibuat.
   - Daftar baris yang gagal diproses beserta alasan.

