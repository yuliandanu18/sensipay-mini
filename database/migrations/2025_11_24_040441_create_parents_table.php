<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parent_student_registrations', function (Blueprint $table) {
            $table->id();

            // User parent yang login
            $table->foreignId('parent_user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Wajib
            $table->string('student_name');    
            $table->string('grade');           
            $table->string('school_name');     
            $table->string('program_choice');  
            $table->string('parent_phone');    

            // Opsional
            $table->string('student_nickname')->nullable();
            $table->string('relation_type')->nullable();
            $table->text('address')->nullable();
            $table->text('academic_notes')->nullable();

            // Status alur admin
            $table->enum('status', ['submitted', 'reviewed', 'converted'])
                ->default('submitted');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_student_registrations');
    }
};
