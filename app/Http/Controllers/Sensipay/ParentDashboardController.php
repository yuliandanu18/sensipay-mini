<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class ParentDashboardController extends Controller
{
    /**
     * Tampilkan dashboard orang tua murid.
     *
     * Menampilkan daftar invoice yang terhubung dengan parent_user_id = user yang login.
     */
    public function index()
    {
        $user = Auth::user();

        // Pastikan ini hanya untuk role parent
        if (! $user || $user->role !== 'parent') {
            abort(403, 'Akses khusus untuk Orang Tua Murid.');
        }

       $invoices = Invoice::with(['student'])
    ->where('parent_user_id', $user->id)
    ->orderByDesc('created_at')
    ->get();


        return view('sensipay.parent.dashboard', [
            'user'     => $user,
            'invoices' => $invoices,
        ]);
    }
}
