<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_rows', function (Blueprint $table) {
            $table->id();

            $table->foreignId('import_batch_id')
                ->constrained('import_batches')
                ->cascadeOnDelete();

            $table->string('sheet_name', 100);

            $table->unsignedInteger('row_number');

            $table->json('raw_data')->nullable();

            $table->string('name')->nullable();

            $table->string('email')->nullable();

            $table->string('phone', 50)->nullable();

            $table->string('document_number', 100)->nullable();

            $table->string('institution_name')->nullable();

            $table->string('normalized_name')->nullable();

            $table->string('normalized_email')->nullable();

            $table->string('normalized_document_number')->nullable();

            $table->string('dedupe_key', 255)->nullable();

            $table->string('status', 30)
                ->default('ready');

            $table->string('duplicate_reason')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index('import_batch_id');
            $table->index('sheet_name');
            $table->index('status');
            $table->index('dedupe_key');
            $table->index('normalized_email');
            $table->index('normalized_document_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_rows');
    }
};