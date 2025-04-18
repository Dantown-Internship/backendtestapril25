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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('central_user_id'); // Links to central users table
            $table->string('name');
            $table->string('password')->nullable();
            $table->string('email')->unique();
            $table->enum('role', ['Admin', 'Manager', 'Employee'])->default('Employee');
            $table->timestamps();

            $table->foreign('central_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('central_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
