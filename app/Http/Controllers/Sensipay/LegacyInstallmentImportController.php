<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;

class LegacyInstallmentImportController extends Controller
{
    public function showForm()
    {
        return view('sensipay.legacy_installments.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');

        if (! $handle) {
            return back()->with('error', 'Gagal membaca file.');
        }

        $header = fgetcsv($handle, 0, ',');
        if (! $header) {
            return back()->with('error', 'File kosong atau header tidak terbaca.');
        }

        $map = [];
        foreach ($header as $i => $col) {
            $key = strtolower(trim($col));
            $key = str_replace([' ', '-'], '_', $key);
            $map[$key] = $i;
        }

        foreach (['invoice_code', 'item_total_amount'] as $col) {
            if (! isset($map[$col])) {
                return back()->with('error', "Kolom '{$col}' tidak ditemukan di header CSV.");
            }
        }

        $dataByInvoice = [];

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $nonEmpty = array_filter($row, function ($v) {
                return $v !== null && $v !== '';
            });
            if (count($nonEmpty) === 0) {
                continue;
            }

            $code = isset($row[$map['invoice_code']])
                ? trim($row[$map['invoice_code']])
                : '';

            if ($code === '') {
                continue;
            }

            $rawAmount = $row[$map['item_total_amount']] ?? 0;

            $normalized = str_replace([' ', '.', ','], ['', '', '.'], (string) $rawAmount);
            if (! is_numeric($normalized)) {
                $amount = 0.0;
            } else {
                $amount = (float) $normalized;
            }

            if (! isset($dataByInvoice[$code])) {
                $dataByInvoice[$code] = [
                    'charges'  => 0.0,
                    'payments' => 0.0,
                ];
            }

            if ($amount > 0) {
                $dataByInvoice[$code]['charges'] += $amount;
            } elseif ($amount < 0) {
                $dataByInvoice[$code]['payments'] += -$amount;
            }
        }

        fclose($handle);

        $updated  = 0;
        $notFound = [];

        DB::beginTransaction();

        foreach ($dataByInvoice as $code => $totals) {
            $invoice = Invoice::where('invoice_code', $code)->first();

            if (! $invoice) {
                $notFound[] = $code;
                continue;
            }

            $total = $totals['charges'];
            $paid  = $totals['payments'];

            $invoice->total_amount = $total;
            $invoice->paid_amount  = $paid;

            if ($paid <= 0) {
                $invoice->status = 'unpaid';
            } elseif ($paid < $total) {
                $invoice->status = 'partial';
            } else {
                $invoice->status = 'paid';
            }

            $invoice->save();
            $updated++;
        }

        DB::commit();

        $message = "Berhasil mengupdate {$updated} invoice.";
        if (! empty($notFound)) {
            $message .= ' Invoice tidak ditemukan untuk kode: ' . implode(', ', array_slice($notFound, 0, 20));
        }

        return back()->with('status', $message);
    }
}
