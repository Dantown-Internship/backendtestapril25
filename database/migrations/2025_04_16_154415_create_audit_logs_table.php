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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who performed the action
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('set null'); // Company context of the action
            $table->string('action'); // e.g., 'created', 'updated', 'deleted'
            $table->json('changes')->nullable(); // JSON object storing the changes (old and new values)
            $table->timestamps();
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