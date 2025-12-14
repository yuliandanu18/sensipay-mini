<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Services\FonnteClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPaymentProofController extends Controller
{
    public function index()
    {
        $pending = PaymentProof::with(['invoice.parentUser', 'uploader'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('sensipay.admin.payment-proofs.index', compact('pending'));
    }

    public function show(PaymentProof $proof)
    {
        $proof->loadMissing(['invoice.parentUser', 'uploader']);

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
            $proof->loadMissing(['invoice.parentUser']);

            $invoice = $proof->invoice;

            // 1) Buat payment entry
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount'     => $request->input('amount'),
                'paid_at'    => now(),
                'method'     => 'transfer',
                'note'       => 'Verifikasi bukti pembayaran #' . $proof->id,
            ]);

            // 2) Update invoice (pakai total_amount + paid_amount)
            $invoice->paid_amount = (int) $invoice->paid_amount + (int) $payment->amount;

            if ($invoice->paid_amount >= $invoice->total_amount) {
                $invoice->status = 'paid';
            } elseif ($invoice->paid_amount > 0) {
                $invoice->status = 'partial';
            } else {
                $invoice->status = 'unpaid';
            }

            $invoice->save();

            // 3) Update status proof
            $proof->update([
                'status'       => 'approved',
                'verified_by'  => Auth::id(),
                'verified_at'  => now(),
            ]);

            // 4) WA ke orang tua (SINGLE SOURCE OF TRUTH)
            $parentWa = $invoice->parentUser?->whatsapp_number;

            if ($parentWa) {
                $msg =
                    "*[Konfirmasi Pembayaran]*\n\n" .
                    "Pembayaran untuk Invoice *{$invoice->invoice_code}* telah kami verifikasi.\n" .
                    "Nominal: Rp " . number_format($payment->amount, 0, ',', '.') . "\n" .
                    "Status invoice sekarang: *{$invoice->status}*.\n\n" .
                    "Terima kasih atas kepercayaannya kepada Bimbel JET 🙏";

                $wa->sendMessage($parentWa, $msg);
            }
        });

        return redirect()
            ->route('sensipay.payment-proofs.index')
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

        $proof->loadMissing(['invoice.parentUser']);

        $proof->update([
            'status'           => 'rejected',
            'verified_by'      => Auth::id(),
            'verified_at'      => now(),
            'rejection_reason' => $request->input('reason'),
        ]);

        $invoice  = $proof->invoice;

        // SINGLE SOURCE OF TRUTH
        $parentWa = $invoice->parentUser?->whatsapp_number;

        if ($parentWa) {
            $msg =
                "*[Bukti Pembayaran Ditolak]*\n\n" .
                "Bukti pembayaran untuk Invoice *{$invoice->invoice_code}* belum dapat kami terima.\n" .
                "Alasan: {$request->input('reason')}\n\n" .
                "Silakan upload ulang bukti pembayaran yang sesuai.\n" .
                "Terima kasih.";

            $wa->sendMessage($parentWa, $msg);
        }

        return redirect()
            ->route('sensipay.payment-proofs.index')
            ->with('status', 'Bukti pembayaran ditolak & orang tua sudah diberi informasi.');
    }
}
