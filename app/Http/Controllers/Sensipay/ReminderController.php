<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Sensipay\InvoiceReminderService;

class ReminderController extends Controller
{
    /**
     * Tampilkan daftar invoice yang perlu di-reminder.
     */
    public function index()
    {
        $invoices = Invoice::query()
            ->with(['parent', 'student', 'program'])
            ->whereDate('due_date', '<=', now()->toDateString())
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date')
            ->paginate(20);

        return view('sensipay.reminders.index', compact('invoices'));
    }

    /**
     * Kirim reminder untuk SATU invoice (tombol "Kirim WA" per baris).
     */
    public function sendSingle(Invoice $invoice, InvoiceReminderService $service)
    {
        try {
            $service->sendForInvoice($invoice);

            return back()->with('success', 'Reminder berhasil dikirim.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Reminder gagal: ' . $e->getMessage());
        }
    }

    /**
     * Kirim reminder untuk SEMUA invoice jatuh tempo (tombol "Kirim Reminder Semua").
     */
    public function sendBulk(InvoiceReminderService $service)
    {
        try {
            $sent = $service->sendForDueInvoices();

            return back()->with('success', "Bulk reminder terkirim untuk {$sent} invoice.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Bulk reminder gagal: ' . $e->getMessage());
        }
    }
}
