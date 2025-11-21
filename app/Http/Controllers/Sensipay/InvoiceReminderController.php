<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceReminderController extends Controller
{
    public function index()
    {
        // Ambil invoice yang masih belum lunas
        $invoices = Invoice::with(['student', 'program'])
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date')
            ->get();

        return view('sensipay.admin.invoice_reminders', [
            'invoices' => $invoices,
        ]);
    }
}
