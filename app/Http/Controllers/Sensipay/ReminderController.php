<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;

class ReminderController extends Controller
{
    /**
     * Contoh: daftar invoice yang perlu dikirim reminder.
     * Nanti bisa dikembangkan untuk kirim WA / email.
     */
    public function index(Request $request)
    {
        $invoices = Invoice::with(['student', 'program'])
            ->orderBy('due_date')
            ->paginate(20);

        return view('sensipay.reminders.index', compact('invoices'));
    }
}
