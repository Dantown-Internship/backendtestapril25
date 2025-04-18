<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->decimal('amount', 10, 2);
            $table->string('category');
            $table->timestamps();

            // Adding indexes for better performance
            $table->index('company_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
}; 