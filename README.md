# Sensipay Mini (Laravel Drop-in Module)

Mini module untuk mengelola **invoice & pembayaran** Bimbel JET.
Dirancang supaya:
- Bisa ditempel ke project Laravel 10 yang sudah ada
- Nanti gampang di-*patch* / di-*upgrade* (tinggal overwrite folder sensipay-mini)

## Isi Paket

- `routes/sensipay.php`  
- `app/Models/Student.php, Program.php, Invoice.php, Payment.php`  
- `app/Http/Controllers/Sensipay/*`  
- `database/migrations/*_sensipay_*.php`  
- `resources/views/sensipay/*`  
- `config/sensipay.php`

## Cara Pasang Singkat

1. **Copy folder** ini ke root project Laravel kamu.
2. Daftarkan route di `routes/web.php`:

```php
require base_path('routes/sensipay.php');
```

3. Jalankan migrasi:

```bash
php artisan migrate
```

4. Pastikan kamu sudah punya autentikasi (middleware `auth`).  
   Kalau belum, sementara bisa hapus `auth` dari middleware di `routes/sensipay.php`.

5. Akses di browser:

- `/sensipay/invoices` → daftar invoice
- Bisa buat invoice, input pembayaran, dan lihat riwayat pembayaran.

## Catatan

- Jika kamu sudah punya tabel `students` atau `programs` sendiri, silakan:
  - hapus/abaikan migration yang duplikat
  - sesuaikan relasi di model `Invoice` dan `Student`/`Program`.
- Semua angka masih simple (belum ada diskon, pajak, dsb).  
  Patch ke depan bisa menambahkan:
  - tipe pembayaran (cash/angsuran/QRIS)
  - *auto reminder* WA lewat Fonnte
  - integrasi Sensijet / JurnalJet


## Dummy Test Data (Sensipay DEV)

Untuk keperluan development & testing alur Sensipay (parent → pengajuan → admin approval), tersedia 1 perintah Artisan:

```bash
php artisan sensipay:seed-dummy
