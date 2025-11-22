<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ParentPaymentController extends Controller
{
    /**
     * Parent membuat pembayaran manual untuk satu invoice.
     *
     * Aturan:
     * - Minimal pembayaran rutin: 1.000.000
     * - Harus kelipatan 50.000
     * - Kecuali kalau sisa tagihan <= 1.000.000 → harus dibayar lunas (amount == sisa)
     * - Amount tidak boleh melebihi sisa tagihan.
     */
    public function store(Request $request, Invoice $invoice)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Pastikan ini orang tua dan invoice milik dia
        if ($user->role !== 'parent' || (int) $invoice->parent_user_id !== (int) $user->id) {
            abort(403, 'Tidak boleh membayar invoice milik akun lain.');
        }

        $remaining = max(0, ($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0));

        if ($remaining <= 0) {
            return back()->with('error', 'Invoice ini sudah lunas.')->withInput();
        }

        $validated = $request->validate([
            'amount'    => ['required', 'numeric', 'min:1000'],
            'reference' => ['nullable', 'string', 'max:255'],
            'evidence'  => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ]);

        $amount = (int) $validated['amount'];

        if ($amount <= 0) {
            return back()->with('error', 'Nominal pembayaran tidak valid.')->withInput();
        }

        if ($amount > $remaining) {
            return back()->with('error', 'Nominal pembayaran melebihi sisa tagihan.')->withInput();
        }

        // Aturan minimal & kelipatan
        if ($remaining <= 1000000) {
            // Sisa kecil: wajib pelunasan penuh
            if ($amount !== (int) $remaining) {
                return back()->with('error', 'Sisa tagihan di bawah atau sama dengan 1 juta harus dibayar lunas.')->withInput();
            }
        } else {
            // Masih besar: minimal 1 juta dan kelipatan 50 ribu
            if ($amount < 1000000) {
                return back()->with('error', 'Minimal pembayaran bulan ini adalah Rp 1.000.000.')->withInput();
            }

            if ($amount % 50000 !== 0) {
                return back()->with('error', 'Nominal pembayaran harus kelipatan Rp 50.000.')->withInput();
            }
        }

        // Simpan bukti bila ada
        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('payment_evidence', 'public');
        }

        $reference = $validated['reference'] ?? null;

        // Buat payment + langsung update invoice
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount'     => $amount,
            'paid_at'    => now(),
            'method'     => 'parent-portal',
            'note'       => $this->buildNote($reference, $evidencePath),
        ]);

        // Update angka invoice
        $invoice->paid_amount = ($invoice->paid_amount ?? 0) + $amount;
        if (method_exists($invoice, 'recalcStatus')) {
            $invoice->recalcStatus();
        } else {
            $invoice->save();
        }

        // Kirim notifikasi ke admin (email + WA)
        $this->notifyAdmin($user, $invoice, $payment, $remaining);

        return back()->with('status', 'Terima kasih. Pembayaran Anda sudah tercatat dan akan dicek admin.');
    }

    protected function buildNote(?string $reference, ?string $evidencePath): string
    {
        $parts = [];

        if ($reference) {
            $parts[] = 'Ref: ' . $reference;
        }

        if ($evidencePath) {
            $parts[] = 'Bukti: ' . $evidencePath;
        }

        if (empty($parts)) {
            return 'Pembayaran via parent portal.';
        }

        return implode(' | ', $parts);
    }

    protected function notifyAdmin($user, Invoice $invoice, Payment $payment, float $previousRemaining): void
    {
        $studentName = optional($invoice->student)->name ?? '-';
        $remainingAfter = max(0, ($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0));

        $baseMessage = "PEMBAYARAN BARU DARI ORANG TUA (Parent Portal)
"
            . "Parent  : {$user->name} ({$user->email})
"
            . "Siswa   : {$studentName}
"
            . "Invoice : {$invoice->invoice_code}
"
            . "Nominal : Rp " . number_format($payment->amount, 0, ',', '.') . "
"
            . "Sisa sebelum : Rp " . number_format($previousRemaining, 0, ',', '.') . "
"
            . "Sisa sesudah : Rp " . number_format($remainingAfter, 0, ',', '.') . "
"
            . "Metode  : {$payment->method}
"
            . "Catatan : {$payment->note}
";

        // Kirim email
        try {
            $adminEmail = env('JET_ADMIN_EMAIL');

            if ($adminEmail) {
                Mail::raw($baseMessage, function ($mail) use ($adminEmail) {
                    $mail->to($adminEmail)
                        ->subject('Pembayaran baru dari Parent Portal - Sensipay');
                });
            }
        } catch (\Throwable $e) {
            // Jangan gagalkan flow kalau email gagal
        }

        // Kirim WA via Fonnte (kalau diset)
        try {
            $token       = config('services.fonnte.token');
            $url         = config('services.fonnte.url', 'https://api.fonnte.com/send');
            $adminNumber = config('services.fonnte.admin_number', env('JET_ADMIN_WA'));

            if ($token && $url && $adminNumber) {
                Http::withHeaders([
                    'Authorization' => $token,
                ])->asForm()->post($url, [
                    'target'  => $adminNumber,
                    'message' => $baseMessage,
                ]);
            }
        } catch (\Throwable $e) {
            // Diam saja; bisa dicek di log kalau mau
        }
    }
}
