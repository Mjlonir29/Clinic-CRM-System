<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_number')->unique(); // e.g. RX-2026-0001
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->date('prescription_date');
            $table->text('diagnosis')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->text('advice')->nullable();
            $table->text('tests_recommended')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->string('medicine_name');
            $table->string('dosage')->nullable(); // e.g. 500 mg, 1 tablet
            $table->string('frequency')->nullable(); // e.g. 3 times daily
            $table->string('duration')->nullable(); // e.g. 5 days
            $table->string('route')->nullable(); // e.g. Oral, Topical, IV
            $table->string('timing')->nullable(); // e.g. After food, Before food
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
    }
};
