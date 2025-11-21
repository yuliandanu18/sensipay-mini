<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Invoice;

class JetRealInvoicesSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     *
     * Catatan penting:
     * - Data di sini HANYA CONTOH STRUKTUR.
     * - Silakan ubah nama orang tua, siswa, nominal, dan tanggal
     *   agar sesuai dengan tagihan Bimbel JET asli milikmu.
     */
    public function run(): void
    {
        // === 1. Definisikan daftar program yang dipakai di Bimbel JET ===
        $programs = [
            [
                'code'        => 'REG-6SD',
                'name'        => 'Reguler 6 SD',
                'description' => 'Program Reguler Kelas 6 SD',
                'default_price' => 10000000, // silakan sesuaikan
            ],
            [
                'code'        => 'INT-LAB-SD',
                'name'        => 'Intensif Labschool SD',
                'description' => 'Program Intensif Labschool SD',
                'default_price' => 2500000,
            ],
            [
                'code'        => 'REG-7SMP',
                'name'        => 'Reguler 7 SMP',
                'description' => 'Program Reguler Kelas 7 SMP',
                'default_price' => 12000000,
            ],
        ];

        $programMap = [];
        foreach ($programs as $p) {
            $program = Program::firstOrCreate(
                ['code' => $p['code']],
                [
                    'name'          => $p['name'],
                    'description'   => $p['description'] ?? null,
                    'base_price'    => $p['default_price'] ?? 0,
                    'is_active'     => true,
                ]
            );
            $programMap[$p['code']] = $program;
        }

        // === 2. Definisikan orang tua + siswa + tagihan (contoh struktur) ===
        // Silakan ganti bagian ini dengan data asli (nama, nominal, tanggal).
        $data = [
            [
                'parent_name'   => 'Yuliandanu (Ayah Dawud)',   // ganti sesuai
                'parent_email'  => 'parent1@jet.com',           // atau email asli
                'student_name'  => 'Dawud',
                'school_grade'  => '6 SD',
                'program_code'  => 'REG-6SD',
                'invoice_code'  => 'INV-JET-2025-001',
                'total_amount'  => 10000000,
                'paid_amount'   => 2000000,
                'status'        => 'partial',
                'due_date'      => now()->setDay(20)->format('Y-m-d'),  // tgl 20 bulan ini
            ],
            [
                'parent_name'   => 'Mama Viola',                // contoh ortu lain
                'parent_email'  => 'parent2@jet.com',
                'student_name'  => 'Viola',
                'school_grade'  => '5 SD',
                'program_code'  => 'INT-LAB-SD',
                'invoice_code'  => 'INV-JET-2025-002',
                'total_amount'  => 2500000,
                'paid_amount'   => 0,
                'status'        => 'unpaid',
                'due_date'      => now()->setDay(20)->format('Y-m-d'),
            ],
            [
                'parent_name'   => 'Orang Tua Mikhael',
                'parent_email'  => 'parent3@jet.com',
                'student_name'  => 'Mikhael',
                'school_grade'  => '7 SMP',
                'program_code'  => 'REG-7SMP',
                'invoice_code'  => 'INV-JET-2025-003',
                'total_amount'  => 12000000,
                'paid_amount'   => 6000000,
                'status'        => 'partial',
                'due_date'      => now()->setDay(20)->format('Y-m-d'),
            ],
        ];

        foreach ($data as $row) {
            // 1) Buat / ambil user parent
            $parent = User::firstOrCreate(
                ['email' => $row['parent_email']],
                [
                    'name'     => $row['parent_name'],
                    'password' => Hash::make('password'), // ubah manual kalau mau
                    'role'     => 'parent',
                ]
            );

            // 2) Buat / ambil student
            $student = Student::firstOrCreate(
                [
                    'name'      => $row['student_name'],
                    'parent_id' => $parent->id,
                ],
                [
                    'grade'     => $row['school_grade'],
                    'school'    => 'Bimbel JET',
                    'note'      => null,
                ]
            );

            // 3) Ambil program
            $program = $programMap[$row['program_code']] ?? null;

            // 4) Buat / update invoice
            $invoice = Invoice::updateOrCreate(
                ['invoice_code' => $row['invoice_code']],
                [
                    'student_id'   => $student->id,
                    'program_id'   => $program?->id,
                    'total_amount' => $row['total_amount'],
                    'paid_amount'  => $row['paid_amount'],
                    'status'       => $row['status'],
                    'due_date'     => $row['due_date'],
                ]
            );
        }
    }
}
