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
        Schema::table('interaction_matrices', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->cascadeOnDelete();
            $table->unsignedBigInteger('reviewer_division_id')->nullable()->change();
            $table->unsignedBigInteger('target_division_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interaction_matrices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
            $table->unsignedBigInteger('reviewer_division_id')->nullable(false)->change();
            $table->unsignedBigInteger('target_division_id')->nullable(false)->change();
        });
    }
};
