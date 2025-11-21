<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Semua invoice (sementara global, nanti bisa difilter per parent)
        $invoices = Invoice::with(['student', 'program'])
            ->orderBy('due_date')
            ->get();

        // Ringkasan tagihan bulan ini (berdasarkan due_date)
        $monthlyInvoices = Invoice::whereBetween('due_date', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])->get();

        $monthlyTotal     = $monthlyInvoices->sum('total_amount');
        $monthlyPaid      = $monthlyInvoices->sum('paid_amount');
        $monthlyRemaining = max(0, $monthlyTotal - $monthlyPaid);

        return view('sensipay.parent.dashboard', [
            'user'             => $user,
            'invoices'         => $invoices,
            'monthlyTotal'     => $monthlyTotal,
            'monthlyPaid'      => $monthlyPaid,
            'monthlyRemaining' => $monthlyRemaining,
        ]);
    }
}
