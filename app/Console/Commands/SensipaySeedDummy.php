<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SensipaySeedDummy extends Command
{
    /**
     * Nama command yang dipakai di CLI.
     *
     * @var string
     */
    protected $signature = 'sensipay:seed-dummy';

    /**
     * Deskripsi command.
     *
     * @var string
     */
    protected $description = 'Seed dummy data untuk testing Sensipay (parent, admin, invoices, payments pending).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        DB::transaction(function () {
            $this->info('Membuat dummy users...');

            // 1. Parent Dummy
            $parent = User::updateOrCreate(
                ['email' => 'parent.dummy@sensipay.test'],
                [
                    'name'     => 'Orang Tua Dummy',
                    'password' => bcrypt('12345678'),
                    'phone'    => '6282138040828',
                    'role'     => 'parent',
                ]
            );

            // 2. Admin Dummy
            $admin = User::updateOrCreate(
                ['email' => 'admin.dummy@sensipay.test'],
                [
                    'name'     => 'Admin Dummy',
                    'password' => bcrypt('12345678'),
                    'phone'    => '628999000111',
                    'role'     => 'owner',
                ]
            );

            // 3. Finance Dummy
            $finance = User::updateOrCreate(
                ['email' => 'finance.dummy@sensipay.test'],
                [
                    'name'     => 'Finance Dummy',
                    'password' => bcrypt('12345678'),
                    'phone'    => '628888777666',
                    'role'     => 'finance',
                ]
            );

            $this->info('Dummy users dibuat / diperbarui.');

            // 4. Student Dummy
            $student = Student::updateOrCreate(
                [
                    'name'   => 'Anak Dummy JET',
                    'grade'  => '6 SD',
                    'school' => 'SD Dummy',
                ],
                [
                    'parent_user_id' => $parent->id,
                ]
            );

            $this->info('Student dummy dibuat / diperbarui.');

            // 5. Program Dummy
            $program = Program::updateOrCreate(
                [
                    'name'  => 'Paket Reguler Dummy',
                    'level' => 'SD 5–6',
                ],
                [
                    'description' => 'Program dummy untuk testing Sensipay.',
                ]
            );

            $this->info('Program dummy dibuat / diperbarui.');

            // 6. Invoices Dummy
            $baseDate = Carbon::now()->addMonth();

            $invoiceRows = [
                [
                    'invoice_code' => 'TEST-1001',
                    'total_amount' => 1_500_000,
                    'paid_amount'  => 0,
                    'status'       => 'unpaid',
                    'due_date'     => $baseDate->copy()->addDays(1),
                ],
                [
                    'invoice_code' => 'TEST-8500-P1',
                    'total_amount' => 8_500_000,
                    'paid_amount'  => 7_000_000,
                    'status'       => 'partial',
                    'due_date'     => $baseDate->copy()->addDays(2),
                ],
                [
                    'invoice_code' => 'TEST-8500-P2',
                    'total_amount' => 8_500_000,
                    'paid_amount'  => 8_000_000,
                    'status'       => 'partial',
                    'due_date'     => $baseDate->copy()->addDays(3),
                ],
            ];

            $this->info('Membuat invoices dummy...');

            foreach ($invoiceRows as $row) {
                Invoice::updateOrCreate(
                    ['invoice_code' => $row['invoice_code']],
                    [
                        'student_id'     => $student->id,
                        'parent_user_id' => $parent->id,
                        'program_id'     => $program->id,
                        'total_amount'   => $row['total_amount'],
                        'paid_amount'    => $row['paid_amount'],
                        'status'         => $row['status'],
                        'due_date'       => $row['due_date'],
                    ]
                );
            }

            $this->info('Invoices dummy dibuat / diperbarui.');

            // 7. Payment pending untuk TEST-8500-P1
            $invoiceP1 = Invoice::where('invoice_code', 'TEST-8500-P1')->first();

            if ($invoiceP1) {
                Payment::create([
                    'invoice_id' => $invoiceP1->id,
                    'amount'     => 500_000,
                    'paid_at'    => Carbon::now(),
                    'method'     => 'transfer',
                    'note'       => 'Contoh pengajuan pembayaran dari parent dummy.',
                    'status'     => 'pending',
                    'proof_path' => null,
                ]);

                $this->info('Payment pending dummy dibuat untuk invoice TEST-8500-P1.');
            } else {
                $this->warn('Invoice TEST-8500-P1 tidak ditemukan, payment pending tidak dibuat.');
            }

            $this->line('');
            $this->info('=== Dummy Sensipay selesai dibuat ===');
            $this->line('Parent Dummy:  parent.dummy@sensipay.test / 12345678 (role: parent)');
            $this->line('Admin Dummy:   admin.dummy@sensipay.test  / 12345678 (role: owner)');
            $this->line('Finance Dummy: finance.dummy@sensipay.test / 12345678 (role: finance)');
        });

        return Command::SUCCESS;
    }
}