
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Student;
use App\Models\Program;
use App\Models\Invoice;

class JetDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ==== 1. User accounts ====

        // Owner
        $owner = User::where('email', 'owner@jet.com')->first();
        if (! $owner) {
            $owner = new User();
            $owner->name  = 'Owner JET';
            $owner->email = 'owner@jet.com';
            $owner->password = Hash::make('password');
            if (Schema::hasColumn('users', 'role')) {
                $owner->role = 'owner';
            }
            $owner->save();
        }

        // Operational director
        $operational = User::where('email', 'operational@jet.com')->first();
        if (! $operational) {
            $operational = new User();
            $operational->name  = 'Direktur Operasional JET';
            $operational->email = 'operational@jet.com';
            $operational->password = Hash::make('password');
            if (Schema::hasColumn('users', 'role')) {
                $operational->role = 'operational_director';
            }
            $operational->save();
        }

        // Academic director
        $academic = User::where('email', 'academic@jet.com')->first();
        if (! $academic) {
            $academic = new User();
            $academic->name  = 'Direktur Akademik JET';
            $academic->email = 'academic@jet.com';
            $academic->password = Hash::make('password');
            if (Schema::hasColumn('users', 'role')) {
                $academic->role = 'academic_director';
            }
            $academic->save();
        }

        // Parent
        $parent = User::where('email', 'parent1@jet.com')->first();
        if (! $parent) {
            $parent = new User();
            $parent->name  = 'Orang Tua Dawud';
            $parent->email = 'parent1@jet.com';
            $parent->password = Hash::make('password');
            if (Schema::hasColumn('users', 'role')) {
                $parent->role = 'parent';
            }
            $parent->save();
        }

        // ==== 2. Student: Dawud ====

        if (! Schema::hasTable('students')) {
            $this->command?->warn('Tabel students tidak ada, bagian student di-skip.');
            return;
        }

        $student = Student::where('name', 'Dawud')->first();
        if (! $student) {
            $student = new Student();
            $student->name = 'Dawud';

            if (Schema::hasColumn('students', 'school_name')) {
                $student->school_name = 'SDN Larangan 5';
            }
            if (Schema::hasColumn('students', 'grade')) {
                $student->grade = '6 SD';
            }
            if (Schema::hasColumn('students', 'notes')) {
                $student->notes = 'Demo student untuk Sensipay / Sensijet.';
            }
            if (Schema::hasColumn('students', 'parent_user_id')) {
                $student->parent_user_id = $parent->id;
            }

            $student->save();
        } else {
            if (Schema::hasColumn('students', 'parent_user_id') && ! $student->parent_user_id) {
                $student->parent_user_id = $parent->id;
                $student->save();
            }
        }

        // ==== 3. Program: Reguler 6 SD ====

        if (! Schema::hasTable('programs')) {
            $this->command?->warn('Tabel programs tidak ada, bagian program di-skip.');
        } else {
            $program = Program::where('name', 'Reguler 6 SD')->first();
            if (! $program) {
                $program = new Program();
                $program->name = 'Reguler 6 SD';

                if (Schema::hasColumn('programs', 'level')) {
                    $program->level = '6 SD';
                }
                if (Schema::hasColumn('programs', 'description')) {
                    $program->description = 'Program reguler kelas 6 SD (demo).';
                }
                if (Schema::hasColumn('programs', 'default_sessions')) {
                    $program->default_sessions = 40;
                }
                if (Schema::hasColumn('programs', 'base_price')) {
                    $program->base_price = 10000000;
                }
                if (Schema::hasColumn('programs', 'is_active')) {
                    $program->is_active = true;
                }

                $program->save();
            }
        }

        // ==== 4. Invoice demo untuk Dawud ====

        if (! Schema::hasTable('invoices')) {
            $this->command?->warn('Tabel invoices tidak ada, bagian invoice di-skip.');
            return;
        }

        $programId = isset($program) ? $program->id : null;

        $invoice = Invoice::where('student_id', $student->id)
            ->when($programId, fn ($q) => $q->where('program_id', $programId))
            ->first();

        if (! $invoice) {
            $invoice = new Invoice();

            if (Schema::hasColumn('invoices', 'invoice_code')) {
                $invoice->invoice_code = 'INV-DEMO-001';
            }

            if (Schema::hasColumn('invoices', 'student_id')) {
                $invoice->student_id = $student->id;
            }
            if (Schema::hasColumn('invoices', 'program_id') && $programId) {
                $invoice->program_id = $programId;
            }

            if (Schema::hasColumn('invoices', 'total_amount')) {
                $invoice->total_amount = 10000000;
            }
            if (Schema::hasColumn('invoices', 'paid_amount')) {
                $invoice->paid_amount = 0;
            }
            if (Schema::hasColumn('invoices', 'status')) {
                $invoice->status = 'unpaid';
            }
            if (Schema::hasColumn('invoices', 'due_date')) {
                $invoice->due_date = Carbon::now()->startOfMonth()->addDays(19)->toDateString();
            }

            $invoice->save();
        }
    }
}
