<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('action'); // 'update' or 'delete'
            $table->json('changes')->nullable(); // Store old and new values
            $table->string('model_type'); // e.g., 'App\Models\Expense'
            $table->unsignedBigInteger('model_id'); // The ID of the model being modified
            $table->timestamps();

            // Add indexes for performance
            $table->index(['company_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}; 