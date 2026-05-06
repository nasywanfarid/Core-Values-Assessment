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
            if (!Schema::hasColumn('reviewer_assignments', 'assessment_date')) {
                $table->date('assessment_date')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviewer_assignments', function (Blueprint $table) {
            $table->dropColumn('assessment_date');
        });
    }
};
