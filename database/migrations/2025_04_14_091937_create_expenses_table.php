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
            $table->foreignIdFor(\App\Models\Company::class)->index('company_id_index')->cascadeOnDelete();
            $table->foreignIdFor(\App\Models\User::class)->index('user_id_index')->cascadeOnDelete();
            $table->string('title');
            $table->decimal('amount', 30, 2);
            $table->string('category');
            $table->timestamps();
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
