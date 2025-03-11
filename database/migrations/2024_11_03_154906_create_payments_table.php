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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('clients')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('annex_id')->nullable()->constrained('contract_annexes')->onDelete('restrict')->onUpdate('cascade');
            $table->string('invoice_number')->unique();
            $table->enum('payment_method',["cash","bank transfer"])->nullable();
            $table->enum('payment_status',["unpaid","paid","overdue","pending"])->default("unpaid");
            $table->boolean('reconciled')->default(false);
            $table->float('payment_amount')->default(0.0);
            $table->date('due_date');
            $table->date('paid_at')->nullable();
            $table->string('payment_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
