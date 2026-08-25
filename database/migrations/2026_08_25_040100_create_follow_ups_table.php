<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->dateTime('follow_up_at');

            $table->string('priority', 20)
                ->default('normal');

            $table->string('status', 30)
                ->default('pending');

            $table->dateTime('completed_at')->nullable();

            $table->timestamps();

            $table->index('customer_id');
            $table->index('assigned_to');
            $table->index('follow_up_at');
            $table->index('status');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};