<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->integer('amount');
            $table->string('category');
            $table->timestamps();

            // Add indexes for performance
            $table->index('company_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
