<?php

// app/Http/Controllers/Sensipay/ParentPaymentProofController.php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentProof;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ParentPaymentProofController extends Controller
{
public function store(Request $request, Invoice $invoice, FonnteClient $wa)
{
    // 1. Hard guard: kalau gak ada file, langsung balik
    if (! $request->hasFile('proof')) {
        return back()
            ->withErrors(['proof' => 'File bukti transfer wajib diupload.'])
            ->withInput();
    }

    // 2. Validasi Laravel
    $validated = $request->validate([
        'proof'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096',
        'amount'        => 'nullable|numeric|min:1',
        'transfer_date' => 'nullable|date',
        'bank_name'     => 'nullable|string|max:100',
    ]);

    $user = Auth::user();

    // 3. Simpan file ke storage/app/public/payment_proofs
    $path = $request->file('proof')->store('payment_proofs', 'public');

    $proof = PaymentProof::create([
        'invoice_id'    => $invoice->id,
        'uploaded_by'   => $user->id,
        'file_path'     => $path,
        'amount'        => $validated['amount']        ?? null,
        'transfer_date' => $validated['transfer_date'] ?? null,
        'bank_name'     => $validated['bank_name']     ?? null,
        'status'        => 'pending',
    ]);

    // 4. Kirim WA ke admin kalau config Fonnte lengkap
    $adminNumber = config('services.fonnte.admin_number');

    if ($adminNumber) {
        $msg =
            "*[Bukti Pembayaran Baru]*\n\n" .
            "Invoice : {$invoice->invoice_number}\n" .
            "Siswa   : " . ($invoice->student->name ?? '-') . "\n" .
            "Nominal : " . number_format($proof->amount ?? 0, 0, ',', '.') . "\n\n" .
            "Silakan verifikasi di panel admin Sensipay.";

        $sent = $wa->sendMessage($adminNumber, $msg);

        if (! $sent) {
            \Log::warning('Gagal kirim WA bukti pembayaran baru ke admin.', [
                'invoice_id' => $invoice->id,
            ]);
        }
    }

    return redirect()
        ->route('sensipay.parent.dashboard')
        ->with('status', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
}
}