<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentPaymentController extends Controller
{
    /**
     * Tampilkan detail 1 invoice + form konfirmasi pembayaran.
     */
    public function show(Invoice $invoice)
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Silakan login sebagai orang tua.');
        }

        // Jika parent_user_id terisi, pastikan cocok dengan akun yang login
        if (! is_null($invoice->parent_user_id) && $invoice->parent_user_id !== $user->id) {
            abort(403, 'Invoice ini bukan milik Anda.');
        }

        $total     = (int) ($invoice->total_amount ?? 0);
        $paid      = (int) ($invoice->paid_amount ?? 0);
        $remaining = max(0, $total - $paid);

        return view('sensipay.parent.invoice-show', [
            'invoice'   => $invoice,
            'total'     => $total,
            'paid'      => $paid,
            'remaining' => $remaining,
        ]);
    }

    /**
     * Simpan konfirmasi pembayaran dari portal orang tua.
     *
     * Aturan:
     * - Jika sisa > 1.000.000 -> minimal bayar 1.000.000 & kelipatan 50.000
     * - Jika sisa <= 1.000.000 -> wajib pelunasan penuh
     * - Pembayaran disimpan sebagai status 'pending' (belum menambah paid_amount di invoice)
     */
    public function store(Request $request, Invoice $invoice)
    {
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Silakan login sebagai orang tua.');
        }

        // Jika parent_user_id diisi, baru cek. Kalau null, jangan diblok.
        if (! is_null($invoice->parent_user_id) && $invoice->parent_user_id !== $user->id) {
            abort(403, 'Invoice ini bukan milik Anda.');
        }

        // Validasi dasar (nominal nanti kita normalisasi manual)
        $validated = $request->validate([
            'amount' => ['required'],
            'note'   => ['nullable', 'string', 'max:255'],
            'proof'  => ['nullable', 'file', 'image', 'max:2048'], // max ~2MB
        ]);

        // ===== 1. NORMALISASI NOMINAL =====
        $rawAmount   = (string) $validated['amount'];
        $onlyDigits  = preg_replace('/[^\d]/', '', $rawAmount);

        if ($onlyDigits === '' || (int) $onlyDigits <= 0) {
            return back()->with('error', 'Nominal bayar tidak valid.');
        }

        $amount = (int) $onlyDigits;

        // ===== 2. HITUNG SISA TAGIHAN =====
        $total     = (int) ($invoice->total_amount ?? 0);
        $paid      = (int) ($invoice->paid_amount ?? 0);
        $remaining = max(0, $total - $paid);

        if ($remaining <= 0) {
            return back()->with('error', 'Invoice ini sudah lunas.');
        }

        if ($amount > $remaining) {
            return back()->with('error', 'Nominal melebihi sisa tagihan.');
        }

        // ===== 3. ATURAN BISNIS =====
        if ($remaining > 1_000_000) {
            if ($amount < 1_000_000) {
                return back()->with('error', 'Minimal pembayaran Rp 1.000.000 untuk tagihan ini.');
            }

            if ($amount % 50_000 !== 0) {
                return back()->with('error', 'Nominal harus kelipatan Rp 50.000.');
            }
        } else {
            // remaining <= 1.000.000 -> wajib pelunasan penuh
            if ($amount !== $remaining) {
                return back()->with('error', 'Untuk sisa di bawah atau sama dengan Rp 1.000.000, wajib pelunasan penuh.');
            }
        }

        // ===== 4. SIMPAN BUKTI JIKA ADA =====
        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('payment_proofs', 'public');
        }

        // ===== 5. CATAT PEMBAYARAN (STATUS PENDING) =====
        Payment::create([
            'invoice_id' => $invoice->id,
            'amount'     => $amount,
            'paid_at'    => now(),
            'method'     => 'parent-portal',
            'note'       => $validated['note'] ?? null,
            'status'     => 'pending',
            'proof_path' => $proofPath,
        ]);

        return back()->with('status', 'Konfirmasi pembayaran berhasil dikirim. Admin JET akan melakukan verifikasi.');
    }
}
