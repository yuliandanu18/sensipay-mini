<?php

namespace App\Http\Controllers;

use App\Services\Whatsapp\WhatsappCloudApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsappCloudTestController extends Controller
{
    /**
     * GET /whatsapp/test?to=628xxxx
     *
     * Mengirim template "hello_world" ke nomor yang diberikan.
     * Dipakai hanya untuk uji koneksi awal ke WhatsApp Cloud API.
     */
    public function sendTest(Request $request, WhatsappCloudApiService $whatsapp)
    {
        $to = $request->query('to', config('services.whatsapp.test_number'));

        if (! $to) {
            return response()->json([
                'ok'    => false,
                'error' => 'Masukkan nomor tujuan di query ?to=628xxxxx atau set services.whatsapp.test_number.',
            ], 400);
        }

        try {
            $result = $whatsapp->sendTemplate($to, 'hello_world', 'en_US');

            return response()->json([
                'ok'      => true,
                'message' => 'Pesan hello_world dikirim. Cek WhatsApp kamu.',
                'result'  => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal kirim WA test hello_world', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok'    => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
