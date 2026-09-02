<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('customer_code', 50)->unique();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->string('name');

            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();

            $table->string('document_number', 100)->nullable();

            $table->string('city')->nullable();
            $table->string('province')->nullable();

            $table->string('status', 30)
                ->default('active');

            $table->string('source', 50)
                ->default('manual');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
            $table->index('email');
            $table->index('document_number');
            $table->index('status');
            $table->index('source');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};