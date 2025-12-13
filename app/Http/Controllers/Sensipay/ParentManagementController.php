<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class ParentManagementController extends Controller
{
    public function index()
    {
        $parents = User::where('role', 'parent')
            ->withCount('invoices')
            ->withSum('invoices as invoices_total_amount', 'total_amount')
            ->withSum('invoices as invoices_paid_amount', 'paid_amount')
            ->orderBy('name')
            ->paginate(50);

        return view('sensipay.parents.index', compact('parents'));
    }

    public function show(User $parent)
    {
        if ($parent->role !== 'parent') {
            abort(404);
        }

        $invoices = Invoice::with('student')
            ->where('parent_user_id', $parent->id)
            ->orderByDesc('created_at')
            ->get();

        return view('sensipay.parents.show', [
            'parent'   => $parent,
            'invoices' => $invoices,
        ]);
    }

    public function create()
    {
        return view('sensipay.parents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $parent = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'parent',
        ]);

        return redirect()->route('sensipay.parents.show', $parent)
            ->with('status', 'Akun orang tua berhasil dibuat.');
    }

    public function edit(User $parent)
    {
        if ($parent->role !== 'parent') {
            abort(404);
        }

        return view('sensipay.parents.edit', compact('parent'));
    }

    public function update(Request $request, User $parent)
    {
        if ($parent->role !== 'parent') {
            abort(404);
        }

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $parent->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $parent->name  = $validated['name'];
        $parent->email = $validated['email'];

        if (! empty($validated['password'])) {
            $parent->password = Hash::make($validated['password']);
        }

        $parent->save();

        return redirect()->route('sensipay.parents.show', $parent)
            ->with('status', 'Data orang tua berhasil diperbarui.');
    }

    public function attachInvoice(Request $request, User $parent)
    {
        if ($parent->role !== 'parent') {
            abort(404);
        }

        $validated = $request->validate([
            'invoice_code' => ['required', 'string'],
        ]);

        $invoice = Invoice::where('invoice_code', $validated['invoice_code'])->first();

        if (! $invoice) {
            return back()->with('error', 'Invoice dengan kode tersebut tidak ditemukan.');
        }

        $invoice->parent_user_id = $parent->id;
        $invoice->save();

        return back()->with('status', 'Invoice berhasil dikaitkan dengan orang tua ini.');
    }

    public function detachInvoice(User $parent, Invoice $invoice)
    {
        if ($parent->role !== 'parent') {
            abort(404);
        }

        if ($invoice->parent_user_id !== $parent->id) {
            return back()->with('error', 'Invoice ini tidak terhubung dengan orang tua tersebut.');
        }

        $invoice->parent_user_id = null;
        $invoice->save();

        return back()->with('status', 'Invoice berhasil dilepas dari orang tua ini.');
    }
}
