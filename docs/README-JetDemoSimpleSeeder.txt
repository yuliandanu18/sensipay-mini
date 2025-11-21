JetDemoSimpleSeeder v2
======================

Ini versi SEEDER PALING AMAN untuk Sensipay-mini:

Yang dibuat:
-----------
- 3 user:
    - owner@jet.com        (role: owner, password: password)
    - academic@jet.com     (role: academic_director, password: password)
    - parent1@jet.com      (role: parent, password: password)

Tidak menyentuh:
---------------
- Tabel program
- Tabel students
- Tabel invoices
- Tabel payments
- Tabel classes / kbm_sessions
Jadi kecil kemungkinan error karena beda struktur kolom.

Cara pakai:
-----------
1. Salin file:
    database/seeders/JetDemoSimpleSeeder.php

2. Buka:
    database/seeders/DatabaseSeeder.php

   Tambahkan di fungsi run():

        public function run(): void
        {
            // seeder lain...
            $this->call(\Database\Seeders\JetDemoSimpleSeeder::class);
        }

3. Jalankan:

    php artisan db:seed --class=Database\\Seeders\\JetDemoSimpleSeeder

   atau kalau sudah dimasukkan ke DatabaseSeeder:

    php artisan db:seed

4. Login testing:

    - Owner:
        email    : owner@jet.com
        password : password

    - Academic:
        email    : academic@jet.com
        password : password

    - Parent:
        email    : parent1@jet.com
        password : password

Jika sebelumnya JetDemoSeeder v1 error:
--------------------------------------
- Hapus / komentar dulu pemanggilan JetDemoSeeder di DatabaseSeeder.
- Pakai hanya JetDemoSimpleSeeder sampai sistem stabil.
