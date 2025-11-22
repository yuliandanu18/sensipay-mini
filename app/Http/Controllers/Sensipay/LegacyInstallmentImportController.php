<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LegacyInstallmentImportController extends Controller
{
    /**
     * Tampilkan form upload file legacy (BAHANIMPORT.csv).
     */
    public function showForm()
    {
        return view('sensipay.legacy-installments.import');
    }

    /**
     * Import invoice + payment + parent dari file CSV (export dari BAHANIMPORT.xlsx).
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ], [
            'file.required' => 'File BAHANIMPORT (CSV) wajib di-upload.',
            'file.mimes'    => 'Gunakan file CSV (export dari BAHANIMPORT.xlsx).',
        ]);

        $file = $request->file('file');

        if (! $file->isValid()) {
            return back()->with('error', 'File upload tidak valid.');
        }

        $path = $file->getRealPath();

        [$header, $rows] = $this->readCsvWithHeader($path);

        if (empty($rows)) {
            return back()->with('error', 'File CSV kosong atau header tidak terbaca.');
        }

        // Normalisasi nama kolom
        $map = $this->buildHeaderMap($header, [
            'student_name',
            'invoice_code',
            'item_description',
            'item_total_amount',
            'status_invoice',
            'phone_number',
            'email',
        ]);

        if ($map === null) {
            return back()->with('error', 'Header CSV tidak sesuai. Pastikan kolom: student_name, invoice_code, item_description, item_total_amount, status_invoice, phone_number, email.');
        }

        // Group rows by invoice_code
        $grouped = [];
        foreach ($rows as $row) {
            $invoiceCode = trim($row[$map['invoice_code']] ?? '');
            if ($invoiceCode === '') {
                continue;
            }
            $grouped[$invoiceCode][] = $row;
        }

        $createdInvoices  = 0;
        $updatedInvoices  = 0;
        $createdParents   = 0;
        $createdPayments  = 0;
        $skippedInvoices  = 0;
        $parentSummaries  = []; // untuk menampilkan email + password baru di view

        DB::beginTransaction();

        try {
            foreach ($grouped as $invoiceCode => $rowsForInvoice) {
                // Ambil info dasar dari baris pertama
                $first       = $rowsForInvoice[0];
                $studentName = trim($first[$map['student_name']] ?? '');
                $parentEmail = trim($first[$map['email']] ?? '');
                $parentPhone = trim($first[$map['phone_number']] ?? '');

                if ($studentName === '' || $parentEmail === '') {
                    $skippedInvoices++;
                    continue;
                }

                // Cari / buat student
                $student = Student::where('name', $studentName)->first();

                if (! $student) {
                    // Tergantung kebijakan: bisa firstOrCreate, atau skip.
                    // Di sini kita pilih firstOrCreate agar semua invoice tetap terdata.
                    $student = Student::create([
                        'name' => $studentName,
                    ]);
                }

                // Cari / buat parent user
                $parent = User::where('email', $parentEmail)->first();
                $rawPassword = null;

                if (! $parent) {
                    $rawPassword = Str::random(10);

                    $parent = User::create([
                        'name'     => 'Orang Tua ' . $studentName,
                        'email'    => $parentEmail,
                        'password' => Hash::make($rawPassword),
                        'role'     => 'parent',
                    ]);

                    $createdParents++;

                    $parentSummaries[] = [
                        'email'         => $parentEmail,
                        'name'          => $parent->name,
                        'phone'         => $parentPhone,
                        'student_name'  => $studentName,
                        'password'      => $rawPassword,
                        'invoice_code'  => $invoiceCode,
                    ];
                }

                // Hitung total & paid per invoice
                $totalAmount = 0.0;
                $paidAmount  = 0.0;
                $paymentLines = [];

                foreach ($rowsForInvoice as $row) {
                    $amountRaw = (float) ($row[$map['item_total_amount']] ?? 0);
                    $desc      = trim($row[$map['item_description']] ?? '');

                    if ($amountRaw >= 0) {
                        // Positif / nol -> item invoice (harga, free, dll)
                        $totalAmount += $amountRaw;
                    } else {
                        // Negatif -> pembayaran / angsuran / DP
                        $paidAmount += abs($amountRaw);
                        $paymentLines[] = [
                            'amount_raw' => $amountRaw,
                            'description'=> $desc,
                        ];
                    }
                }

                // Tentukan status invoice
                $status = 'unpaid';
                if ($paidAmount >= $totalAmount && $totalAmount > 0) {
                    $status = 'paid';
                } elseif ($paidAmount > 0 && $paidAmount < $totalAmount) {
                    $status = 'partial';
                }

                // Create or update invoice
                $invoice = Invoice::where('invoice_code', $invoiceCode)->first();

                if (! $invoice) {
                    $invoice = Invoice::create([
                        'invoice_code'   => $invoiceCode,
                        'student_id'     => $student->id,
                        'parent_user_id' => $parent->id,
                        'program_id'     => null,
                        'total_amount'   => $totalAmount,
                        'paid_amount'    => $paidAmount,
                        'status'         => $status,
                        'due_date'       => null,
                    ]);
                    $createdInvoices++;
                } else {
                    $invoice->student_id     = $student->id;
                    $invoice->parent_user_id = $parent->id;
                    $invoice->total_amount   = $totalAmount;
                    $invoice->paid_amount    = $paidAmount;
                    $invoice->status         = $status;
                    $invoice->save();
                    $updatedInvoices++;
                }

                // Hapus payments lama? opsional.
                // Kalau mau clear dulu:
                // $invoice->payments()->delete();

                // Tambahkan payment untuk setiap baris negatif
                foreach ($paymentLines as $pl) {
                    $amountRaw = (float) $pl['amount_raw'];
                    $amount    = abs($amountRaw);
                    $desc      = $pl['description'];

                    $paidAt = $this->parseDateFromDescription($desc) ?? now();

                    if ($desc === '') {
                        $desc = 'Update transaksi ' . $paidAt->format('d/m/Y');
                    }

                    Payment::create([
                        'invoice_id'  => $invoice->id,
                        'amount'      => $amount,
                        'paid_at'     => $paidAt,
                        'method'      => 'legacy-import',
                        'description' => $desc,
                    ]);

                    $createdPayments++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }

        $statusMessage = sprintf(
            'Import selesai. Invoice baru: %d, invoice di-update: %d, parent baru: %d, payment dibuat: %d, invoice dilewati: %d',
            $createdInvoices,
            $updatedInvoices,
            $createdParents,
            $createdPayments,
            $skippedInvoices
        );

        return back()
            ->with('status', $statusMessage)
            ->with('new_parents', $parentSummaries);
    }

    /**
     * Baca CSV dan kembalikan [header, rows]
     */
    protected function readCsvWithHeader(string $path): array
    {
        $handle = fopen($path, 'r');
        if (! $handle) {
            return [null, []];
        }

        $header = null;
        $rows   = [];

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            // skip baris kosong
            if (count($data) === 1 && trim($data[0]) === '') {
                continue;
            }

            if ($header === null) {
                $header = $data;
                continue;
            }

            $rows[] = $data;
        }

        fclose($handle);

        return [$header, $rows];
    }

    /**
     * Buat map nama kolom -> index, dengan normalisasi sederhana.
     */
    protected function buildHeaderMap(array $header, array $expectedColumns): ?array
    {
        $normalized = [];
        foreach ($header as $index => $col) {
            $key = strtolower(trim($col));
            $key = str_replace([' ', '-'], '_', $key);
            $normalized[$key] = $index;
        }

        $map = [];
        foreach ($expectedColumns as $col) {
            if (! array_key_exists($col, $normalized)) {
                return null;
            }
            $map[$col] = $normalized[$col];
        }

        return $map;
    }

    /**
     * Coba parsing tanggal dari deskripsi pembayaran.
     * Contoh yang didukung:
     * - "Pembayaran DP 24/09/25"
     * - "Angsuran ke-2 (01 Oktober 2025)"
     * - "Pelunasan 12 Nov 2025"
     */
    protected function parseDateFromDescription(?string $desc): ?Carbon
    {
        if (! $desc) {
            return null;
        }

        $desc = trim($desc);

        // 1) Format dd/mm/yy atau dd-mm-yy atau dd/mm/yyyy
        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})/', $desc, $m)) {
            $day   = (int) $m[1];
            $month = (int) $m[2];
            $year  = (int) $m[3];
            if ($year < 100) {
                $year += 2000; // asumsi 20xx
            }

            try {
                return Carbon::createFromDate($year, $month, $day);
            } catch (\Exception $e) {
                // lanjut ke pola lain
            }
        }

        // 2) Format "12 Nov 2025" / "12 November 2025" / dll
        if (preg_match('/(\d{1,2})\s+([A-Za-zÀ-ÿ\.]+)\s+(\d{2,4})/u', $desc, $m)) {
            $day   = (int) $m[1];
            $monthName = strtolower(str_replace('.', '', $m[2]));
            $year  = (int) $m[3];
            if ($year < 100) {
                $year += 2000;
            }

            $monthMap = [
                'januari' => 1, 'jan' => 1,
                'februari' => 2, 'feb' => 2,
                'maret' => 3, 'mar' => 3,
                'april' => 4, 'apr' => 4,
                'mei' => 5,
                'juni' => 6, 'jun' => 6,
                'juli' => 7, 'jul' => 7,
                'agustus' => 8, 'agt' => 8, 'aug' => 8,
                'september' => 9, 'sept' => 9, 'sep' => 9,
                'oktober' => 10, 'okt' => 10, 'oct' => 10,
                'november' => 11, 'nov' => 11,
                'desember' => 12, 'des' => 12, 'dec' => 12,
            ];

            if (isset($monthMap[$monthName])) {
                $month = $monthMap[$monthName];
                try {
                    return Carbon::createFromDate($year, $month, $day);
                } catch (\Exception $e) {
                    // fallback di bawah
                }
            }
        }

        // Kalau gagal semua, return null
        return null;
    }
}
