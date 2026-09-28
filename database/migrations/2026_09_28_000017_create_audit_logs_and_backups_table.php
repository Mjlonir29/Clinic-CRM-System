<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->default('System');
            $table->string('user_role')->default('Staff');
            $table->string('action'); // e.g. Created Invoice, Updated Prescription
            $table->string('module'); // Invoices, Patients, Prescriptions, Appointments, Settings
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('details')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('database_backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('file_path');
            $table->bigInteger('file_size_bytes')->default(0);
            $table->string('backup_type')->default('Manual'); // Manual, Auto
            $table->string('status')->default('Completed');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('database_backups');
        Schema::dropIfExists('audit_logs');
    }
};
