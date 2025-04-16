<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->string('category');
            $table->timestamps();

            $table->index('company_id');
            $table->index('user_id');
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}; 