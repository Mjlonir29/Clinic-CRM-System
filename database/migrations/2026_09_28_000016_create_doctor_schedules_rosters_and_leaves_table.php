<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->string('day_of_week'); // Monday, Tuesday, etc.
            $table->time('start_time')->default('09:00:00');
            $table->time('end_time')->default('17:00:00');
            $table->time('break_start')->default('13:00:00');
            $table->time('break_end')->default('14:00:00');
            $table->integer('slot_duration_minutes')->default(30);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('doctor_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->date('leave_date');
            $table->string('reason')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Approved');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('staff_rosters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('shift_date');
            $table->string('shift_type')->default('Morning'); // Morning, Evening, Night, Off
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_rosters');
        Schema::dropIfExists('doctor_leaves');
        Schema::dropIfExists('doctor_schedules');
    }
};
