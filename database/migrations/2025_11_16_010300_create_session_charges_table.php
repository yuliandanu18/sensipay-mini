<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('session_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kbm_session_id')->constrained('kbm_sessions')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();

            $table->decimal('amount', 14, 2)->default(0);
            $table->string('description')->nullable();
            $table->boolean('approved_by_academic')->default(false);
            $table->boolean('approved_by_operational')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_charges');
    }
};
