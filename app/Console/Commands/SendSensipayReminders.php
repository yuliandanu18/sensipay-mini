<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSensipayReminders extends Command
{
    /**
     * Nama perintah Artisan.
     *
     * php artisan sensipay:reminder
     */
    protected $signature = 'sensipay:reminder {--dry-run : Hanya log, tidak kirim WhatsApp}';

    /**
     * Deskripsi singkat.
     */
    protected $description = 'Kirim reminder WhatsApp untuk invoice yang jatuh tempo / hampir jatuh tempo';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $today    = now()->startOfDay();
        $nextWeek = now()->addDays(7)->endOfDay();

        // Ambil invoice yang:
        // - status: unpaid / partial / null
        // - punya due_date
        // - due_date <= 7 hari ke depan (termasuk yang sudah lewat)
        $invoices = Invoice::with('student')
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $nextWeek)
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('Tidak ada invoice yang perlu diingatkan.');
            return Command::SUCCESS;
        }

        $token       = config('services.fonnte.token');
        $url         = config('services.fonnte.url', 'https://api.fonnte.com/send');
        $adminNumber = config('services.fonnte.admin_number'); // opsional, kalau mau copy ke admin

        if (! $token || ! $url) {
            $this->error('Konfigurasi Fonnte belum lengkap (services.fonnte.*).');
            Log::warning('sensipay:reminder - Fonnte config missing.');
            return Command::FAILURE;
        }

        $this->info('Menyiapkan reminder untuk ' . $invoices->count() . ' invoice.');
        Log::info('sensipay:reminder start', ['count' => $invoices->count(), 'dry_run' => $dryRun]);

        foreach ($invoices as $invoice) {
            $student = $invoice->student;

            // Cari nomor WA penagihan
            $parentPhone =
                ($student->parent_phone ?? null) ??
                ($student->parent_whatsapp ?? null) ??
                ($student->whatsapp ?? null);

            if (! $parentPhone) {
                Log::warning('Invoice tanpa nomor WA', [
                    'invoice_id'   => $invoice->id,
                    'invoice_code' => $invoice->invoice_code,
                ]);
                continue;
            }

            $total     = (int) ($invoice->total_amount ?? 0);
            $paid      = (int) ($invoice->paid_amount ?? 0);
            $remaining = max(0, $total - $paid);

            $due = $invoice->due_date;

            $isOverdue = $due->lt($today);

            $message  = "*[Bimbel JET – Pengingat Tagihan]*\n\n";
            $message .= "Yth. Orang Tua/Wali dari *" . ($student->name ?? 'Siswa') . "*.\n\n";
            $message .= "Tagihan bimbel dengan kode invoice *{$invoice->invoice_code}* ";
            $message .= $isOverdue
                ? "sudah *LEWAT JATUH TEMPO*.\n"
                : "akan jatuh tempo pada *" . $due->format('d-m-Y') . "*.\n";

            $message .= "\n*Ringkasan Tagihan:*\n";
            $message .= "- Total: Rp " . number_format($total, 0, ',', '.') . "\n";
            $message .= "- Terbayar: Rp " . number_format($paid, 0, ',', '.') . "\n";
            $message .= "- Sisa: Rp " . number_format($remaining, 0, ',', '.') . "\n";

            $message .= "\nUntuk konfirmasi pembayaran, Bapak/Ibu dapat:\n";
            $message .= "- Login ke portal orang tua\n";
            $message .= "- Atau kirim bukti transfer ke admin Bimbel JET\n\n";
            $message .= "_Pesan ini dikirim otomatis oleh sistem Sensipay Bimbel JET._";

            // LOG dulu
            $this->line("Reminder ke {$parentPhone} untuk invoice {$invoice->invoice_code}");
            Log::info('sensipay:reminder prepared', [
                'invoice_id'   => $invoice->id,
                'invoice_code' => $invoice->invoice_code,
                'target'       => $parentPhone,
                'overdue'      => $isOverdue,
            ]);

            if ($dryRun) {
                // Mode simulasi: tidak kirim
                continue;
            }

            try {
                $response = Http::withHeaders([
                        'Authorization' => $token,
                    ])
                    ->asForm()
                    ->post($url, [
                        'target'  => $parentPhone,
                        'message' => $message,
                    ]);

                if (! $response->successful()) {
                    Log::error('sensipay:reminder send failed', [
                        'invoice_id'   => $invoice->id,
                        'invoice_code' => $invoice->invoice_code,
                        'target'       => $parentPhone,
                        'response'     => $response->body(),
                    ]);
                }

                // Kalau mau, kirim copy ke admin:
                if ($adminNumber) {
                    Http::withHeaders([
                            'Authorization' => $token,
                        ])
                        ->asForm()
                        ->post($url, [
                            'target'  => $adminNumber,
                            'message' => "[Copy Reminder]\n\n" . $message,
                        ]);
                }
            } catch (\Throwable $e) {
                Log::error('sensipay:reminder exception', [
                    'invoice_id'   => $invoice->id,
                    'invoice_code' => $invoice->invoice_code,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        $this->info('Selesai proses reminder.');
        Log::info('sensipay:reminder finished');

        return Command::SUCCESS;
    }
}
