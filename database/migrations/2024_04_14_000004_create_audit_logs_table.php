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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // e.g., 'create', 'update', 'delete'
            $table->json('changes')->nullable(); // Store old and new values
            $table->string('model_type'); // The model that was changed
            $table->unsignedBigInteger('model_id'); // The ID of the model that was changed
            $table->timestamps();

            // Add indexes for performance optimization
            $table->index(['company_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
