<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('wa_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('parents')->nullOnDelete();
            $table->string('target',30);
            $table->text('message');
            $table->string('status',50)->default('sent');
            $table->string('provider',50)->default('fonnte');
            $table->string('provider_message_id')->nullable();
            $table->string('reference',100)->nullable();
            $table->json('last_payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('wa_reminders');
    }
};