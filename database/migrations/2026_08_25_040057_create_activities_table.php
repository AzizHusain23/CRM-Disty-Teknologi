<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('type', 30);

            $table->string('subject')->nullable();

            $table->text('description')->nullable();

            $table->dateTime('activity_at');

            $table->timestamps();

            $table->index('customer_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('activity_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};