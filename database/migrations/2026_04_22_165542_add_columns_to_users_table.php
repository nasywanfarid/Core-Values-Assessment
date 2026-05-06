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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->unique()->nullable()->after('id');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete()->after('name');
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete()->after('branch_id');
            $table->enum('role', ['admin', 'direktur', 'karyawan'])->default('karyawan')->after('division_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['division_id']);
            $table->dropColumn(['nip', 'branch_id', 'division_id', 'role']);
        });
    }
};
