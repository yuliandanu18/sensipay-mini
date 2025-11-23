# Patch Jalur 1 – Portal Orang Tua + Konfirmasi Pembayaran

Isi patch ini:
- Tambahan kolom `status` dan `proof_path` di tabel `payments`.
- Update `App\Models\Payment`.
- Update `App\Http\Controllers\Sensipay\ParentPaymentController`.
- Controller baru: `App\Http\Controllers\Sensipay\PaymentApprovalController`.
- Update `routes/sensipay.php` untuk route admin konfirmasi pembayaran.
- Update layout dan dashboard parent:
  - `resources/views/sensipay/layout.blade.php`
  - `resources/views/sensipay/parent/dashboard.blade.php`
- View baru admin:
  - `resources/views/sensipay/payments/index.blade.php`

## Cara pakai

1. Ekstrak ZIP ini ke folder project Laravel Anda.
   - File di dalam `app/`, `routes/`, `resources/`, dan `database/` boleh dioverwrite.
2. Jalankan migrasi:
   ```bash
   php artisan migrate
   ```
3. Pastikan sudah ada symbolic link untuk storage (agar bukti transfer bisa diakses):
   ```bash
   php artisan storage:link
   ```
4. Login sebagai role admin (owner/operational_director/academic_director/finance),
   lalu buka menu **Konfirmasi Pembayaran** di sidebar Sensipay.

Business rule:
- Sisa > 1.000.000 → minimal bayar 1.000.000 dan kelipatan 50.000.
- Sisa ≤ 1.000.000 → wajib pelunasan penuh.
- Pembayaran dari parent masuk sebagai `payments.status = 'pending'`.
- Invoice baru dianggap bertambah `paid_amount` setelah admin menekan tombol **Setujui** di halaman admin.