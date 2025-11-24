<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
/**
     * Generate dummy data untuk development:
     * - reset payments & invoices
     * - buat 1 parent dummy
     * - buat 1 student dummy
     * - buat 1 program dummy
     * - buat 3 invoice contoh
     */
    public function resetDummy(Request $request)
{
    // Supaya route lama `sensipay.invoices.reset-dummy` tetap jalan,
    // kita cukup delegasikan ke generateDummy.
    return $this->generateDummy($request);
}
    public function generateDummy(Request $request)
{
    if (! app()->environment('local')) {
        abort(403, 'Fitur ini hanya untuk environment LOCAL.');
    }

    $emailDummy = 'parent.dummy@sensipay.test';
    $waDummy    = '081210382121';       // boleh ganti, WA dummy/admin
    $plainPass  = 'parentdummy123';      // biar kamu hafal

    DB::transaction(function () use ($emailDummy, $waDummy, $plainPass) {

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Payment::truncate();
        Invoice::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 🔑 Kunci pakai EMAIL, bukan phone
        $parent = User::updateOrCreate(
            ['email' => $emailDummy],
            [
                'name'     => 'Orang Tua Dummy',
                'password' => bcrypt($plainPass),
                'role'     => 'parent',
                'phone'    => $waDummy,
            ]
        );

        // lanjut student / program / invoices dummy persis punyamu tadi...
        // (boleh tetap pakai Student::firstOrCreate, Program::firstOrCreate, dll)
    });

    return redirect()
        ->route('sensipay.invoices.index')
        ->with([
            'success'               => 'Dummy data berhasil digenerate.',
            'dummy_parent_email'    => $emailDummy,
            'dummy_parent_password' => $plainPass,
        ]);
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
    // 1. Kalau invoice sudah lunas, jangan izinkan edit sama sekali
    if ($invoice->is_paid) {
        return redirect()
            ->route('sensipay.invoices.show', $invoice)
            ->with('error', 'Invoice ini sudah LUNAS dan dikunci. Jika perlu koreksi, lakukan lewat modul keuangan (bukan edit invoice langsung).');
    }

    // 2. Validasi input
    $data = $request->validate([
        'student_id'    => 'required|exists:students,id',
        'program_id'    => 'required|exists:programs,id',
        'total_amount'  => 'required|numeric|min:0',
        'due_date'      => 'nullable|date',
        'status'        => 'required|in:unpaid,partial,paid',
    ]);

    $originalTotal = (float) ($invoice->total_amount ?? 0);
    $paid          = (float) ($invoice->paid_amount ?? 0);
    $newTotal      = (float) $data['total_amount'];

    $warning = null;

    // 3. Kalau sudah ada pembayaran, jaga-jaga:
    if ($paid > 0) {
        // a) Jangan sampai total baru lebih kecil dari yang sudah dibayar
        if ($newTotal < $paid) {
            return back()
                ->withInput()
                ->with('error', 'Total tagihan tidak boleh lebih kecil dari jumlah yang sudah dibayar (Rp ' . number_format($paid, 0, ',', '.') . ').');
        }

        // b) Kalau total diubah, kasih warning ke admin
        if ($newTotal !== $originalTotal) {
            $warning = 'Perhatian: Total tagihan diubah padahal sudah ada pembayaran. Pastikan perubahan ini sudah dikonfirmasi dengan orang tua.';
        }
    }

    // 4. Update data
    $invoice->update($data);

    // 5. Kalau status manual nggak sinkron dengan paid_amount, biarkan dulu.
    // Admin bisa pakai tombol "Recalc Status" untuk sinkron ulang.

    $redirect = redirect()
        ->route('sensipay.invoices.show', $invoice)
        ->with('success', 'Invoice berhasil diperbarui.');

    if ($warning) {
        $redirect->with('warning', $warning);
    }

    return $redirect;
}
public function recalcStatus(Invoice $invoice)
{
    // Hitung ulang status berdasarkan total_amount & paid_amount
    $invoice->recalcStatus();

    return back()->with('success', 'Status invoice sudah disesuaikan dengan jumlah pembayaran (recalc status).');
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
