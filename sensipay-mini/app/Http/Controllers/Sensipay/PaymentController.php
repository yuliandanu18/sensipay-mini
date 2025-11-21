<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'amount'    => 'required|numeric|min:1',
            'paid_at'   => 'required|date',
            'method'    => 'nullable|string|max:50',
            'note'      => 'nullable|string|max:500',
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
