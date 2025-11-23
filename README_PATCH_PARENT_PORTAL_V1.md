# PATCH: Parent Portal & Payment v1 (Sensipay Mini)

Isi patch ini:

- `routes/sensipay.php`
  - Memisahkan route admin dan route parent:
    - Admin (owner, operational_director, academic_director, finance) di prefix `/sensipay`
    - Parent (orang tua murid) di prefix `/sensipay/parent`
- `routes/web.php`
  - Mengarahkan `/dashboard` ke:
    - `/sensipay/parent/dashboard` jika role = `parent`
    - `/sensipay/invoices` jika role admin
- `app/Http/Controllers/Sensipay/ParentPaymentController.php`
  - Controller untuk menerima input pembayaran dari portal parent
  - Menyimpan ke tabel `payments` dan mengupdate `invoice.paid_amount` + `status`
- `resources/views/sensipay/layout.blade.php`
  - Layout utama Sensipay (sidebar + navbar)
  - Sidebar menyesuaikan role (parent vs admin)
- `resources/views/sensipay/parent/dashboard.blade.php`
  - Dashboard orang tua:
    - Menampilkan daftar invoice
    - Menampilkan sisa tagihan
    - Menampilkan detail angsuran (jika ada relasi `installments`)
    - Form "Bayar Tagihan Bulan Ini"

## Cara Apply (Manual)

1. Backup dulu file-file berikut di project Anda:
   - `routes/sensipay.php`
   - `routes/web.php`
   - `resources/views/sensipay/layout.blade.php`
   - `resources/views/sensipay/parent/dashboard.blade.php` (jika sudah ada)
   - `app/Http/Controllers/Sensipay/ParentPaymentController.php` (jika sudah ada)

2. Ekstrak ZIP ini ke root project Laravel:
   - Pastikan struktur folder sama:
     - `routes/sensipay.php`
     - `routes/web.php`
     - `app/Http/Controllers/Sensipay/ParentPaymentController.php`
     - `resources/views/sensipay/layout.blade.php`
     - `resources/views/sensipay/parent/dashboard.blade.php`

3. Jalankan:
   - `php artisan route:clear`
   - `php artisan config:clear`

4. Tes alur:
   - Login sebagai parent (role = `parent`)
   - Harus diarahkan ke `/sensipay/parent/dashboard`
   - Harus melihat list invoice, sisa tagihan, dan form pembayaran
   - Isi nominal dan submit
   - Cek:
     - Tabel `payments` terisi
     - `invoices.paid_amount` bertambah
     - `invoices.status` berubah (`unpaid` → `partial` → `paid`)

Jika ada file lokal yang sudah banyak modifikasi, lakukan merge manual dengan diff tool.
