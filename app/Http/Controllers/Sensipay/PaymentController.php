<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Invoice $invoice)
    {
        $user = auth()->user();

        // Kalau PARENT: boleh lihat HANYA invoice miliknya sendiri
        if ($user->role === 'parent') {
            if ((int) $invoice->parent_user_id !== (int) $user->id) {
                abort(403, 'Invoice ini bukan milik Anda.');
            }
        } else {
            // Kalau BUKAN parent: pastikan dia role keuangan/owner/direksi
            if (! in_array($user->role, [
                'owner',
                'operational_director',
                'academic_director',
                'finance',
            ], true)) {
                abort(403, 'Tidak punya akses.');
            }
        }

        // Muat relasi
        $invoice->load(['student', 'program', 'payments']);

        // PAKAI VIEW LAMA YANG ADA FORM UPLOAD BUKTI
        return view('sensipay.payments.index', [
            'invoice' => $invoice,
            'user'    => $user,
        ]);
    }

    public function store(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount'    => 'required|numeric|min:1',
            'paid_at'   => 'required|date',
            'method'    => 'nullable|string|max:50',
            'note'      => 'nullable|string|max:500',
            // kalau ada kolom bukti upload, misalnya:
            // 'proof'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data['invoice_id'] = $invoice->id;

        $payment = Payment::create($data);

        $invoice->paid_amount = ($invoice->paid_amount ?? 0) + $payment->amount;
        if ($invoice->paid_amount >= $invoice->total_amount) {
            $invoice->status = 'paid';
        } elseif ($invoice->paid_amount > 0) {
            $invoice->status = 'partial';
        }
        $invoice->save();

        return redirect()
            ->route('sensipay.invoices.show', $invoice)
            ->with('success', 'Payment recorded');
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;

        if ($invoice) {
            $invoice->paid_amount = max(0, ($invoice->paid_amount ?? 0) - $payment->amount);
            if ($invoice->paid_amount <= 0) {
                $invoice->status = 'unpaid';
            } elseif ($invoice->paid_amount < $invoice->total_amount) {
                $invoice->status = 'partial';
            }
            $invoice->save();
        }

        $payment->delete();

        return back()->with('success', 'Payment deleted');
    }
}
