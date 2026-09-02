<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('training_id')
                ->constrained('trainings')
                ->restrictOnDelete();

            $table->date('training_date')->nullable();

            $table->string('status', 30)
                ->default('registered');

            $table->decimal('amount', 15, 2)
                ->nullable();

            $table->string('registration_number', 100)
                ->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('customer_id');
            $table->index('training_id');
            $table->index('training_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};