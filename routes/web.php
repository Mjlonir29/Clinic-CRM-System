<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PatientAuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RosterController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubaccountController;
use Illuminate\Support\Facades\Route;

// Root Route: Displays Login page first for guests, redirects to Dashboard for authenticated staff
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Guest Auth & Patient Booking Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Forgot Password, OTP & Reset Password Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('password.otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('password.verify_otp');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Customer / Patient Booking & Account Portal (JWT Authenticated)
Route::get('/patient/book', [PatientPortalController::class, 'showBooking'])->name('patient.book');
Route::get('/patient/booking', [PatientPortalController::class, 'showBooking'])->name('patient_portal.book');
Route::post('/patient/book', [PatientPortalController::class, 'storeBooking'])->name('patient.book.store');

Route::get('/patient/login', [PatientAuthController::class, 'showLogin'])->name('patient_portal.login');
Route::post('/patient/login', [PatientAuthController::class, 'login'])->name('patient_portal.authenticate');
Route::get('/patient/dashboard', [PatientAuthController::class, 'dashboard'])->name('patient_portal.dashboard');
Route::post('/patient/notifications/{id}/read', [PatientAuthController::class, 'markNotificationRead'])->name('patient_portal.notifications.read');
Route::post('/patient/logout', [PatientAuthController::class, 'logout'])->name('patient_portal.logout');

// Authenticated CRM Routes (Strict Protection)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::middleware('permission:dashboard.view')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Global Search
    Route::get('/search', [GlobalSearchController::class, 'search'])->name('global.search');

    // Doctors Module
    Route::prefix('doctors')->name('doctors.')->group(function () {
        Route::get('/', [DoctorController::class, 'index'])->name('index');
        Route::get('/create', [DoctorController::class, 'create'])->name('create');
        Route::post('/', [DoctorController::class, 'store'])->name('store');
        Route::get('/{doctor}', [DoctorController::class, 'show'])->name('show');
    });

    // Patients Module
    Route::middleware('permission:patients.view')->prefix('patients')->name('patients.')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->name('index');
        Route::get('/create', [PatientController::class, 'create'])->name('create');
        Route::post('/', [PatientController::class, 'store'])->name('store');
        Route::get('/{patient}', [PatientController::class, 'show'])->name('show');
        Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('edit');
        Route::put('/{patient}', [PatientController::class, 'update'])->name('update');
        Route::post('/{patient}/documents', [PatientController::class, 'uploadDocument'])->name('upload-document');
        Route::delete('/{patient}', [PatientController::class, 'destroy'])->name('destroy');
    });

    // Appointments Module
    Route::middleware('permission:appointments.view')->prefix('appointments')->name('appointments.')->group(function () {
        Route::get('/', [AppointmentController::class, 'index'])->name('index');
        Route::get('/create', [AppointmentController::class, 'create'])->name('create');
        Route::get('/calendar', [AppointmentController::class, 'calendar'])->name('calendar');
        Route::post('/', [AppointmentController::class, 'store'])->name('store');
        Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
        Route::put('/{appointment}', [AppointmentController::class, 'update'])->name('update');
        Route::patch('/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{appointment}', [AppointmentController::class, 'destroy'])->name('destroy');
    });

    // Consultations Workflow
    Route::middleware('permission:consultations.view')->prefix('consultations')->name('consultations.')->group(function () {
        Route::get('/create', [ConsultationController::class, 'create'])->name('create');
        Route::post('/', [ConsultationController::class, 'store'])->name('store');
    });

    // Prescriptions Module
    Route::middleware('permission:prescriptions.view')->prefix('prescriptions')->name('prescriptions.')->group(function () {
        Route::get('/', [PrescriptionController::class, 'index'])->name('index');
        Route::get('/create', [PrescriptionController::class, 'create'])->name('create');
        Route::post('/', [PrescriptionController::class, 'store'])->name('store');
        Route::get('/{prescription}', [PrescriptionController::class, 'show'])->name('show');
        Route::get('/{prescription}/print', [PrescriptionController::class, 'print'])->name('print');
        Route::post('/{prescription}/send', [PrescriptionController::class, 'sendToPatient'])->name('send');
        Route::delete('/{prescription}', [PrescriptionController::class, 'destroy'])->name('destroy');
    });

    // Invoices & Billing Module
    Route::middleware('permission:invoices.view')->prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::post('/{invoice}/payments', [InvoiceController::class, 'recordPayment'])->name('payments.store');
        Route::get('/{invoice}/print', [InvoiceController::class, 'print'])->name('print');
        Route::patch('/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('cancel');
        Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->name('destroy');
    });

    // Subaccounts / Staff Directory Module
    Route::middleware('permission:subaccounts.manage')->prefix('subaccounts')->name('subaccounts.')->group(function () {
        Route::get('/', [SubaccountController::class, 'index'])->name('index');
        Route::get('/create', [SubaccountController::class, 'create'])->name('create');
        Route::post('/', [SubaccountController::class, 'store'])->name('store');
        Route::put('/{user}', [SubaccountController::class, 'update'])->name('update');
        Route::post('/{user}/documents', [SubaccountController::class, 'uploadDocument'])->name('upload-document');
        Route::delete('/{user}/documents/{document}', [SubaccountController::class, 'deleteDocument'])->name('delete-document');
        Route::delete('/{user}', [SubaccountController::class, 'destroy'])->name('destroy');
    });

    // Settings Module
    Route::middleware('permission:settings.edit')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'update'])->name('update');
        Route::post('/change-password', [SettingController::class, 'changePassword'])->name('change-password');
    });

    // Reports Module
    Route::middleware('permission:reports.view')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });

    // Graceful Redirects for Removed Routes
    Route::get('/roster', function () { return redirect()->route('dashboard'); });
    Route::get('/analytics', function () { return redirect()->route('dashboard'); });
    Route::get('/security', function () { return redirect()->route('dashboard'); });
    Route::get('/communication', function () { return redirect()->route('dashboard'); });
});
