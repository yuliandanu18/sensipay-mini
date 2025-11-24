<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;

class SendDueInvoiceReminders extends Command
{
    protected $signature = 'sensipay:remind-due';
    protected $description = 'Kirim reminder WA untuk invoice yang jatuh tempo dalam 3 hari atau sudah lewat';

    public function handle()
    {
        $today = now()->startOfDay();
        $limit = now()->addDays(3)->endOfDay();

        $invoices = Invoice::with('student.parent')
            ->whereIn('status', ['unpaid', 'partial'])
            ->whereNotNull('due_date')
            ->get()
            ->filter(function ($inv) use ($today, $limit) {
                $due = $inv->due_date;

                return $due && (
                    $due->isBefore($today)     // overdue
                    || $due->between($today, $limit) // 3 hari
                );
            });

        foreach ($invoices as $inv) {
            $parent = $inv->student->parent;

            if (!$parent || !$parent->phone) {
                continue;
            }

            $msg = "Halo, orang tua {$inv->student->name}. "
                 . "Invoice {$inv->invoice_code} memiliki sisa tagihan sebesar "
                 . "Rp " . number_format($inv->remaining, 0, ',', '.') . ". "
                 . "Jatuh tempo: " . $inv->due_date->format('d-m-Y') . ".\n\n"
                 . "Silakan cek portal Sensipay untuk pembayaran ya 🙏";

            Http::withHeaders([
                'Authorization' => config('services.fonnte.token'),
            ])->post(config('services.fonnte.url'), [
                'target' => $parent->phone,
                'message' => $msg,
            ]);
        }

        $this->info('Reminder dikirim.');
    }
}
