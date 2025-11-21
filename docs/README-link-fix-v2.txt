
PATCH v2: Perbaikan "link not found" untuk dashboard-owner & parent dashboard

1. File baru: routes/sensipay_parent_routes.php
   ------------------------------------------------
   - Berisi route:
        - sensipay.parent.dashboard
        - sensipay.parents.index / edit / update
   - Semua diprefix dengan /sensipay dan middleware auth.

   Cara pakai:
   - Buka routes/web.php
   - Tambahkan:

        require base_path('routes/sensipay_parent_routes.php');

2. File baru: routes/sensijet_dashboard_routes.php
   ------------------------------------------------
   - Berisi route:
        - sensijet.dashboard.owner  -> /sensijet/dashboard-owner

   Cara pakai:
   - Di routes/web.php tambahkan juga:

        require base_path('routes/sensijet_dashboard_routes.php');

3. Pastikan RoleRedirectController mengarah ke route yang benar:
   --------------------------------------------------------------
   Di app/Http/Controllers/RoleRedirectController.php pastikan:

        return match ($user->role) {
            'owner', 'operational_director' =>
                redirect()->route('sensijet.dashboard.owner'),

            'academic_director' =>
                redirect('/sensijet/dashboard-academic'), // atau route lain milikmu

            'parent' =>
                redirect()->route('sensipay.parent.dashboard'),

            default =>
                redirect('/'),
        };

   Catatan:
   - Untuk owner/operational pakai name route: sensijet.dashboard.owner
   - Untuk parent pakai: sensipay.parent.dashboard

4. Pastikan file ini di-load
   --------------------------
   Setelah menambah require di routes/web.php:

        require base_path('routes/sensijet_dashboard_routes.php');
        require base_path('routes/sensipay_parent_routes.php');

   Jalankan:

        php artisan optimize:clear
        php artisan route:list | findstr dashboard-owner
        php artisan route:list | findstr parent.dashboard

   Jika muncul di daftar, maka "link not found" untuk URL tersebut harusnya sudah hilang.
