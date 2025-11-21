<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Helpers\StudentHelper;
use App\Models\Student;
use App\Models\Program;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyCustomerInvoiceImportController extends Controller
{
    /**
     * Form upload file legacy (customer + invoice).
     */
    public function showForm()
    {
        return view('sensipay.imports.legacy_customers_invoices');
    }

    /**
     * Proses import: buat user (student_parent) + invoice lama.
     *
     * Format CSV yang disarankan (header):
     * parent_name;parent_email;student_name;program_name;total_amount;paid_amount;status;invoice_code
     */
    public function process(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');

        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle, 0, ';');
        if (! $header) {
            return back()->withErrors(['file' => 'File kosong atau header tidak terbaca.']);
        }

        $header = array_map(function ($h) {
            return strtolower(trim($h));
        }, $header);

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            if (count($data) === 1 && $data[0] === null) {
                continue;
            }
            $rowAssoc = [];
            foreach ($header as $index => $key) {
                $rowAssoc[$key] = $data[$index] ?? null;
            }
            $rows[] = $rowAssoc;
        }

        fclose($handle);

        $createdUsers   = [];
        $existingUsers  = [];
        $createdInvoices = [];
        $failedRows     = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $parentName   = $row['parent_name'] ?? $row['nama_ortu'] ?? null;
                $parentEmail  = $row['parent_email'] ?? $row['email'] ?? null;
                $studentName  = $row['student_name'] ?? $row['nama_siswa'] ?? null;
                $programName  = $row['program_name'] ?? $row['program'] ?? null;
                $totalAmount  = $row['total_amount'] ?? $row['total'] ?? null;
                $paidAmount   = $row['paid_amount'] ?? 0;
                $status       = $row['status'] ?? null;
                $invoiceCode  = $row['invoice_code'] ?? null;

                if (! $parentEmail || ! $studentName || ! $programName || ! $totalAmount) {
                    $failedRows[] = [
                        'row'    => $row,
                        'reason' => 'Data wajib (email, siswa, program, total) tidak lengkap.',
                    ];
                    continue;
                }

                // 1) User (customer) sebagai student_parent
                $user = User::where('email', $parentEmail)->first();
                $generatedPassword = null;

                if (! $user) {
                    $generatedPassword = Str::random(10);

                    $user = User::create([
                        'name'     => $parentName ?: $parentEmail,
                        'email'    => $parentEmail,
                        'password' => bcrypt($generatedPassword),
                        'role'     => 'student_parent',
                    ]);

                    $createdUsers[] = [
                        'name'     => $user->name,
                        'email'    => $user->email,
                        'password' => $generatedPassword,
                    ];
                } else {
                    $existingUsers[] = [
                        'name'  => $user->name,
                        'email' => $user->email,
                    ];
                }

                // 2) Cari siswa
                $student = StudentHelper::findStudent($studentName);

                if (! $student) {
                    $failedRows[] = [
                        'row'    => $row,
                        'reason' => 'Siswa tidak ditemukan di database (nama: ' . $studentName . ')',
                    ];
                    continue;
                }

                // 3) Cari program
                $program = Program::whereRaw('LOWER(name) = ?', [mb_strtolower(trim($programName))])->first();

                if (! $program) {
                    $failedRows[] = [
                        'row'    => $row,
                        'reason' => 'Program tidak ditemukan di database (nama: ' . $programName . ')',
                    ];
                    continue;
                }

                $totalAmount = (int) $totalAmount;
                $paidAmount  = (int) $paidAmount;

                if (! $status) {
                    $status = $paidAmount >= $totalAmount ? 'paid' : 'unpaid';
                }

                // 4) Buat invoice lama
                $invoiceData = [
                    'student_id'   => $student->id,
                    'program_id'   => $program->id,
                    'total_amount' => $totalAmount,
                    'paid_amount'  => $paidAmount,
                    'status'       => $status,
                ];

                if ($invoiceCode) {
                    $invoiceData['invoice_code'] = $invoiceCode;
                }

                $invoice = Invoice::create($invoiceData);

                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'description' => $program->name,
                    'qty'         => 1,
                    'price'       => $totalAmount,
                    'total'       => $totalAmount,
                ]);

                $createdInvoices[] = [
                    'invoice_id'   => $invoice->id,
                    'student_name' => $student->name,
                    'program_name' => $program->name,
                    'total_amount' => $totalAmount,
                    'status'       => $status,
                ];
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors([
                'file' => 'Terjadi error saat import: ' . $e->getMessage(),
            ]);
        }

        return view('sensipay.imports.legacy_customers_invoices_result', [
            'createdUsers'    => $createdUsers,
            'existingUsers'   => $existingUsers,
            'createdInvoices' => $createdInvoices,
            'failedRows'      => $failedRows,
        ]);
    }
}
