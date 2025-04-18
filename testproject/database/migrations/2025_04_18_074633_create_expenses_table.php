<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->string('category');
            $table->timestamps();
    
            // Indexes
            $table->index('company_id');
            $table->index('user_id');
    
            // Foreign Keys
            $table->foreign('company_id')
                  ->references('id')->on('companies')
                  ->onDelete('cascade');
    
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
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
