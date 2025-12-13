<?php


namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentProof;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPaymentProofController extends Controller
{
    public function index()
    {
        $pending = PaymentProof::with('invoice', 'uploader')
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('sensipay.admin.payment-proofs.index', compact('pending'));
    }

    public function show(PaymentProof $proof)
    {
        $invoice = $proof->invoice;

        return view('sensipay.admin.payment-proofs.show', compact('proof', 'invoice'));
    }

    public function approve(Request $request, PaymentProof $proof, FonnteClient $wa)
    {
        if ($proof->status !== 'pending') {
            return back()->with('error', 'Bukti pembayaran ini sudah diproses.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($request, $proof, $wa) {
            $invoice = $proof->invoice;

            // 1. Buat payment entry
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount'     => $request->input('amount'),
                'paid_at'    => now(),
                'method'     => 'transfer',
                'note'       => 'Verifikasi bukti pembayaran #' . $proof->id,
            ]);

            // 2. Update sisa invoice (sesuaikan dengan struktur yg kamu pakai)
            // Misal:
            $invoice->remaining_amount = max(0, $invoice->remaining_amount - $payment->amount);
            if ($invoice->remaining_amount == 0) {
                $invoice->status = 'paid';
            } elseif ($invoice->remaining_amount < $invoice->total_amount) {
                $invoice->status = 'partial';
            }
            $invoice->save();

            // 3. Update status proof
            $proof->update([
                'status'       => 'approved',
                'verified_by'  => Auth::id(),
                'verified_at'  => now(),
            ]);

            // 4. WA ke orang tua (kalau nomor tersedia)
            $parentWa = $invoice->parent_whatsapp ?? null;
            if (! $parentWa && $invoice->student && method_exists($invoice->student, 'parent')) {
                $parentWa = $invoice->student->parent->whatsapp_number ?? null;
            }

            if ($parentWa) {
                $msg =
                    "*[Konfirmasi Pembayaran]*\n\n" .
                    "Pembayaran untuk Invoice *{$invoice->invoice_number}* telah kami verifikasi.\n" .
                    "Nominal: Rp " . number_format($payment->amount, 0, ',', '.') . "\n" .
                    "Status invoice sekarang: *{$invoice->status}*.\n\n" .
                    "Terima kasih atas kepercayaannya kepada Bimbel JET 🙏";
                $wa->sendMessage($parentWa, $msg);
            }
        });

        return redirect()
            ->route('sensipay.admin.payment-proofs.index')
            ->with('status', 'Bukti pembayaran disetujui & pembayaran tercatat.');
    }

    public function reject(Request $request, PaymentProof $proof, FonnteClient $wa)
    {
        if ($proof->status !== 'pending') {
            return back()->with('error', 'Bukti pembayaran ini sudah diproses.');
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $proof->update([
            'status'           => 'rejected',
            'verified_by'      => Auth::id(),
            'verified_at'      => now(),
            'rejection_reason' => $request->input('reason'),
        ]);

        // WA ke parent
        $invoice  = $proof->invoice;
        $parentWa = $invoice->parent_whatsapp ?? null;
        if (! $parentWa && $invoice->student && method_exists($invoice->student, 'parent')) {
            $parentWa = $invoice->student->parent->whatsapp_number ?? null;
        }

        if ($parentWa) {
            $msg =
                "*[Bukti Pembayaran Ditolak]*\n\n" .
                "Bukti pembayaran untuk Invoice *{$invoice->invoice_number}* belum dapat kami terima.\n" .
                "Alasan: {$request->input('reason')}\n\n" .
                "Silakan upload ulang bukti pembayaran yang sesuai.\n" .
                "Terima kasih.";
            $wa->sendMessage($parentWa, $msg);
        }

        return redirect()
            ->route('sensipay.admin.payment-proofs.index')
            ->with('status', 'Bukti pembayaran ditolak & orang tua sudah diberi informasi.');
    }
}
