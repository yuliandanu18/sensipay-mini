<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\Payment;

class StudentFinanceController extends Controller
{
    public function show(Student $student)
    {
        // Ambil semua invoice siswa ini
        $invoices = Invoice::with('program')
            ->where('student_id', $student->id)
            ->orderBy('due_date')
            ->get();

        // Ambil semua payment yang terkait invoice- invoice tersebut
        $payments = Payment::with('invoice')
            ->whereIn('invoice_id', $invoices->pluck('id'))
            ->orderBy('paid_at')
            ->orderBy('created_at')
            ->get();

        $total_invoice = $invoices->sum('total_amount');
        $total_paid    = $invoices->sum('paid_amount');
        $remaining     = max(0, $total_invoice - $total_paid);

        return view('sensipay.students.finance', [
            'student'       => $student,
            'invoices'      => $invoices,
            'payments'      => $payments,
            'total_invoice' => $total_invoice,
            'total_paid'    => $total_paid,
            'remaining'     => $remaining,
        ]);
    }
}
