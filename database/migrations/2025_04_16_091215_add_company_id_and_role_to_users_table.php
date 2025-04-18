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
            $table->uuid()->after('id')->unique();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->onDelete('cascade')
                ->after('uuid');
            $table->string('role')->after('company_id')->index();

            // Since I'm using mysql, I dont need to add the index manually as the
            // foreign key will automatically create an index for the foreign key column.
            // $table->index('company_id');

            $table->index(['company_id', 'role'], 'company_role_index');
            $table->index(['company_id', 'uuid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex('company_role_index');
            $table->dropColumn([
                'company_id',
                'role',
                'uuid',
            ]);
        });
    }
};
