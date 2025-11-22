<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LegacyInstallmentImportController extends Controller
{
    /**
     * Tampilkan form upload cicilan legacy.
     */
    public function showForm()
    {
        // Sesuaikan dengan nama view yang kamu pakai
        // contoh: resources/views/sensipay/legacy-installments/import.blade.php
        return view('sensipay.legacy-installments.import');
    }

    /**
     * Proses file upload dan import ke tabel invoice + installments.
     */
    public function import(Request $request)
    {
        // 1. Validasi file
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt'],
        ], [
            'file.required' => 'File cicilan legacy wajib di-upload.',
        ]);

        $file = $request->file('file');

        // TODO: di sini nanti logic baca Excel/CSV kamu
        // Contoh kerangka umum (silakan sesuaikan):
        /*
        DB::transaction(function () use ($file) {
            // - parse file (pakai library seperti maatwebsite/excel, dsb.)
            // - loop tiap baris:
            //      - cari / buat Invoice terkait
            //      - insert ke InvoiceInstallment
        });
        */

        return redirect()
            ->route('sensipay.legacy-installments.import.form')
            ->with('status', 'Import cicilan legacy berhasil diproses (dummy).');
    }
}
