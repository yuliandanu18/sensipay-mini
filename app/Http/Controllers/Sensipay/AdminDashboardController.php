<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Program;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalStudents  = Student::count();
        $totalPrograms  = Program::count();
        $totalInvoices  = Invoice::count();

        $totalAmount = (int) Invoice::sum('total_amount');
        $totalPaid   = (int) Invoice::sum('paid_amount');
        $totalUnpaid = $totalAmount - $totalPaid;

        $latestInvoices = Invoice::with(['student', 'program'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $byStatus = Invoice::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        return view('sensipay.admin.dashboard', [
            'totalStudents'  => $totalStudents,
            'totalPrograms'  => $totalPrograms,
            'totalInvoices'  => $totalInvoices,
            'totalAmount'    => $totalAmount,
            'totalPaid'      => $totalPaid,
            'totalUnpaid'    => $totalUnpaid,
            'latestInvoices' => $latestInvoices,
            'byStatus'       => $byStatus,
        ]);
    }
}
