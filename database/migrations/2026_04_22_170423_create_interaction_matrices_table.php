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
        Schema::create('interaction_matrices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reviewer_division_id')->constrained('divisions')->cascadeOnDelete();
            $table->foreignId('target_division_id')->constrained('divisions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['reviewer_division_id', 'target_division_id'], 'unique_matrix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interaction_matrices');
    }
};
