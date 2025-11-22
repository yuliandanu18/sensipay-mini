
SENSIPAY-MINI OTM / PARENT DASHBOARD PATCH
=========================================

Generated: 2025-11-22T01:05:49.946184

Patch ini berisi file-file untuk:
- Dashboard Orang Tua Murid (ParentDashboardController + view)
- Controller LegacyInstallmentImportController dengan method showForm() + import() kerangka dasar
- Migration untuk menambah kolom parent_user_id di tabel invoices
- Contoh snippet route untuk parent & legacy installments

Struktur patch (relatif terhadap root project sensipay-mini):
- app/Http/Controllers/Sensipay/ParentDashboardController.php
- app/Http/Controllers/Sensipay/LegacyInstallmentImportController.php   (replace / lengkapi yang lama)
- database/migrations/2025_11_22_000000_add_parent_user_id_to_invoices_table.php
- resources/views/sensipay/parent/dashboard.blade.php
- resources/views/sensipay/legacy-installments/import.blade.php
- routes/sensipay_parent_snippet.php   (HANYA SNIPPET, jangan langsung dipakai sebagai file route utama)

LANGKAH PAKAI PATCH INI
-----------------------
1. BACKUP PROJECT
   - Copy dulu folder sensipay-mini kamu ke lokasi aman sebagai backup.

2. EXTRACT ZIP KE ROOT PROJECT
   - Ekstrak ZIP ini langsung ke folder:
     D:\proyekutama\sensipay-mini
   - Biarkan folder & file bercampur, karena path-nya sudah disusun mengikuti struktur Laravel.

3. CEK / MERGE ROUTE
   - Buka file: routes/sensipay_parent_snippet.php
   - Copy isi route group di dalamnya
   - Paste / merge ke: routes/sensipay.php
     (letakkan di dalam group middleware sensipay yang sudah ada)

4. JALANKAN MIGRATION
   - php artisan migrate

  Migration ini hanya menambah kolom:
  - invoices.parent_user_id (nullable unsignedBigInteger) + index

5. UJI COBA
   - Login sebagai user dengan role = 'parent' (nanti akan dibuat lewat import atau manual)
   - Akses: /sensipay/parent/dashboard
   - Form import legacy: /sensipay/legacy-installments/import

CATATAN PENTING
---------------
- Method import() di LegacyInstallmentImportController masih bersifat KERANGKA (skeleton).
  Kamu perlu menyesuaikan mapping kolom sesuai format file Excel / CSV kamu.
- Secara default, import() akan:
  - Validasi file
  - Parse CSV sederhana dengan header
  - Untuk setiap baris: mencari/ membuat user parent, dan membuat invoice kosong sebagai contoh.
  - Bagian mapping diberi komentar "TODO" agar mudah disesuaikan.

- Dashboard parent menampilkan semua invoice dengan parent_user_id = auth()->id(),
  jadi saat import kamu perlu mengisi kolom parent_user_id pada tabel invoices.

Kalau ada error saat pakai patch ini, kirim stack trace + potongan kode yang relevan,
nanti kita bedah bareng lagi.
