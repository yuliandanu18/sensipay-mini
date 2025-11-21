
JET Demo Seeder Patch v1
========================

Seeder ini akan:
- Membuat user demo:
    - owner@jet.com        (role: owner, password: password)
    - operational@jet.com  (role: operational_director, password: password)
    - academic@jet.com     (role: academic_director, password: password)
    - parent1@jet.com      (role: parent, password: password)

- Membuat siswa demo:
    - Nama: Dawud
    - Sekolah (jika ada kolom): SDN Larangan 5
    - Grade (jika ada kolom): 6 SD
    - parent_user_id → user parent1@jet.com (jika kolomnya ada)

- Membuat program demo (jika tabel programs ada):
    - Nama: Reguler 6 SD
    - level, description, default_sessions, base_price, is_active diisi jika kolomnya ada.

- Membuat invoice demo:
    - Untuk siswa Dawud, program Reguler 6 SD (jika ada)
    - total_amount: 10.000.000
    - status: unpaid
    - due_date: tanggal 20 bulan berjalan (jika kolom due_date ada).

Seeder ini dibuat defensif:
- Memakai Schema::hasTable dan Schema::hasColumn supaya tidak error walaupun struktur tabel sedikit berbeda.
- Jika tabel tertentu belum ada, bagian terkait akan di-skip dan ditandai lewat output seeder.

Cara instal:
-----------

1. Salin file:
   - database/seeders/JetDemoSeeder.php
   ke dalam proyek Laravel kamu (sensipay-mini).

2. Buka file:
   - database/seeders/DatabaseSeeder.php

   Tambahkan pemanggilan seeder ini di dalam method run():

       public function run(): void
       {
           // seeder lain (kalau ada) ...

           $this->call([
               JetDemoSeeder::class,
           ]);
       }

3. Jalankan seeder:

   Dari root folder proyek:

       php artisan db:seed

   atau kalau mau spesifik:

       php artisan db:seed --class=Database\\Seeders\\JetDemoSeeder

4. Setelah selesai, kamu bisa login dengan akun:

   - Owner:
        Email:    owner@jet.com
        Password: password

   - Direktur Operasional:
        Email:    operational@jet.com
        Password: password

   - Direktur Akademik:
        Email:    academic@jet.com
        Password: password

   - Orang Tua:
        Email:    parent1@jet.com
        Password: password

5. Parent dashboard:

   Setelah login sebagai parent1@jet.com, akses:

       /sensipay/parent/dashboard

   Harusnya tampil data invoice demo untuk Dawud (jika parent dashboard sudah kamu buat).
