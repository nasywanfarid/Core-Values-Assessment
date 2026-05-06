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
        Schema::table('indicators', function (Blueprint $table) {
            $table->text('scale_1')->nullable()->after('description');
            $table->text('scale_2')->nullable()->after('scale_1');
            $table->text('scale_3')->nullable()->after('scale_2');
            $table->text('scale_4')->nullable()->after('scale_3');
            $table->text('scale_5')->nullable()->after('scale_4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indicators', function (Blueprint $table) {
            $table->dropColumn(['scale_1', 'scale_2', 'scale_3', 'scale_4', 'scale_5']);
        });
    }
};
