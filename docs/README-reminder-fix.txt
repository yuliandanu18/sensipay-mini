Patch: Perbaikan Fatal error namespace di ReminderController

File ini akan menggantikan:
  app/Http/Controllers/Sensipay/ReminderController.php

Penyebab error:
  "Namespace declaration statement has to be the very first statement..."
biasanya karena:
  - ada spasi / karakter sebelum `<?php`
  - ada teks lain sebelum deklarasi namespace

Solusi:
  - Timpa file ReminderController lama dengan file di patch ini.
  - Pastikan tidak ada karakter sebelum `<?php` di baris pertama.

Setelah menyalin:
  - Jalankan: php artisan route:list
    untuk memastikan error fatal sudah hilang.
