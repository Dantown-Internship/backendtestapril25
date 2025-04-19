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
        Schema::table('expenses', function (Blueprint $table) {
            $table->index('company_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down()
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
            $table->dropIndex(['user_id']);
        });
    }

};
