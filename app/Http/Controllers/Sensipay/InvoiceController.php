<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index(Request $request)
{
    $status   = $request->status;
    $program  = $request->program;
    $month    = $request->month;
    $search   = $request->search;

    $query = Invoice::with(['student', 'program']);

    if ($status) {
        $query->where('status', $status);
    }

    if ($program) {
        $query->where('program_id', $program);
    }

    if ($month) {
        $query->whereMonth('created_at', substr($month, 5, 2))
              ->whereYear('created_at', substr($month, 0, 4));
    }

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('invoice_code', 'like', "%$search%")
              ->orWhereHas('student', fn($s)=>$s->where('name','like',"%$search%"));
        });
    }

    $invoices = $query->orderBy('created_at','desc')->paginate(20);

    $programs = \App\Models\Program::orderBy('name')->get();

    return view('sensipay.invoices.index', [
        'invoices' => $invoices,
        'programs' => $programs,
        'status'   => $status,
        'program_id' => $program,
        'month'    => $month,
        'search'   => $search,
    ]);
}


    public function create()
    {
        $students = Student::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();

        return view('sensipay.invoices.create', compact('students', 'programs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'    => 'required|exists:students,id',
            'program_id'    => 'required|exists:programs,id',
            'total_amount'  => 'required|numeric|min:0',
            'due_date'      => 'nullable|date',
        ]);

        $data['invoice_code'] = $this->generateInvoiceCode();
        $data['paid_amount'] = 0;
        $data['status'] = 'unpaid';

        $invoice = Invoice::create($data);

        return redirect()
            ->route('sensipay.invoices.show', $invoice)
            ->with('success', 'Invoice created');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['student', 'program', 'payments']);

        return view('sensipay.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $students = Student::orderBy('name')->get();
        $programs = Program::orderBy('name')->get();

        return view('sensipay.invoices.edit', compact('invoice', 'students', 'programs'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'student_id'    => 'required|exists:students,id',
            'program_id'    => 'required|exists:programs,id',
            'total_amount'  => 'required|numeric|min:0',
            'due_date'      => 'nullable|date',
            'status'        => 'required|in:unpaid,partial,paid',
        ]);

        $invoice->update($data);

        return redirect()
            ->route('sensipay.invoices.show', $invoice)
            ->with('success', 'Invoice updated');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()
            ->route('sensipay.invoices.index')
            ->with('success', 'Invoice deleted');
    }

    protected function generateInvoiceCode(): string
    {
        $prefix = now()->format('ymd');
        $random = strtoupper(Str::random(4));

        return $prefix . $random;
    }
}
