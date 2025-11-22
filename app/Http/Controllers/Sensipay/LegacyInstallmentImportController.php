<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\Payment;

class LegacyInstallmentImportController extends Controller
{
    /**
     * Tampilkan form upload CSV legacy.
     */
    public function showForm()
    {
        return view('sensipay.legacy-installments.import');
    }

    /**
     * Import data legacy dari CSV.
     *
     * Format minimal kolom yang diharapkan:
     * parent_name, parent_email, student_name, invoice_code, item, amount
     *
     * amount boleh positif (harga), 0 (free), negatif (diskon / pembayaran).
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');

        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            return back()->with('error', 'Tidak bisa membaca file CSV.');
        }

        $header = null;
        $rows   = [];

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if (! $header) {
                $header = array_map('trim', $data);
                continue;
            }

            if (count($data) === 1 && trim($data[0]) === '') {
                continue;
            }

            $row = [];
            foreach ($header as $i => $col) {
                $row[$col] = $data[$i] ?? null;
            }

            $rows[] = $row;
        }

        fclose($handle);

        if (empty($rows)) {
            return back()->with('error', 'Tidak ada data di file CSV.');
        }

        $grouped = [];
        foreach ($rows as $row) {
            $code = trim($row['invoice_code'] ?? '');
            if ($code === '') {
                // Lewati baris tanpa kode invoice
                continue;
            }
            $grouped[$code][] = $row;
        }

        $stats = [
            'invoices_created'   => 0,
            'invoices_updated'   => 0,
            'payments_created'   => 0,
            'parents_created'    => 0,
            'parents_existing'   => 0,
            'students_created'   => 0,
            'students_existing'  => 0,
            'skipped'            => 0,
        ];

        DB::beginTransaction();

        try {
            foreach ($grouped as $invoiceCode => $rowsPerInvoice) {

                $first = $rowsPerInvoice[0];

                $parentName  = trim($first['parent_name']  ?? '');
                $parentEmail = trim($first['parent_email'] ?? '');
                $studentName = trim($first['student_name'] ?? '');

                // --- Parent (User dengan role parent) ---
                $parent = null;
                if ($parentEmail !== '') {
                    $parent = User::where('email', $parentEmail)->first();
                }

                if (! $parent && $parentName !== '') {
                    $parent = User::create([
                        'name'     => $parentName,
                        'email'    => $parentEmail ?: null,
                        'password' => bcrypt(str()->random(10)),
                        'role'     => 'parent',
                    ]);
                    $stats['parents_created']++;
                } elseif ($parent) {
                    $stats['parents_existing']++;
                }

                // --- Student ---
                $student = null;
                if ($studentName !== '') {
                    $student = Student::firstOrCreate(
                        ['name' => $studentName],
                        ['school_name' => null]
                    );
                    $wasRecentlyCreated = $student->wasRecentlyCreated;
                    if ($wasRecentlyCreated) {
                        $stats['students_created']++;
                    } else {
                        $stats['students_existing']++;
                    }
                }

                // Hitung base_price, discount, dan daftar payments
                $basePrice = 0;
                $discount  = 0;
                $payments  = [];

                foreach ($rowsPerInvoice as $row) {
                    $item   = trim($row['item'] ?? '');
                    $amount = (float) str_replace(['.', ' '], ['', ''], $row['amount'] ?? 0);

                    if ($amount > 0 && ! str_contains(strtolower($item), 'diskon')) {
                        // Positif dan bukan diskon → anggap harga utama
                        $basePrice += $amount;
                    } elseif ($amount < 0 && str_contains(strtolower($item), 'diskon')) {
                        // Diskon (negatif)
                        $discount += abs($amount);
                    } elseif ($amount < 0) {
                        // Pembayaran / angsuran
                        $payments[] = [
                            'amount' => abs($amount),
                            'note'   => $item,
                        ];
                    } else {
                        // amount == 0 (free item) → tidak mempengaruhi angka
                        continue;
                    }
                }

                if ($basePrice <= 0) {
                    $stats['skipped']++;
                    continue;
                }

                $totalAmount = max(0, $basePrice - $discount);
                $paidSum     = array_sum(array_column($payments, 'amount'));

                // Cari atau buat invoice
                $invoice = Invoice::where('invoice_code', $invoiceCode)->first();

                $data = [
                    'student_id'     => $student?->id,
                    'parent_user_id' => $parent?->id,
                    'total_amount'   => $totalAmount,
                    'paid_amount'    => $paidSum,
                ];

                if ($paidSum <= 0) {
                    $data['status'] = 'unpaid';
                } elseif ($paidSum > 0 && $paidSum < $totalAmount) {
                    $data['status'] = 'partial';
                } else {
                    $data['status'] = 'paid';
                }

                if ($invoice) {
                    $invoice->update($data);
                    $stats['invoices_updated']++;
                } else {
                    $invoice = Invoice::create(array_merge([
                        'invoice_code' => $invoiceCode,
                    ], $data));
                    $stats['invoices_created']++;
                }

                // Buat payment untuk setiap angsuran
                foreach ($payments as $p) {
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount'     => $p['amount'],
                        'paid_at'    => now(),
                        'method'     => 'legacy-import',
                        'note'       => $p['note'],
                    ]);
                    $stats['payments_created']++;
                }
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'Terjadi error saat import legacy: ' . $e->getMessage());
        }

        return back()->with('status', 'Import legacy selesai. '
            .'Invoice baru: ' . $stats['invoices_created']
            .', invoice di-update: ' . $stats['invoices_updated']
            .', payment dibuat: ' . $stats['payments_created']
            .', parent baru: ' . $stats['parents_created']
            .', student baru: ' . $stats['students_created']
        );
    }
}
