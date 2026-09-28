<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('qualifications')->nullable()->after('phone');
            $table->string('specialization')->nullable()->after('qualifications');
            $table->string('registration_number')->nullable()->after('specialization');
            $table->string('experience_years')->nullable()->after('registration_number');
            $table->decimal('consultation_fee', 10, 2)->default(500.00)->after('experience_years');
            $table->string('cabin_number')->nullable()->after('consultation_fee');
            $table->string('working_hours')->nullable()->after('cabin_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'qualifications',
                'specialization',
                'registration_number',
                'experience_years',
                'consultation_fee',
                'cabin_number',
                'working_hours',
            ]);
        });
    }
};
