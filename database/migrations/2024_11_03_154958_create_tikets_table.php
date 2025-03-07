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
        Schema::create('tikets', function (Blueprint $table) {
            $table->id();
            $table->string('tiket_number');
            $table->string('tiket_title');
            $table->string('tiket_description');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->string('ticket_priority')->nullable();
            $table->foreignId('who_solved_it')->nullable()->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('customer_id')->constrained('clients')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tikets');
    }
};
