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
        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('audit_logs', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('audit_logs', 'company_id')) {
                $table->foreignId('company_id')->after('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('audit_logs', 'action')) {
                $table->string('action')->after('company_id');
            }
            if (!Schema::hasColumn('audit_logs', 'changes')) {
                $table->json('changes')->after('action');
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            //
        });
    }

    
};
