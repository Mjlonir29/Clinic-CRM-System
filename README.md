# 🏥 Clinic CRM System

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-6.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

An enterprise-grade, full-featured **Healthcare & Clinic Customer Relationship Management (CRM) System** built with **Laravel 11** and **Tailwind CSS**. Designed to streamline clinic operations, empower doctors and medical staff, automate billing & e-prescriptions, and offer a dedicated self-service portal for patients.

---

## 👥 Development Team & Credits

| Member Name | Role / Contribution | GitHub / Profile |
| :--- | :--- | :--- |
| **SUMIT MALVIYA (Mjlonir29)** | Lead Developer & Architect | [@Mjlonir29](https://github.com/Mjlonir29) |
| **AGNIBHA DEY** | Team Member / Full-Stack Developer | Team Contributor |
| **RANJAN MANDAL** | Team Member / Full-Stack Developer | Team Contributor |

---

## ✨ Key Features & Core Modules

### 🏥 1. Patient Management & Self-Service Portal
* **Comprehensive Patient Records**: Full profile management, emergency contacts, medical history, and file/document uploads (PDF, images).
* **Self-Service Online Booking**: Public patient appointment booking interface (`/patient/book`).
* **JWT-Authenticated Patient Portal**: Dedicated patient login (`/patient/login`) to view upcoming appointments, notifications, and active e-prescriptions.

### 👨‍⚕️ 2. Doctor & Staff Directory (Subaccount Management)
* **Doctor Profiles & Schedules**: Doctor bio, medical registration numbers, consultation fees, working hours, and leave management.
* **Staff Subaccounts**: Create and manage subaccounts for Receptionists, Nurses, Clinic Managers, and Accountants.
* **Role-Based Access Control (RBAC)**: Fine-grained permission system protecting sensitive clinic operations.

### 📅 3. Interactive Appointment System & Calendar
* **Real-time Appointment Calendar**: Visual calendar overview of daily/weekly doctor schedules.
* **Status Workflow**: Manage appointment lifecycle with status transitions (`Pending`, `Confirmed`, `Completed`, `Cancelled`).

### 📝 4. Consultations & Digital E-Prescriptions
* **Clinical Consultation Notes**: Record patient chief complaints, clinical observations, diagnosis, and treatment plans.
* **Itemized E-Prescriptions**: Add medicines with dosage, frequency, duration, and special instructions.
* **Direct Printing & Dispatch**: One-click prescription print formatting and instant dispatch to patient portals.

### 💳 5. Billing, Invoicing & Financial Management
* **Itemized Invoices**: Automated billing for consultations, treatments, medicines, and services with custom tax & discounts.
* **Multi-Payment Tracking**: Record partial and full payments with real-time status tracking (`Paid`, `Unpaid`, `Partial`, `Cancelled`).
* **Printable Invoices**: Clean, printable PDF/HTML invoice formats for patients and clinic records.

### 📊 6. Analytics, Reporting & Global Search
* **Executive Dashboard**: Real-time KPI summaries (Total Patients, Today's Appointments, Revenue Overview, Active Doctors).
* **Global Search Bar**: Instant real-time search across patients, doctors, appointments, prescriptions, and invoices.
* **Custom Reports & Export**: Export detailed revenue and appointment reports for operational audit.

---

## 🛠️ Tech Stack & Architecture

* **Backend Framework**: [Laravel 11.x](https://laravel.com) (PHP 8.2+)
* **Database**: SQLite (default for quick setup) / MySQL / PostgreSQL
* **Frontend Engine**: Blade Templating + [Tailwind CSS 3](https://tailwindcss.com) + [Vite 6](https://vitejs.dev)
* **Authentication**: Session-based Guard for Staff + JWT Authentication Guard for Patient Portal
* **HTTP Client & Tooling**: Axios, PostCSS, Concurrently, PHPUnit

---

## 🚀 Getting Started

### Prerequisites
Ensure you have the following installed on your local machine:
* **PHP** `>= 8.2` (with PDO, OpenSSL, Mbstring, SQLite3 extensions)
* **Composer** `>= 2.0`
* **Node.js** `>= 18.0` & **npm**

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Mjlonir29/Clinic-CRM-System.git
   cd Clinic-CRM-System
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node Dependencies**
   ```bash
   npm install
   ```

4. **Set Up Environment File**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Prepare Database & Run Migrations**
   ```bash
   # Ensure database/database.sqlite exists
   touch database/database.sqlite

   # Run migrations and seed default roles, staff, and sample data
   php artisan migrate --seed
   ```

6. **Build Frontend Assets & Start Development Server**
   ```bash
   # Run both Laravel server and Vite asset bundler concurrently
   npm run start
   ```
   Or launch servers individually:
   ```bash
   php artisan serve   # Serves at http://127.0.0.1:8000
   npm run dev         # Serves Vite asset hot-reloading
   ```

---

## 🔑 Default Demo Login Credentials

After seeding the database (`php artisan db:seed`), you can log in using the following accounts:

| Role | Email | Password | Access Rights |
| :--- | :--- | :--- | :--- |
| **Admin / Doctor** | `doctor@cliniccrm.com` | `password` | Full Access (Super Admin) |
| **Clinic Manager** | `manager@cliniccrm.com` | `password` | Staff, Appointments, Patients & Financials |
| **Receptionist** | `reception@cliniccrm.com` | `password` | Appointments & Patient Registration |
| **Nurse / Assistant** | `nurse@cliniccrm.com` | `password` | Consultations & Patient Records |
| **Accountant** | `accountant@cliniccrm.com` | `password` | Invoices, Payments & Financial Reports |

---

## 📂 Project Directory Structure

```
Clinic-CRM-System/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Modules (Doctor, Patient, Appointment, Invoice, etc.)
│   │   └── Middleware/      # Role & Permission Middleware
│   ├── Models/              # Eloquent Models (User, Patient, Appointment, Prescription, etc.)
│   └── Services/            # JWT Service & Helper utilities
├── config/                  # App, Auth, Database configurations
├── database/
│   ├── migrations/          # DB Schemas & Tables
│   └── seeders/             # DatabaseSeeder with predefined roles & demo data
├── public/                  # Public assets & entry point (index.php)
├── resources/
│   ├── css/                 # Tailwind Styles
│   ├── js/                  # Frontend Scripts
│   └── views/               # Blade Templates & Modular Components
├── routes/
│   └── web.php              # App Routes & Middleware guards
├── storage/                 # Patient & Staff Uploaded Documents
└── composer.json / package.json
```

---

## 📜 License

This project is open-sourced under the [MIT License](LICENSE).

Developed with ❤️ by **SUMIT MALVIYA**, **AGNIBHA DEY**, and **RANJAN MANDAL**.
