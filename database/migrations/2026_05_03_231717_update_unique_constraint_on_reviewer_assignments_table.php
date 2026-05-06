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
        Schema::table('reviewer_assignments', function (Blueprint $table) {
            // Add a temporary index for reviewer_id because it's used in a foreign key
            // and currently relies on the unique_assignment index.
            $table->index('reviewer_id', 'reviewer_assignments_reviewer_id_index');
            
            $table->dropUnique('unique_assignment');
            $table->unique(['reviewer_id', 'reviewee_id', 'assessment_date'], 'unique_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviewer_assignments', function (Blueprint $table) {
            $table->dropUnique('unique_assignment');
            $table->unique(['reviewer_id', 'reviewee_id'], 'unique_assignment');
            
            // Drop the temporary index
            $table->dropIndex('reviewer_assignments_reviewer_id_index');
        });
    }
};
