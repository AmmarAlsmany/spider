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
        Schema::create('visit_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visit_schedules')->onDelete('cascade')->onUpdate('cascade');
            $table->time('time_in');
            $table->time('time_out');
            $table->enum('visit_type', ['regular', 'complementary', 'emergency', 'free', 'other']);
            $table->json('target_insects');
            $table->json('pesticides_used');
            $table->text('recommendations');
            $table->text('customer_notes')->nullable();
            $table->text('customer_signature'); // Will store base64 encoded signature
            $table->string('phone_signature');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_reports');
    }
};
