<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kbm_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kbm_session_id')->constrained('kbm_sessions')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();

            $table->enum('status', ['present', 'absent', 'sick', 'leave'])->default('present');
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kbm_attendance');
    }
};
