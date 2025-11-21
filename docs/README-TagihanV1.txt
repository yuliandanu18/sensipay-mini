Tagihan v1 Patch - Sensipay Mini
================================

Fitur:
------
1. Dashboard Orang Tua:
   - Ringkasan tagihan bulan ini (total, terbayar, sisa)
   - Tabel semua invoice dengan sisa tagihan dan status.

2. Halaman Admin Reminder:
   - URL: /sensipay/admin/invoice-reminders
   - Menampilkan semua invoice yang statusnya UNPAID / PARTIAL
   - Menyediakan template WhatsApp per baris (bisa di-copy)
   - Tombol "Buka WhatsApp (manual)" yang mengisi teks WA (nomor tujuan diisi sendiri).

File di patch:
--------------
- app/Http/Controllers/Sensipay/ParentDashboardController.php
- resources/views/sensipay/parent/dashboard.blade.php
- app/Http/Controllers/Sensipay/InvoiceReminderController.php
- resources/views/sensipay/admin/invoice_reminders.blade.php
- routes/sensipay_invoice_reminders.php
- docs/README-TagihanV1.txt

Cara pasang:
------------
1. Salin semua file patch ke dalam project Laravel (overwrite jika diminta).

2. Buka routes/web.php dan tambahkan:

   require base_path('routes/sensipay_invoice_reminders.php');

3. Jalankan:

   php artisan optimize:clear
   php artisan route:list | findstr invoice-reminders

4. Akses:

   - Parent:
       /sensipay/parent/dashboard

   - Admin/Owner:
       /sensipay/admin/invoice-reminders

Pastikan:
---------
- Sudah ada middleware "role" seperti yang dipakai di route dashboard owner:
    'role:owner,operational_director'
- Model Invoice punya relasi:
    student() dan program()
- Kolom "due_date", "total_amount", "paid_amount", "status" ada di tabel invoices.
