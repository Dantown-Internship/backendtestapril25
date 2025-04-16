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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamps();
        });


        DB::table('companies')->insert([
            [
                'name' => 'TechNova Ltd',
                'email' => 'info@technova.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'GreenFields Inc',
                'email' => 'contact@greenfields.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BrightEdge Corp',
                'email' => 'support@brightedge.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SoftQuest Solutions',
                'email' => 'hello@softquest.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'NextEra Group',
                'email' => 'admin@nextera.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
