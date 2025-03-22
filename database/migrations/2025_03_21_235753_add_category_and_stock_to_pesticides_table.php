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
        Schema::table('pesticides', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
            $table->integer('current_stock')->default(0)->after('category');
            $table->integer('min_stock_threshold')->default(10)->after('current_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesticides', function (Blueprint $table) {
            $table->dropColumn(['category', 'current_stock', 'min_stock_threshold']);
        });
    }
};
