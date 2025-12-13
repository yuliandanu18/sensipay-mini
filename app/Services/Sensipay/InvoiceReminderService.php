<?php

namespace App\Services\Sensipay;

use App\Models\Invoice;
use App\Models\WaReminder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvoiceReminderService
{
    /**
     * Cari nomor WA orang tua dengan berbagai fallback.
     */
    protected function resolveParentWhatsapp(Invoice $invoice): ?string
    {
        // 1. Kolom langsung di invoice
        if (! empty($invoice->parent_whatsapp)) {
            return $invoice->parent_whatsapp;
        }

        // 2. Relasi parentUser (invoice.parent_user_id -> users)
        if (method_exists($invoice, 'parentUser') && $invoice->parentUser) {
            $user = $invoice->parentUser;
            $wa   = $user->whatsapp_number ?? $user->phone ?? null;

            if (! empty($wa)) {
                return $wa;
            }
        }

        // 3. invoice -> student -> parent
        if (method_exists($invoice, 'student') && $invoice->student) {
            $student = $invoice->student;

            if (! empty($student->parent_whatsapp)) {
                return $student->parent_whatsapp;
            }

            if (method_exists($student, 'parent') && $student->parent) {
                $p  = $student->parent;
                $wa = $p->whatsapp_number ?? $p->phone ?? null;

                if (! empty($wa)) {
                    return $wa;
                }
            }
        }

        Log::warning('Reminder gagal: tidak menemukan nomor WA parent untuk invoice.', [
            'invoice_id' => $invoice->id,
        ]);

        return null;
    }

    /**
     * Cari nama & ID parent (kalau ada).
     */
    protected function resolveParentIdentity(Invoice $invoice): array
    {
        $parentName = 'Orang Tua/Wali';
        $parentId   = null;

        if (method_exists($invoice, 'parentUser') && $invoice->parentUser) {
            $parentId   = $invoice->parentUser->id ?? $parentId;
            $parentName = $invoice->parentUser->name ?? $parentName;
        }

        if (method_exists($invoice, 'student') && $invoice->student) {
            $student = $invoice->student;

            if (method_exists($student, 'parent') && $student->parent) {
                $p          = $student->parent;
                $parentId   = $p->id ?? $parentId;
                $parentName = $p->name ?? $parentName;
            }
        }

        return [$parentName, $parentId];
    }

    /**
     * Kirim reminder untuk satu invoice.
     */
    public function sendForInvoice(Invoice $invoice): void
    {
        $number = $this->resolveParentWhatsapp($invoice);

        if (! $number) {
            return;
        }

        [$parentName, $parentId] = $this->resolveParentIdentity($invoice);

        $token       = config('services.fonnte.token');
        $url         = config('services.fonnte.url', 'https://api.fonnte.com/send');
        $adminNumber = config('services.fonnte.admin_number');

        if (! $token) {
            throw new \RuntimeException('FONNTE_TOKEN belum diisi di .env / config.');
        }

        $dueDate = optional($invoice->due_date)->format('d-m-Y');
        $student = optional($invoice->student)->name ?? '-';
        $amount  = number_format($invoice->total_amount, 0, ',', '.');

        $message = "Assalamualaikum Ayah/Bunda {$parentName}\n\n"
            . "Ini pengingat tagihan Bimbel JET untuk:\n"
            . "Siswa : {$student}\n"
            . "Jatuh Tempo : {$dueDate}\n"
            . "Jumlah : Rp {$amount}\n\n"
            . "Mohon kesediaannya untuk melakukan pembayaran.\n"
            . "Terima kasih. 🙏";

        $reference = 'INV-' . $invoice->id . '-' . Str::uuid();

        $log = WaReminder::create([
            'invoice_id' => $invoice->id,
            'parent_id'  => $parentId,
            'target'     => $number,
            'message'    => $message,
            'status'     => 'sent',
            'provider'   => 'fonnte',
            'reference'  => $reference,
            'sent_at'    => now(),
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->asForm()->post($url, [
                'target'  => $number,
                'message' => $message,
                'tag'     => $reference,
            ]);

            if (! $response->successful()) {
                Log::error('Gagal kirim WA reminder invoice (HTTP)', [
                    'invoice_id' => $invoice->id,
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                ]);

                $log->status    = 'failed';
                $log->failed_at = now();
                $log->save();

                throw new \RuntimeException(
                    'HTTP gagal: status ' . $response->status()
                    . '. Body: ' . $response->body()
                );
            }

            $data = $response->json();

            if (! ($data['status'] ?? false)) {
                $log->status       = 'failed';
                $log->failed_at    = now();
                $log->last_payload = $data;
                $log->save();

                throw new \RuntimeException(
                    'Gateway balas status=false. Response: ' . json_encode($data)
                );
            }

            $log->last_payload        = $data;
            $log->provider_message_id = $data['id'] ?? $log->provider_message_id;
            $log->save();

            if ($invoice->isFillable('last_reminder_sent_at')) {
                $invoice->last_reminder_sent_at = now();
                $invoice->save();
            }

            if ($adminNumber) {
                Http::withHeaders([
                    'Authorization' => $token,
                ])->asForm()->post($url, [
                    'target'  => $adminNumber,
                    'message' => "[LOG] Reminder tagihan terkirim untuk {$parentName}, invoice #{$invoice->id}",
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Exception kirim WA reminder invoice', [
                'invoice_id' => $invoice->id ?? null,
                'error'      => $e->getMessage(),
            ]);

            $log->status    = 'failed';
            $log->failed_at = now();
            $log->save();

            throw $e;
        }
    }

    /**
     * Kirim reminder untuk semua invoice yang sudah / lewat jatuh tempo.
     * Return jumlah invoice yang dicoba dikirim.
     */
    public function sendForDueInvoices(): int
    {
        $query = Invoice::query()
            ->whereDate('due_date', '<=', now()->toDateString())
            ->whereIn('status', ['unpaid', 'partial']);

        $sent = 0;

        $query->chunk(50, function ($invoices) use (&$sent) {
            foreach ($invoices as $invoice) {
                try {
                    $this->sendForInvoice($invoice);
                    $sent++;
                } catch (\Throwable $e) {
                    Log::warning('Lewati invoice gagal reminder', [
                        'invoice_id' => $invoice->id ?? null,
                        'error'      => $e->getMessage(),
                    ]);
                }
            }
        });

        return $sent;
    }
}
