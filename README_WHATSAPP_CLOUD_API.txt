WAJET x Sensipay-mini - Patch WhatsApp Cloud API

1. Tambahkan ke .env project Laravel kamu:

   WHATSAPP_CLOUD_API_TOKEN=ISI_DENGAN_TOKEN_DARI_META
   WHATSAPP_CLOUD_API_PHONE_ID=100889272934607
   WHATSAPP_CLOUD_API_VERSION=v22.0
   WHATSAPP_TEST_NUMBER=628xxxxxxxxxxx

2. Tambahkan ke config/services.php:

   'whatsapp' => [
       'token'      => env('WHATSAPP_CLOUD_API_TOKEN'),
       'phone_id'   => env('WHATSAPP_CLOUD_API_PHONE_ID'),
       'version'    => env('WHATSAPP_CLOUD_API_VERSION', 'v22.0'),
       'test_number'=> env('WHATSAPP_TEST_NUMBER'),
   ],

3. Copy semua file dari patch ini ke root project Laravel (sensipay-mini).

4. Tambahkan route test ke routes/web.php:

   use App\Http\Controllers\WhatsappCloudTestController;

   Route::get('/whatsapp/test', [WhatsappCloudTestController::class, 'sendTest'])
       ->name('whatsapp.test');

5. Jalankan:

   php artisan config:clear
   php artisan route:clear

6. Tes di browser:

   http://127.0.0.1:8000/whatsapp/test?to=628xxxxxxxxxxx

   Jika sukses, kamu akan menerima pesan template "hello_world" dari WA Cloud API.

7. File app/Services/Sensipay/InvoiceReminderService_cloud_example.php
   adalah CONTOH implementasi InvoiceReminderService yang sudah memakai
   WhatsappCloudApiService + WaReminder logging. Bandingkan dengan
   InvoiceReminderService kamu sekarang dan sesuaikan manual kalau perlu.
