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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('action'); // e.g., 'create', 'update', 'delete'
            $table->string('model_type'); // e.g., 'App\Models\Expense'
            $table->unsignedBigInteger('model_id');
            $table->json('changes')->nullable(); // Stores old/new values
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'model_type', 'model_id']);
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
