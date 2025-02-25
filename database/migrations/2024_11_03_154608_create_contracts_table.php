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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_number')->unique();
            $table->foreignId('customer_id')->constrained('clients')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('sales_id')->constrained("users")->onDelete('cascade')->onUpdate('cascade');
            $table->enum('Property_type',["Residential","Commercial","Industrial","Government","equipment"])->nullable();
            $table->foreignId('contract_type')->constrained('contracts_types')->onDelete('cascade')->onUpdate('cascade');
            $table->string('contract_price');
            $table->enum('contract_status',["approved","pending","Not approved","completed","stopped","canceled"])->default("pending");
            $table->string('rejection_reason')->nullable();
            $table->string('contract_description');
            $table->enum('Payment_type',["postpaid","prepaid"])->default("postpaid");
            $table->integer('number_Payments')->nullable();
            $table->enum('is_multi_branch',["yes","no"])->default("no");
            $table->enum('is_finish',["1","0"])->default("0");
            $table->date('contract_start_date');
            $table->date('contract_end_date');
            $table->integer('warranty')->nullable();
            $table->integer('number_of_visits')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
