<?php

namespace App\Http\Controllers\Sensipay;

use App\Http\Controllers\Controller;
use App\Helpers\StudentHelper;
use App\Models\Student;
use App\Models\Program;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceImportController extends Controller
{
    /**
     * Tampilkan form upload file invoice (Excel/CSV).
     */
    public function showForm()
    {
        return view('sensipay.invoices.import');
    }

    /**
     * Terima file, baca isi, lalu tampilkan PREVIEW sebelum disimpan.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx'],
        ]);

        $file = $request->file('file');

        // Sederhana: kalau CSV → pakai fgetcsv; kalau XLS/XLSX,
        // aku anggap kamu pakai library Maatwebsite\Excel.
        // Di sini aku tulis versi CSV generic, kamu bisa ganti sesuai impor lamamu.

        $rows = [];
        if ($file->getClientOriginalExtension() === 'csv' || $file->getClientOriginalExtension() === 'txt') {
            $handle = fopen($file->getRealPath(), 'r');

            // Asumsi baris pertama = header
            $header = fgetcsv($handle, 0, ';');
            if (! $header) {
                return back()->withErrors(['file' => 'File kosong atau header tidak terbaca.']);
            }

            // Normalisasi header ke lowercase
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
        } else {
            // Kalau kamu memang pakai Maatwebsite\Excel,
            // idealnya di sini pakai Excel::toArray(...).
            // Untuk sekarang, kita fokus pola logikanya dulu.
            return back()->withErrors([
                'file' => 'Untuk sementara contoh ini hanya mendukung CSV. Sesuaikan dengan logic import Excel yang sudah kamu punya.',
            ]);
        }

        $preview = [];
        $failed  = [];

        foreach ($rows as $row) {
            // Sesuaikan nama kolom dengan header file kamu
            $namaSiswa  = $row['nama_siswa'] ?? $row['nama'] ?? null;
            $namaProgram = $row['program'] ?? $row['nama_program'] ?? null;
            $total       = $row['total'] ?? $row['total_item'] ?? $row['jumlah'] ?? null;

            $student = StudentHelper::findStudent($namaSiswa);

            $program = null;
            if ($namaProgram) {
                $program = Program::whereRaw('LOWER(name) = ?', [mb_strtolower(trim($namaProgram))])->first();
            }

            $ok = $student && $program && $total;

            $rowPreview = [
                'raw'         => $row,
                'nama_siswa'  => $namaSiswa,
                'nama_program'=> $namaProgram,
                'total'       => $total,
                'student'     => $student,
                'program'     => $program,
                'ok'          => $ok,
            ];

            $preview[] = $rowPreview;

            if (! $ok) {
                $failed[] = $rowPreview;
            }
        }

        // Simpan sementara di session supaya bisa diproses saat user klik "Import Sekarang"
        $request->session()->put('sensipay_import_preview', $preview);

        return view('sensipay.invoices.import_preview', [
            'preview' => $preview,
            'failed'  => $failed,
        ]);
    }

    /**
     * Eksekusi insert ke database setelah user setuju dari layar preview.
     */
    public function process(Request $request)
    {
        $preview = $request->session()->get('sensipay_import_preview', []);

        if (empty($preview)) {
            return redirect()
                ->route('sensipay.invoices.import.form')
                ->withErrors(['file' => 'Data preview tidak ditemukan. Silakan upload ulang file.']);
        }

        DB::beginTransaction();

        try {
            foreach ($preview as $row) {
                if (! $row['ok']) {
                    // Lewati yang gagal
                    continue;
                }

                /** @var \App\Models\Student $student */
                $student = $row['student'];
                /** @var \App\Models\Program $program */
                $program = $row['program'];
                $total   = (int) $row['total'];

                // Buat invoice
                $invoice = Invoice::create([
                    'student_id'   => $student->id,
                    'program_id'   => $program->id,
                    'total_amount' => $total,
                    'paid_amount'  => 0,
                    'status'       => 'unpaid',
                    // Tambah kolom lain sesuai schema kamu, misal invoice_code, due_date, dll
                ]);

                // Item invoice minimal 1 baris
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description'=> $row['nama_program'] ?? ('Program ' . $program->name),
                    'qty'        => 1,
                    'price'      => $total,
                    'total'      => $total,
                ]);
            }

            DB::commit();

            // Clear session preview
            $request->session()->forget('sensipay_import_preview');

            return redirect()
                ->route('sensipay.invoices.index')
                ->with('success', 'Import invoice selesai. Invoice yang valid sudah masuk ke sistem.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withErrors(['file' => 'Terjadi error saat menyimpan ke database: ' . $e->getMessage()]);
        }
    }
}
