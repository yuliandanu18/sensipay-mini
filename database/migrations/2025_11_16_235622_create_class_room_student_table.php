<?php
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_room_student', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(ClassRoom::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(Student::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('status', ['active', 'waiting'])->default('active');
            $table->timestamps();

            $table->unique(['class_room_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_room_student');
    }
};
