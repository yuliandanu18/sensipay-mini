<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invoice;

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
}
