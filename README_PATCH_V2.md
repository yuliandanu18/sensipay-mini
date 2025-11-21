# Sensipay Mini - Patch V2 (Role + Mini Sensijet)

Patch ini menambahkan:

1. **Role user** di tabel `users`:
   - Migration: `add_role_to_users_table`
   - Middleware: `RoleMiddleware`
   - Contoh role: `owner`, `operational_director`, `academic_director`, `teacher`, `parent`, `user`

2. **Proteksi akses Sensipay**:
   - `routes/sensipay.php` sekarang menggunakan middleware: `role:owner,operational_director`
   - Hanya owner & direktur operasional yang bisa kelola invoice & pembayaran.

3. **Mini Sensijet - Struktur KBM**:
   - Tabel:
     - `classes` → data kelas (quota 40 sesi, guru, program, dll)
     - `kbm_sessions` → jadwal & sesi KBM (regular, private, make-up, bonus)
     - `kbm_attendance` → absensi siswa per sesi
     - `session_charges` → tagihan extra untuk sesi private / over-quota
   - Model:
     - `ClassRoom`
     - `KbmSession`
     - `KbmAttendance`
     - `SessionCharge`
   - Route:
     - `routes/sensijet.php` (belum ada controllernya, ini fondasi dulu)

## Cara Pakai Patch

1. **Ekstrak isi zip** ini ke root project Laravel.
   - File akan menambah:
     - migration baru
     - middleware baru
     - models baru
     - routes baru (`sensijet.php`) + update `sensipay.php`

2. **Daftarkan middleware role** di `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ...
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
```

3. **Daftarkan routes baru** di `routes/web.php`:

```php
require base_path('routes/sensipay.php');  // kalau belum
require base_path('routes/sensijet.php');  // untuk jadwal & absensi KBM
```

4. **Jalankan migrasi**:

```bash
php artisan migrate
```

5. **Atur role user** (misal via tinker / seeder / form admin):

```php
$user = \App\Models\User::find(1);
$user->role = 'owner';
$user->save();
```

Sesuaikan:
- Danu  → `operational_director`
- Miss Anis → `academic_director`
- Guru → `teacher`
- Ortu → `parent`
- Default lain → `user`

Setelah ini:
- Modul keuangan Sensipay **aman** (hanya 2 role yang bisa utak-atik).
- Fondasi jadwal & absensi (Sensijet mini) sudah siap untuk ditambahkan controller & Blade UI.
