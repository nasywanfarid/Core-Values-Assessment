<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Bersihkan data lama
        DB::table('interaction_matrices')->truncate();

        // 2. Karena index unik lama dibutuhkan oleh foreign key, kita biarkan saja.
        // Kita hanya perlu memastikan Controller selalu mengirim branch_id = null.
        // Database MYSQL mengizinkan banyak baris dengan NULL pada kolom unik.
        // Contoh: (NULL, 1, 2) dan (NULL, 1, 2) dianggap duplikat jika aturan uniknya ketat.
        // Namun, jika kita ingin mematikan validasi unik lama, kita harus drop foreign key dulu.
        
        Schema::table('interaction_matrices', function (Blueprint $table) {
            // Drop FK dulu agar bisa drop index
            try {
                $table->dropForeign(['branch_id']);
                $table->dropUnique('unique_matrix');
            } catch (\Exception $e) {}

            // Buat aturan unik baru yang GLOBAL
            $table->unique(['target_division_id', 'reviewer_division_id'], 'global_interaction_unique');
            
            // Pasang kembali FK branch_id (sebagai nullable)
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interaction_matrices', function (Blueprint $table) {
            $table->dropUnique('global_interaction_unique');
        });
    }
};
