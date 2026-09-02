<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('original_filename');

            $table->string('stored_path');

            $table->unsignedInteger('total_rows')
                ->default(0);

            $table->unsignedInteger('ready_rows')
                ->default(0);

            $table->unsignedInteger('duplicate_rows')
                ->default(0);

            $table->unsignedInteger('invalid_rows')
                ->default(0);

            $table->string('status', 30)
                ->default('uploaded');

            $table->text('error_message')->nullable();

            $table->dateTime('completed_at')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};