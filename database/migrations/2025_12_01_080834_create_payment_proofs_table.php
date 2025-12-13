<?php

// database/migrations/2025_12_01_000000_create_payment_proofs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('uploaded_by'); // user (parent) yang upload

            $table->string('file_path'); // path file di storage
            $table->unsignedBigInteger('amount')->nullable(); // nominal yang di-claim
            $table->date('transfer_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('status')->default('pending'); // pending|approved|rejected

            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('rejection_reason')->nullable();

            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            $table->foreign('uploaded_by')->references('id')->on('users')->cascadeOnDelete();
            // verified_by juga ke users, tapi bisa pakai nullable tanpa FK kalau mau simpel
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
    }
};
