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
        Schema::table('users', function (Blueprint $table) {

            $table->unsignedBigInteger('company_id');
            $table->enum('role', ['Admin', 'Manager', 'Employee'])->default('Employee');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("users");
    }
};
