<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('registrations', function (Blueprint $table) {
            $table->foreignId('training_schedule_id')->nullable()->after('training_id')
                ->constrained('training_schedules')->nullOnDelete();
            $table->index('training_schedule_id');
        });
    }
    public function down(): void {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropForeign(['training_schedule_id']);
            $table->dropIndex(['training_schedule_id']);
            $table->dropColumn('training_schedule_id');
        });
    }
};
