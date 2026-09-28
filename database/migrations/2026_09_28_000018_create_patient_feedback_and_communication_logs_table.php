<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->integer('rating')->default(5); // 1-5 stars
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        Schema::create('communication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
            $table->string('recipient_phone');
            $table->string('channel')->default('WhatsApp'); // WhatsApp, SMS
            $table->string('template_type'); // Appointment_Reminder, Invoice_Share, Prescription_Share, Followup_Alert
            $table->text('message_content');
            $table->string('status')->default('Sent'); // Sent, Delivered, Failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_logs');
        Schema::dropIfExists('patient_feedbacks');
    }
};
