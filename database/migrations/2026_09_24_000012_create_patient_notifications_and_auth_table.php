<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
        });

        Schema::create('patient_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('type')->default('general'); // lab_report_awaiting, lab_report_ready, appointment_reminder, prescription_ready
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_notifications');
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'password')) {
                $table->dropColumn('password');
            }
        });
    }
};
