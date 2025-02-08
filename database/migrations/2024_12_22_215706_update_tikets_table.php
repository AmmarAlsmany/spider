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
        Schema::table('tikets', function (Blueprint $table) {
            // Update status enum to include more statuses
            $table->dropColumn('status');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            
            // Update priority to be an enum
            $table->dropColumn('ticket_priority');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            
            // Add created_by field
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // Rename who_solved_it to solver_id for consistency
            $table->renameColumn('who_solved_it', 'solver_id');
            
            // Add solved_at timestamp
            $table->timestamp('solved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tikets', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->enum('status', ['open', 'closed'])->default('open');
            
            $table->dropColumn('priority');
            $table->string('ticket_priority')->nullable();
            
            $table->dropColumn('created_by');
            
            $table->renameColumn('solver_id', 'who_solved_it');
            
            $table->dropColumn('solved_at');
        });
    }
};
