<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah kolom hanya kalau BELUM ada
        if (! Schema::hasColumn('invoices', 'parent_user_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->unsignedBigInteger('parent_user_id')
                    ->nullable()
                    ->after('student_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus kolom hanya kalau MEMANG ada
        if (Schema::hasColumn('invoices', 'parent_user_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('parent_user_id');
            });
        }
    }
};
