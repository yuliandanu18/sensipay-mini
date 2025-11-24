<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $invoicesQuery = Invoice::with('student')
            ->where('parent_user_id', $user->id)
            ->orderBy('due_date')
            ->orderBy('created_at', 'desc');

        $invoices = $invoicesQuery->get();

        // === NOTIF JATUH TEMPO ===
        $today    = now()->startOfDay();
        $nextWeek = now()->addDays(7)->endOfDay();

        $dueSoon = $invoices->filter(function ($invoice) use ($today, $nextWeek) {
            if (! $invoice->due_date) {
                return false;
            }

            $due = $invoice->due_date instanceof \Carbon\Carbon
                ? $invoice->due_date
                : \Carbon\Carbon::parse($invoice->due_date);

            $status = $invoice->status;
            $isUnpaid = in_array($status, ['unpaid', 'partial', null], true);

            return $isUnpaid && $due->between($today, $nextWeek);
        });

        $overdue = $invoices->filter(function ($invoice) use ($today) {
            if (! $invoice->due_date) {
                return false;
            }

            $due = $invoice->due_date instanceof \Carbon\Carbon
                ? $invoice->due_date
                : \Carbon\Carbon::parse($invoice->due_date);

            $status = $invoice->status;
            $isUnpaid = in_array($status, ['unpaid', 'partial', null], true);

            return $isUnpaid && $due->lt($today);
        });

        return view('sensipay.parent.dashboard', [
            'invoices' => $invoices,
            'dueSoon'  => $dueSoon,
            'overdue'  => $overdue,
        ]);
    }
}
