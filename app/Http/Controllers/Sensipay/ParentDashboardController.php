<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'parent') {
            abort(403, 'Hanya akun orang tua yang bisa mengakses halaman ini.');
        }

        $invoices = Invoice::with('student')
            ->where('parent_user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('sensipay.parent.dashboard', [
            'user'     => $user,
            'invoices' => $invoices,
        ]);
    }
}
