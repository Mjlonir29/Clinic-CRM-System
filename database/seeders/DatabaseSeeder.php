<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\ClinicNotification;
use App\Models\Consultation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions Setup
        $rolesData = [
            [
                'name' => 'Admin / Doctor',
                'slug' => 'admin',
                'permissions' => ['*'],
            ],
            [
                'name' => 'Clinic Manager',
                'slug' => 'clinic_manager',
                'permissions' => [
                    'dashboard.view',
                    'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel',
                    'patients.view', 'patients.create', 'patients.edit',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'payments.manage',
                ],
            ],
            [
                'name' => 'Receptionist',
                'slug' => 'receptionist',
                'permissions' => [
                    'dashboard.view',
                    'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel',
                    'patients.view', 'patients.create', 'patients.edit',
                ],
            ],
            [
                'name' => 'Nurse / Assistant',
                'slug' => 'nurse',
                'permissions' => [
                    'patients.view',
                    'appointments.view',
                    'consultations.view', 'consultations.edit',
                ],
            ],
            [
                'name' => 'Accountant',
                'slug' => 'accountant',
                'permissions' => [
                    'dashboard.view',
                    'invoices.view', 'invoices.create', 'invoices.edit', 'payments.manage',
                    'reports.view',
                ],
            ],
        ];

        $roles = [];
        foreach ($rolesData as $r) {
            $roles[$r['slug']] = Role::create($r);
        }

        // 2. Create Users for each Role
        $adminDoctor = User::create([
            'name' => 'Dr. Rajesh Sharma, MD',
            'username' => 'dr.rajesh',
            'email' => 'doctor@cliniccrm.com',
            'phone' => '+91 98765 43210',
            'password' => Hash::make('password'),
            'role_id' => $roles['admin']->id,
            'role_slug' => 'admin',
            'status' => 'active',
        ]);

        $manager = User::create([
            'name' => 'Priya Sharma',
            'username' => 'psharma',
            'email' => 'manager@cliniccrm.com',
            'phone' => '+91 98765 12345',
            'password' => Hash::make('password'),
            'role_id' => $roles['clinic_manager']->id,
            'role_slug' => 'clinic_manager',
            'status' => 'active',
        ]);

        $receptionist = User::create([
            'name' => 'Sunita Verma',
            'username' => 'sverma',
            'email' => 'reception@cliniccrm.com',
            'phone' => '+91 98765 23456',
            'password' => Hash::make('password'),
            'role_id' => $roles['receptionist']->id,
            'role_slug' => 'receptionist',
            'status' => 'active',
        ]);

        $nurse = User::create([
            'name' => 'Amit Kumar, RN',
            'username' => 'akumar',
            'email' => 'nurse@cliniccrm.com',
            'phone' => '+91 98765 34567',
            'password' => Hash::make('password'),
            'role_id' => $roles['nurse']->id,
            'role_slug' => 'nurse',
            'status' => 'active',
        ]);

        $accountant = User::create([
            'name' => 'Jessica Taylor',
            'username' => 'jtaylor',
            'email' => 'billing@cliniccrm.com',
            'phone' => '+91 98765 45678',
            'password' => Hash::make('password'),
            'role_id' => $roles['accountant']->id,
            'role_slug' => 'accountant',
            'status' => 'active',
        ]);

        // 3. Settings Configuration
        $settings = [
            // Doctor Profile
            'doctor_name' => 'Dr. Rajesh Sharma, MD',
            'doctor_qualification' => 'MBBS, MD (General Medicine), DM (Cardiology)',
            'doctor_specialization' => 'Cardiology & General Medicine',
            'doctor_registration_number' => 'MCI-2024-98765',
            'doctor_phone' => '+91 98765 43210',
            'doctor_email' => 'doctor@cliniccrm.com',

            // Clinic Settings
            'clinic_name' => 'Apex Heart & Multispeciality Clinic',
            'clinic_address' => '742 Park Street, Connaught Place, New Delhi, Delhi 110001',
            'clinic_phone' => '+91 11 2345 6789',
            'clinic_email' => 'contact@apexclinic.in',
            'clinic_website' => 'https://apexclinic.in',
            'working_hours' => 'Mon-Sat: 9:00 AM - 8:00 PM, Sun: 10:00 AM - 2:00 PM',
            'consultation_fee' => '500.00',
            'currency' => '₹',
            'timezone' => 'Asia/Kolkata',

            // Appointment Settings
            'appointment_duration' => '30', // minutes
            'working_days' => json_encode(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
            'break_time' => '1:00 PM - 2:00 PM',
            'max_appointments_per_day' => '24',
            'cancellation_rules' => 'Appointments must be cancelled at least 2 hours prior to scheduled time.',

            // Prescription Settings
            'prescription_header' => 'Apex Heart & Multispeciality Clinic - Digital Health Record',
            'prescription_footer' => 'Wish you a speedy recovery! For emergencies, please call +91 98765 43210.',
            'default_instructions' => 'Take all medicines after meals with plenty of warm water unless specified otherwise.',

            // Invoice Settings
            'invoice_prefix' => 'INV-',
            'tax_percentage' => '5.0',
            'payment_terms' => 'Payment due upon receipt of invoice.',
            'invoice_footer' => 'Thank you for choosing Apex Clinic. Keep this receipt for your records.',

            // Notification Settings
            'notify_new_appointment' => '1',
            'notify_appointment_reminder' => '1',
            'notify_payment_received' => '1',
        ];

        foreach ($settings as $key => $val) {
            Setting::set($key, $val, 'system');
        }

        // 4. Patients Creation
        $patientsData = [
            [
                'patient_id' => 'PAT-2026-0001',
                'first_name' => 'Rahul',
                'last_name' => 'Sharma',
                'dob' => '1985-04-12',
                'age' => 41,
                'gender' => 'Male',
                'blood_group' => 'O+',
                'phone' => '+91 98200 12345',
                'email' => 'rahul.sharma@example.com',
                'address' => '123 Connaught Place',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'pin_code' => '110001',
                'allergies' => 'Penicillin, Dust Mites',
                'existing_conditions' => 'Hypertension, Mild Asthma',
                'current_medications' => 'Lisinopril 10mg daily, Albuterol inhaler PRN',
                'previous_history' => 'Appendectomy (2018)',
                'family_history' => 'Father had Type 2 Diabetes, Mother had CAD',
                'emergency_contact_name' => 'Neha Sharma (Wife)',
                'emergency_contact_phone' => '+91 98200 99999',
            ],
            [
                'patient_id' => 'PAT-2026-0002',
                'first_name' => 'Ananya',
                'last_name' => 'Verma',
                'dob' => '1992-08-25',
                'age' => 34,
                'gender' => 'Female',
                'blood_group' => 'A+',
                'phone' => '+91 99887 76655',
                'email' => 'ananya.verma@example.com',
                'address' => '456 Bandra West, Apt 4B',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'pin_code' => '400050',
                'allergies' => 'Sulfa Drugs',
                'existing_conditions' => 'Migraine',
                'current_medications' => 'Sumatriptan 50mg PRN',
                'previous_history' => 'None',
                'family_history' => 'Mother has Thyroid Disorder',
                'emergency_contact_name' => 'Karan Verma (Brother)',
                'emergency_contact_phone' => '+91 99887 11111',
            ],
            [
                'patient_id' => 'PAT-2026-0003',
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'dob' => '1968-11-05',
                'age' => 57,
                'gender' => 'Male',
                'blood_group' => 'B+',
                'phone' => '+91 98110 54321',
                'email' => 'rajesh.k@example.com',
                'address' => '789 MG Road',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'pin_code' => '560001',
                'allergies' => 'None known',
                'existing_conditions' => 'Type 2 Diabetes, Hyperlipidemia',
                'current_medications' => 'Metformin 850mg BD, Atorvastatin 20mg HS',
                'previous_history' => 'Cholecystectomy (2021)',
                'family_history' => 'Diabetes mellitus type 2 across paternal line',
                'emergency_contact_name' => 'Sunita Kumar (Spouse)',
                'emergency_contact_phone' => '+91 98110 88888',
            ],
            [
                'patient_id' => 'PAT-2026-0004',
                'first_name' => 'Pooja',
                'last_name' => 'Patel',
                'dob' => '1998-02-14',
                'age' => 28,
                'gender' => 'Female',
                'blood_group' => 'AB-',
                'phone' => '+91 97123 45678',
                'email' => 'pooja.patel@example.com',
                'address' => '321 CG Road',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pin_code' => '380009',
                'allergies' => 'Peanuts',
                'existing_conditions' => 'Seasonal Rhinitis',
                'current_medications' => 'Cetirizine 10mg daily',
                'previous_history' => 'Fractured Right Wrist (2015)',
                'family_history' => 'None',
                'emergency_contact_name' => 'Suresh Patel (Father)',
                'emergency_contact_phone' => '+91 97123 00000',
            ],
        ];

        $patients = [];
        foreach ($patientsData as $p) {
            $patients[] = Patient::create($p);
        }

        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');

        // 5. Create Appointments across all statuses
        $appointments = [
            [
                'appointment_number' => 'APT-2026-0001',
                'patient_id' => $patients[0]->id,
                'doctor_id' => $adminDoctor->id,
                'appointment_date' => $today,
                'appointment_time' => '09:00 AM',
                'appointment_type' => 'Consultation',
                'reason_for_visit' => 'Routine Cardiology follow-up & Blood Pressure check',
                'notes' => 'Patient complained of occasional chest tightness during morning walk.',
                'status' => 'Completed',
                'payment_status' => 'Paid',
            ],
            [
                'appointment_number' => 'APT-2026-0002',
                'patient_id' => $patients[1]->id,
                'doctor_id' => $adminDoctor->id,
                'appointment_date' => $today,
                'appointment_time' => '10:30 AM',
                'appointment_type' => 'General Checkup',
                'reason_for_visit' => 'Severe throbbing migraine headache for 2 days',
                'notes' => 'Checked in at front desk by receptionist.',
                'status' => 'Checked In',
                'payment_status' => 'Unpaid',
            ],
            [
                'appointment_number' => 'APT-2026-0003',
                'patient_id' => $patients[2]->id,
                'doctor_id' => $adminDoctor->id,
                'appointment_date' => $today,
                'appointment_time' => '02:15 PM',
                'appointment_type' => 'Follow-up',
                'reason_for_visit' => 'HbA1c quarterly review & medication dosage calibration',
                'notes' => 'Patient requested afternoon appointment slot.',
                'status' => 'Confirmed',
                'payment_status' => 'Unpaid',
            ],
            [
                'appointment_number' => 'APT-2026-0004',
                'patient_id' => $patients[3]->id,
                'doctor_id' => $adminDoctor->id,
                'appointment_date' => $tomorrow,
                'appointment_time' => '11:00 AM',
                'appointment_type' => 'General Checkup',
                'reason_for_visit' => 'Annual preventive physical exam & blood panel',
                'notes' => 'Fasting required for blood tests.',
                'status' => 'Confirmed',
                'payment_status' => 'Unpaid',
            ],
            [
                'appointment_number' => 'APT-2026-0005',
                'patient_id' => $patients[2]->id,
                'doctor_id' => $adminDoctor->id,
                'appointment_date' => $yesterday,
                'appointment_time' => '04:00 PM',
                'appointment_type' => 'Special Care',
                'reason_for_visit' => 'Diabetic neuropathy evaluation',
                'notes' => 'Numbness in lower extremities.',
                'status' => 'Completed',
                'payment_status' => 'Paid',
            ],
        ];

        $appEntities = [];
        foreach ($appointments as $apt) {
            $appEntities[] = Appointment::create($apt);
        }

        // 6. Consultations, Prescriptions, Invoices & Payments
        $consult1 = Consultation::create([
            'appointment_id' => $appEntities[0]->id,
            'patient_id' => $patients[0]->id,
            'doctor_id' => $adminDoctor->id,
            'consultation_date' => $today,
            'symptoms' => 'Mild dyspnea on exertion, occasional morning fatigue, blood pressure 138/88 mmHg.',
            'diagnosis' => 'Essential Stage 1 Hypertension with mild cardiovascular strain.',
            'medical_notes' => 'Auscultation revealed normal S1/S2 without murmurs. EKG showed normal sinus rhythm. Advised sodium restriction < 2g/day and daily brisk walk for 30 minutes.',
            'treatment_plan' => 'Up-titrate Lisinopril to 20mg once daily. Recheck BP in 3 weeks with home monitoring log.',
            'vital_signs' => [
                'bp' => '138/88',
                'pulse' => '74 bpm',
                'temp' => '98.6 °F',
                'weight' => '82 kg',
                'height' => '178 cm',
                'spo2' => '99%',
            ],
        ]);

        $rx1 = Prescription::create([
            'prescription_number' => 'RX-2026-0001',
            'patient_id' => $patients[0]->id,
            'doctor_id' => $adminDoctor->id,
            'consultation_id' => $consult1->id,
            'appointment_id' => $appEntities[0]->id,
            'prescription_date' => $today,
            'diagnosis' => 'Essential Stage 1 Hypertension',
            'symptoms' => 'Mild dyspnea on exertion, BP 138/88 mmHg',
            'clinical_notes' => 'Patient instructed to record daily BP morning and evening.',
            'advice' => 'Low salt diet, regular physical exercise, refrain from caffeine after 4 PM.',
            'tests_recommended' => 'Fasting Lipid Profile, Serum Creatinine, Serum Electrolytes',
            'follow_up_date' => now()->addDays(21)->format('Y-m-d'),
        ]);

        PrescriptionItem::create([
            'prescription_id' => $rx1->id,
            'medicine_name' => 'Lisinopril',
            'dosage' => '20 mg',
            'frequency' => '1 tablet once daily',
            'duration' => '30 days',
            'route' => 'Oral',
            'timing' => 'Morning after breakfast',
            'instructions' => 'Swallow whole with plenty of water. Do not crush.',
        ]);

        PrescriptionItem::create([
            'prescription_id' => $rx1->id,
            'medicine_name' => 'Amlodipine',
            'dosage' => '5 mg',
            'frequency' => '1 tablet daily',
            'duration' => '30 days',
            'route' => 'Oral',
            'timing' => 'Bedtime',
            'instructions' => 'May cause mild ankle swelling; report if persistent.',
        ]);

        $inv1 = Invoice::create([
            'invoice_number' => 'INV-2026-0001',
            'patient_id' => $patients[0]->id,
            'appointment_id' => $appEntities[0]->id,
            'doctor_id' => $adminDoctor->id,
            'invoice_date' => $today,
            'due_date' => $today,
            'subtotal' => 800.00,
            'discount' => 50.00,
            'tax' => 37.50,
            'total_amount' => 787.50,
            'paid_amount' => 787.50,
            'due_amount' => 0.00,
            'status' => 'Paid',
            'notes' => 'Full payment received via UPI.',
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv1->id,
            'service_name' => 'Cardiology Consultation Fee',
            'description' => 'Comprehensive cardiac evaluation & BP assessment',
            'quantity' => 1,
            'rate' => 500.00,
            'discount' => 50.00,
            'tax' => 22.50,
            'total' => 472.50,
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv1->id,
            'service_name' => '12-Lead Electrocardiogram (EKG)',
            'description' => 'In-clinic resting 12-lead EKG recording and interpretation',
            'quantity' => 1,
            'rate' => 300.00,
            'discount' => 0.00,
            'tax' => 15.00,
            'total' => 315.00,
        ]);

        Payment::create([
            'payment_number' => 'PAY-2026-0001',
            'invoice_id' => $inv1->id,
            'patient_id' => $patients[0]->id,
            'amount' => 787.50,
            'payment_date' => $today,
            'payment_method' => 'UPI',
            'transaction_reference' => 'UPI/9820012345@okaxis',
            'notes' => 'Paid via Google Pay / UPI',
        ]);

        $consult2 = Consultation::create([
            'appointment_id' => $appEntities[4]->id,
            'patient_id' => $patients[2]->id,
            'doctor_id' => $adminDoctor->id,
            'consultation_date' => $yesterday,
            'symptoms' => 'Bilateral tingling and burning sensation in toes, HbA1c 7.8%.',
            'diagnosis' => 'Early Diabetic Peripheral Neuropathy',
            'medical_notes' => 'Monofilament test positive for reduced sensation in plantar aspect of bilateral 1st and 5th metatarsal heads.',
            'treatment_plan' => 'Initiate Pregabalin 75mg at bedtime, adjust dietary carbohydrates.',
            'vital_signs' => [
                'bp' => '126/80',
                'pulse' => '78 bpm',
                'temp' => '98.4 °F',
                'weight' => '88 kg',
                'height' => '175 cm',
            ],
        ]);

        $rx2 = Prescription::create([
            'prescription_number' => 'RX-2026-0002',
            'patient_id' => $patients[2]->id,
            'doctor_id' => $adminDoctor->id,
            'consultation_id' => $consult2->id,
            'appointment_id' => $appEntities[4]->id,
            'prescription_date' => $yesterday,
            'diagnosis' => 'Diabetic Peripheral Neuropathy & Type 2 Diabetes',
            'symptoms' => 'Tingling and burning sensation in feet',
            'clinical_notes' => 'Daily foot care and protective footwear strongly recommended.',
            'advice' => 'Inspect feet daily for cuts or ulcers. Avoid walking barefoot.',
            'tests_recommended' => 'Repeat HbA1c in 3 months',
            'follow_up_date' => now()->addDays(30)->format('Y-m-d'),
        ]);

        PrescriptionItem::create([
            'prescription_id' => $rx2->id,
            'medicine_name' => 'Pregabalin',
            'dosage' => '75 mg',
            'frequency' => '1 capsule at bedtime',
            'duration' => '30 days',
            'route' => 'Oral',
            'timing' => 'At bedtime',
            'instructions' => 'May cause drowsiness.',
        ]);

        $inv2 = Invoice::create([
            'invoice_number' => 'INV-2026-0002',
            'patient_id' => $patients[2]->id,
            'appointment_id' => $appEntities[4]->id,
            'doctor_id' => $adminDoctor->id,
            'invoice_date' => $yesterday,
            'due_date' => $yesterday,
            'subtotal' => 500.00,
            'discount' => 0.00,
            'tax' => 25.00,
            'total_amount' => 525.00,
            'paid_amount' => 525.00,
            'due_amount' => 0.00,
            'status' => 'Paid',
            'notes' => 'Paid via PhonePe UPI.',
        ]);

        InvoiceItem::create([
            'invoice_id' => $inv2->id,
            'service_name' => 'Specialist Consultation',
            'description' => 'Diabetic neuropathy evaluation and sensory exam',
            'quantity' => 1,
            'rate' => 500.00,
            'discount' => 0.00,
            'tax' => 25.00,
            'total' => 525.00,
        ]);

        Payment::create([
            'payment_number' => 'PAY-2026-0002',
            'invoice_id' => $inv2->id,
            'patient_id' => $patients[2]->id,
            'amount' => 525.00,
            'payment_date' => $yesterday,
            'payment_method' => 'UPI',
            'transaction_reference' => 'UPI/9811054321@ybl',
            'notes' => 'Instant UPI payment',
        ]);

        // 7. Notifications
        ClinicNotification::createNotification(
            $adminDoctor->id,
            'appointment_created',
            'New Appointment Booked',
            "Patient Rahul Sharma booked an appointment for today at 09:00 AM.",
            "/appointments"
        );

        ClinicNotification::createNotification(
            $adminDoctor->id,
            'payment_received',
            'Payment Received',
            "Received ₹787.50 payment for Invoice #INV-2026-0001 from Rahul Sharma.",
            "/invoices"
        );
    }
}
