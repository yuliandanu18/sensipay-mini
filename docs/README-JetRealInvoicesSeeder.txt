JetRealInvoicesSeeder - Contoh Struktur Tagihan Asli Bimbel JET
=====================================================================

Seeder ini TIDAK berisi data asli, tapi STRUKTUR yang siap kamu isi
dengan nama orang tua, siswa, nominal, dan tanggal tagihan Bimbel JET
yang sebenarnya.

File di patch:
--------------
- database/seeders/JetRealInvoicesSeeder.php
- docs/README-JetRealInvoicesSeeder.txt

Yang dilakukan seeder ini:
--------------------------
1. Membuat beberapa Program (Reguler 6 SD, Intensif Labschool SD, Reguler 7 SMP)
   jika belum ada di tabel `programs`.

2. Membuat beberapa user role `parent` (parent1@jet.com, dst) jika belum ada.

3. Membuat Student yang dihubungkan dengan parent (kolom `parent_id` di `students`).

4. Membuat Invoice (record di tabel `invoices`) dengan:
   - invoice_code
   - student_id
   - program_id
   - total_amount
   - paid_amount
   - status
   - due_date

Cara pasang:
------------
1. Salin file ini ke:
   database/seeders/JetRealInvoicesSeeder.php

2. Jalankan:

   php artisan db:seed --class=Database\\Seeders\\JetRealInvoicesSeeder

3. Setelah seeding:
   - Cek di dashboard parent:
       /sensipay/parent/dashboard
   - Cek di admin reminder:
       /sensipay/admin/invoice-reminders

Kustomisasi data asli:
----------------------
Buka file:

   database/seeders/JetRealInvoicesSeeder.php

Lalu ubah array `$data`:

   - 'parent_name'   => isi nama orang tua asli
   - 'parent_email'  => email login parent (bisa kamu set)
   - 'student_name'  => nama siswa
   - 'school_grade'  => misal: '6 SD', '9 SMP'
   - 'program_code'  => sesuaikan dengan program yang kamu pakai
   - 'invoice_code'  => kode invoice sesuai formatmu
   - 'total_amount'  => nominal tagihan
   - 'paid_amount'   => nominal sudah dibayar (0 kalau belum)
   - 'status'        => 'unpaid' / 'partial' / 'paid'
   - 'due_date'      => tanggal jatuh tempo, format 'Y-m-d'

Password default untuk parent:
------------------------------
Semua parent yang dibuat di seeder ini diberi password:

   password

Silakan ubah manual di database atau di seeder sebelum dijalankan
jika ingin pakai password lain.
