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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Foreign key to companies table
            $table->foreignId('user_id')->constrained()->onDelete('cascade');    // Foreign key to users table
            $table->string('title');
            $table->decimal('amount', 10, 2); // Use decimal for currency amounts
            $table->string('category');
            $table->timestamps();

            $table->index('company_id'); // Add index on company_id for performance
            $table->index('user_id');    // Add index on user_id for performance
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
