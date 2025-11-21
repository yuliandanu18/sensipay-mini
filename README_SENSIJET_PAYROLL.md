# Patch Sensijet Payroll (Rekap Gaji Guru)

Patch ini menambahkan modul **Payroll Guru** berbasis data sesi KBM (`kbm_sessions`):

## Fitur

- Halaman: `GET /sensijet/payroll/teachers`
- Filter bulan (input type `month`, format `YYYY-MM`)
- Rekap per guru:
  - Total sesi
  - Sesi reguler vs private
  - Total menit
  - Total fee (dari kolom `teacher_fee`)
- Total keseluruhan di bagian footer
- Link "Payroll Guru" muncul di navbar Sensijet untuk role:
  - `owner`
  - `operational_director`
  - `academic_director`

Perhitungan fee:
- Menggunakan kolom `teacher_fee` dari setiap baris `kbm_sessions`
- Secara default (dari model KbmSession yang sebelumnya dibuat):
  - Jika `duration_minutes` ada dan `teacher_fee` kosong:
    - `teacher_fee = duration_minutes * 1000`
  - Contoh:
    - 90 menit (reguler) -> 90.000
    - 45 menit (private) -> 45.000
- Nilai ini bisa disesuaikan per sesi jika diperlukan.

## File yang Ditambahkan / Diubah

1. **Controller baru**
   - `app/Http/Controllers/Sensijet/TeacherPayrollController.php`

2. **View baru**
   - `resources/views/sensijet/payroll/index.blade.php`

3. **Update layout Sensijet**
   - `resources/views/sensijet/layout.blade.php`
   - Menambahkan link menu "Payroll Guru" di navbar.

## Cara Pasang Patch

1. Ekstrak isi zip ke root project Laravel (folder yang sama dengan `app/`, `routes/`, dll).
   - Biarkan struktur folder seperti ini:
     - `app/Http/Controllers/Sensijet/TeacherPayrollController.php`
     - `resources/views/sensijet/payroll/index.blade.php`
     - `resources/views/sensijet/layout.blade.php`

2. Tambahkan route di `routes/sensijet.php` (jika belum ada):

```php
use App\Http\Controllers\Sensijet\TeacherPayrollController;

Route::middleware(['web'])
    ->prefix('sensijet')
    ->as('sensijet.')
    ->group(function () {

        // ... route lain Sensijet (classes, sessions, dsb.)

        Route::get('payroll/teachers', [TeacherPayrollController::class, 'index'])
            ->name('payroll.teachers.index');
    });
```

> Catatan: Jika nanti auth + role sudah diaktifkan lagi, route ini bisa dibatasi dengan middleware `role:owner,operational_director,academic_director`.

3. Bersihkan cache route:

```bash
php artisan route:clear
php artisan cache:clear
php artisan route:list
```

Pastikan ada route:

- `sensijet/payroll/teachers` → `sensijet.payroll.teachers.index`

4. Jalankan dan uji di browser:

- Buka: `http://127.0.0.1:8000/sensijet/payroll/teachers`
- Isi parameter bulan (jika ingin ganti) via form di kanan atas.

Jika sudah ada data di `kbm_sessions`:
- Rekap per guru akan tampil otomatis.
- Jika belum ada, halaman akan menampilkan pesan "Belum ada sesi KBM pada periode ini.".

Dengan modul ini:
- Direktur Akademik & Operasional bisa melihat beban mengajar dan estimasi gaji guru per bulan tanpa hitung manual.
- Owner bisa melihat total fee guru sebagai salah satu komponen biaya operasional bulanan Bimbel JET.
