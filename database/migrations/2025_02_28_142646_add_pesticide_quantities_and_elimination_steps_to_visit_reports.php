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
        Schema::table('visit_reports', function (Blueprint $table) {
            $table->json('pesticide_quantities')->after('pesticides_used');
            $table->text('elimination_steps')->after('pesticide_quantities')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_reports', function (Blueprint $table) {
            $table->dropColumn(['pesticide_quantities', 'elimination_steps']);
        });
    }
};
