# Patch: Parent Portal Payment (v1)

Isi patch ini:

1. `app/Http/Controllers/Sensipay/ParentPaymentController.php`
   - Menangani POST pembayaran dari dashboard orang tua.
   - Aturan:
     - Minimal pembayaran rutin: Rp 1.000.000
     - Harus kelipatan Rp 50.000
     - Kecuali sisa tagihan <= Rp 1.000.000 → wajib pelunasan (amount == sisa)
     - Amount tidak boleh melebihi sisa tagihan
   - Simpan `Payment` baru (`method = parent-portal`, `note` berisi ref + path bukti).
   - Update `invoice->paid_amount` dan `status` (via `recalcStatus()` kalau ada).
   - Kirim notifikasi ke admin:
     - Email ke `JET_ADMIN_EMAIL`
     - WA ke `JET_ADMIN_WA` via Fonnte (`config('services.fonnte.*')`).

2. `routes/sensipay_parent_payment.php`
   - Route:
     - `POST /sensipay/parent/invoices/{invoice}/pay`
     - Name: `sensipay.parent.invoices.pay`
     - Middleware: `web`, `auth`, `role:parent`

3. `resources/views/sensipay/parent/dashboard.blade.php`
   - Menampilkan:
     - Ringkasan invoice
     - Sisa tagihan
     - Tabel angsuran (kalau ada relasi `installments`)
     - Tabel riwayat pembayaran (`payments`)
     - Form "Bayar Tagihan Bulan Ini" per invoice:
       - Input nominal
       - No referensi
       - Upload bukti transfer

## Langkah Integrasi

1. Ekstrak zip ini di root project Laravel `sensipay-mini` dan izinkan overwrite untuk
   file `resources/views/sensipay/parent/dashboard.blade.php`.

2. Tambahkan require route baru di `routes/web.php`:

   ```php
   require base_path('routes/sensipay_parent_payment.php');
   ```

   Letakkan berdekatan dengan require file routes sensipay lain.

3. Pastikan model `Payment` memiliki field berikut di database:
   - `invoice_id` (unsigned big int)
   - `amount` (numeric)
   - `paid_at` (datetime nullable)
   - `method` (string nullable)
   - `note` (text/string nullable)

   Patch ini hanya memakai kolom-kolom tersebut.

4. Setup env untuk notifikasi:

   ```env
   JET_ADMIN_EMAIL=admin@example.com
   JET_ADMIN_WA=62812xxxxxxx

   FONNTE_TOKEN=your_fonnte_token
   FONNTE_URL=https://api.fonnte.com/send
   ```

   Dan di `config/services.php`:

   ```php
   'fonnte' => [
       'token'         => env('FONNTE_TOKEN'),
       'url'           => env('FONNTE_URL', 'https://api.fonnte.com/send'),
       'admin_number'  => env('JET_ADMIN_WA'),
   ],
   ```

5. Jalankan:

   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan cache:clear
   ```

Setelah itu, orang tua bisa:
- Login ke portal
- Lihat sisa tagihan & riwayat pembayaran
- Mengirim konfirmasi pembayaran dengan nominal sesuai aturan,
  plus no referensi dan bukti transfer.
