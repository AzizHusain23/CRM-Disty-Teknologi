<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Rename companies -> institutions
        |--------------------------------------------------------------------------
        */

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });

        Schema::rename('companies', 'institutions');

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('company_id', 'institution_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('institution_id')
                ->references('id')
                ->on('institutions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Rollback institutions -> companies
        |--------------------------------------------------------------------------
        */

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('institution_id', 'company_id');
        });

        Schema::rename('institutions', 'companies');

        Schema::table('customers', function (Blueprint $table) {
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->nullOnDelete();
        });
    }
};