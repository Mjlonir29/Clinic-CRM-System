<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientPortalTest extends TestCase
{
    public function test_patient_can_view_booking_portal(): void
    {
        $response = $this->get('/patient/book');

        $response->assertStatus(200);
        $response->assertSee('Apex Care Hospital');
        $response->assertSee('Patient Portal');
    }

    public function test_patient_can_submit_online_appointment(): void
    {
        $doctor = User::first();

        $response = $this->post('/patient/book', [
            'first_name' => 'Sarah',
            'last_name' => 'Connor',
            'phone' => '+15559876543',
            'email' => 'sarah.connor@example.com',
            'gender' => 'Female',
            'age' => 32,
            'doctor_id' => $doctor ? $doctor->id : 1,
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:30 AM',
            'appointment_type' => 'General Consultation',
            'reason_for_visit' => 'Routine physical checkup and blood pressure check.',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('booking_success');
    }
}
