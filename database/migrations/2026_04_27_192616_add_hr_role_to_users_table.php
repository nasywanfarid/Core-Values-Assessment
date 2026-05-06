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
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'direktur', 'karyawan', 'hr') DEFAULT 'karyawan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse back to previous enum options without hr
        // Note: Make sure there are no users with 'hr' role before rolling back, or it will fail
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'direktur', 'karyawan') DEFAULT 'karyawan'");
    }
};
