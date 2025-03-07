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
        Schema::create('visit_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visit_schedules')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('restrict')->onUpdate('cascade');
            $table->date('visit_date');
            $table->time('visit_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_change_requests');
    }
};
