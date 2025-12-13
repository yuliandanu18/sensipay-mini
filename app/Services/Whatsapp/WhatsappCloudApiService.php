<?php

namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappCloudApiService
{
    protected string $token;
    protected string $phoneId;
    protected string $version;

    public function __construct()
    {
        $this->token   = (string) config('services.whatsapp.token');
        $this->phoneId = (string) config('services.whatsapp.phone_id');
        $this->version = (string) config('services.whatsapp.version', 'v22.0');
    }

    protected function endpoint(string $path): string
    {
        return "https://graph.facebook.com/{$this->version}/{$this->phoneId}/{$path}";
    }

    /**
     * Kirim pesan template (business-initiated).
     *
     * Pastikan template sudah di-approve di WhatsApp Manager.
     */
    public function sendTemplate(string $to, string $templateName, string $langCode = 'id', array $components = []): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $to,
            'type'              => 'template',
            'template'          => [
                'name'     => $templateName,
                'language' => ['code' => $langCode],
            ],
        ];

        if (! empty($components)) {
            $payload['template']['components'] = $components;
        }

        return $this->sendRequest('messages', $payload);
    }

    /**
     * Kirim pesan teks biasa (user-initiated conversation / dalam window 24 jam).
     */
    public function sendText(string $to, string $body): array
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $to,
            'type'              => 'text',
            'text'              => [
                'preview_url' => false,
                'body'        => $body,
            ],
        ];

        return $this->sendRequest('messages', $payload);
    }

    /**
     * Low-level HTTP caller ke Cloud API.
     */
    protected function sendRequest(string $path, array $payload): array
    {
        if (! $this->token || ! $this->phoneId) {
            throw new \RuntimeException('Config WhatsApp Cloud API belum lengkap (token / phone_id).');
        }

        $url = $this->endpoint($path);

        try {
            $response = Http::withToken($this->token)
                ->acceptJson()
                ->post($url, $payload);

            $json = $response->json() ?? [];

            if (! $response->successful()) {
                Log::error('WA Cloud API HTTP error', [
                    'url'      => $url,
                    'status'   => $response->status(),
                    'payload'  => $payload,
                    'response' => $json,
                ]);

                throw new \RuntimeException(
                    'WA Cloud API error: HTTP ' . $response->status()
                );
            }

            return $json;
        } catch (\Throwable $e) {
            Log::error('WA Cloud API exception', [
                'url'     => $url,
                'payload' => $payload,
                'error'   => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
