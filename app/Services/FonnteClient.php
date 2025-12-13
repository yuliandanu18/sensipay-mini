<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteClient
{
    protected string $token;
    protected string $url;
    protected ?string $sender;

    public function __construct()
    {
        $this->token  = config('services.fonnte.token', '');
        $this->url    = config('services.fonnte.url', 'https://api.fonnte.com/send');
        $this->sender = config('services.fonnte.sender');
    }

    public function sendMessage(string $number, string $message): bool
    {
        Log::info('Fonnte sendMessage called', ['number' => $number]);

        if (empty($this->token) || empty($this->url)) {
            Log::warning('Fonnte not configured properly.', [
                'token_empty' => empty($this->token),
                'url'         => $this->url,
            ]);
            return false;
        }

        $payload = [
            'target'  => $number,
            'message' => $message,
        ];

        if (!empty($this->sender)) {
            $payload['sender'] = $this->sender;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->asForm()->post($this->url, $payload);

            Log::info('Fonnte response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if (! $response->successful()) {
                Log::error('Fonnte send failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            $data = $response->json();

            if (!($data['status'] ?? false)) {
                Log::error('Fonnte gateway returned status=false', [
                    'response' => $data,
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Fonnte exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }
}
