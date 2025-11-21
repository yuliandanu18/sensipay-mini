<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kbm_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            $table->unsignedInteger('duration_minutes')->default(90);

            $table->enum('type', ['regular', 'private', 'makeup', 'bonus'])->default('regular');

            $table->boolean('is_counted_in_quota')->default(true);
            $table->boolean('is_chargeable')->default(false);

            $table->decimal('teacher_fee', 12, 2)->default(0);
            $table->string('topic')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kbm_sessions');
    }
};
