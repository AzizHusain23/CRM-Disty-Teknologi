<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('training_category_id')
                ->nullable()
                ->constrained('training_categories')
                ->nullOnDelete();

            $table->string('name');

            $table->text('description')->nullable();

            $table->decimal('price', 15, 2)
                ->nullable();

            $table->unsignedInteger('duration_hours')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index('name');
            $table->index('training_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};