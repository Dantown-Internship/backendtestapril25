<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// class CreateAuditLogsTable extends Migration
// {
//     public function up()
//     {
//         Schema::create('audit_logs', function (Blueprint $table) {
//             $table->id();
//             $table->foreignId('user_id')->constrained()->onDelete('cascade');
//             $table->foreignId('company_id')->constrained()->onDelete('cascade');
//             $table->string('action');
//             $table->json('changes');
//             $table->timestamps();
//         });
//     }

//     public function down()
//     {
//         Schema::dropIfExists('audit_logs');
//     }
// }

// database/migrations/XXXX_alter_audit_logs_add_columns.php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAuditLogsAddColumns extends Migration
{
    public function up()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('model_type')->after('action');
            $table->unsignedBigInteger('model_id')->after('model_type');
            $table->timestamp('performed_at')->after('changes');
        });
    }

    public function down()
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['model_type', 'model_id', 'performed_at']);
        });
    }
}


