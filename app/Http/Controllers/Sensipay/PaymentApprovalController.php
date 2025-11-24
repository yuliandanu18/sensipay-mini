<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentApprovalController extends Controller
{
    /**
     * Halaman daftar pembayaran untuk approval admin.
     */
   

public function index(Request $request)
    {
        $query = Payment::with([
                'invoice',
                'invoice.student',
                'invoice.student.parent',
            ])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->latest();

        // Ambil filter status dari query string: ?status=pending/approved/rejected
        $status = $request->query('status');

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        } else {
            $status = null; // kalau nggak valid, anggap "semua"
        }

        $payments = $query->paginate(50)->withQueryString();

        return view('sensipay.payments.index', compact('payments', 'status'));
    }
    /**
     * Setujui pembayaran.
     *
     * - Ubah status payment -> approved
     * - Tambah paid_amount di invoice
     * - Kalau sudah lunas: status invoice -> paid
     */
    public function approve(Payment $payment)
    {
        if ($payment->status === 'approved') {
            return back()->with('error', 'Pembayaran ini sudah disetujui sebelumnya.');
        }

        if ($payment->status === 'rejected') {
            return back()->with('error', 'Pembayaran ini sudah pernah ditolak.');
        }

        $invoice = $payment->invoice;

        if (! $invoice) {
            return back()->with('error', 'Invoice untuk pembayaran ini tidak ditemukan.');
        }

        DB::transaction(function () use ($payment, $invoice) {
            // Update status payment
            $payment->status = 'approved';
            $payment->save();

            // Hitung ulang paid_amount invoice
            $currentPaid = (int) ($invoice->paid_amount ?? 0);
            $total       = (int) ($invoice->total_amount ?? 0);
            $amount      = (int) ($payment->amount ?? 0);

            $newPaid = $currentPaid + $amount;

            if ($newPaid > $total && $total > 0) {
                // Amanin, jangan sampai lebih dari total
                $newPaid = $total;
            }

            $invoice->paid_amount = $newPaid;

            // Update status invoice
            if ($total > 0 && $newPaid >= $total) {
                $invoice->status = 'paid'; // sesuaikan dengan enum/string di sistemmu
            } elseif ($newPaid > 0) {
                // Kalau kamu punya status 'partial', bisa pakai ini
                $invoice->status = 'partial';
            } else {
                $invoice->status = 'unpaid';
            }

            $invoice->save();
        });

        return back()->with('success', 'Pembayaran disetujui dan invoice diperbarui.');
    }

    /**
     * Tolak pembayaran.
     */
    public function reject(Payment $payment)
    {
        if ($payment->status === 'approved') {
            return back()->with('error', 'Pembayaran ini sudah disetujui, tidak bisa ditolak.');
        }

        if ($payment->status === 'rejected') {
            return back()->with('error', 'Pembayaran ini sudah pernah ditolak.');
        }

        $payment->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Pembayaran ditolak.');
    }
}
