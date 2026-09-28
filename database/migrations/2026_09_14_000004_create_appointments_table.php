<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_number')->unique(); // e.g. APT-2026-0001
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('appointment_date');
            $table->string('appointment_time'); // e.g., 09:30 AM
            $table->string('appointment_type')->default('Consultation'); // General Checkup, Follow-up, Consultation, Emergency, Special Care
            $table->text('reason_for_visit')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('Pending'); // Pending, Confirmed, Checked In, In Consultation, Completed, Cancelled, No Show
            $table->string('payment_status')->default('Unpaid'); // Unpaid, Paid, Partially Paid
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
