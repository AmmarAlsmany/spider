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
        Schema::create('branchs', function (Blueprint $table) {
            $table->id();
            $table->string('branch_name');
            $table->string('branch_manager_name');
            $table->string('branch_manager_phone');
            $table->string('branch_address');
            $table->string('branch_city');
            $table->foreignId('contracts_id')->constrained('contracts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('annex_id')->nullable()->constrained('contract_annexes')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branchs');
    }
};
