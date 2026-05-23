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
            $table->unique(['branch_id', 'reviewer_division_id', 'target_division_id'], 'unique_interaction_rel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interaction_matrices', function (Blueprint $table) {
            $table->dropUnique('unique_interaction_rel');
        });
    }
};
