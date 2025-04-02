<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('visit_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branchs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade')->onUpdate('cascade');
            $table->string('visit_date');
            $table->string('visit_time');
            $table->enum('visit_type', ['regular', 'complementary', 'emergency', 'free', 'other']);
            $table->enum('status', ['scheduled', 'completed', 'cancelled','pending','approved','stopped','in_progress'])->default('scheduled');
            $table->integer('visit_number');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('visit_schedules');
    }
};
