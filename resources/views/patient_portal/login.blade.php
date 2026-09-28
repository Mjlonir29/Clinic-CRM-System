<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Portal Login - Ekta Care Clinic</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['"Outfit"', 'sans-serif'],
                    },
                    colors: {
                        apex: {
                            50: '#f0fdf9',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#006654',
                            700: '#005243',
                            800: '#003e33',
                            900: '#002b23',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col justify-between">

    <!-- Top Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Ekta Care Logo" class="w-10 h-10 object-contain rounded-xl border border-slate-100 shadow-sm group-hover:scale-105 transition-transform">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-heading font-extrabold text-xl tracking-tight text-slate-900">Ekta Care</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Patient Portal</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">JWT Secure Patient Access</p>
                    </div>
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('patient.book') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-apex-700 bg-apex-50 hover:bg-apex-100 rounded-xl transition border border-apex-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Book Appointment
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="w-full max-w-md space-y-6">

            <!-- Title Header -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-apex-50 text-apex-600 border border-apex-100 shadow-inner mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold font-heading text-slate-900 tracking-tight">Patient Account Sign In</h1>
                <p class="text-sm text-slate-500 mt-1">Access your medical records, lab reports, prescriptions & notifications</p>
            </div>

            <!-- Notifications / Alerts -->
            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start space-x-3 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1 font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start space-x-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1 font-medium">{{ session('success') }}</div>
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-apex-50 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>

                <form action="{{ route('patient_portal.authenticate') }}" method="POST" class="space-y-5 relative">
                    @csrf

                    <div>
                        <label for="login" class="block text-sm font-semibold text-slate-700 mb-1">Patient ID, Phone, or Email</label>
                        <div class="relative">
                            <input type="text" name="login" id="login" required value="{{ old('login') }}"
                                placeholder="e.g. PAT-2026-0001 or +91 9876543210"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-apex-500 focus:border-apex-500 text-slate-900 placeholder-slate-400 font-medium text-sm transition">
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Enter your registered Phone Number or Patient ID code</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-semibold text-slate-700">Password / Access Pin</label>
                        </div>
                        <input type="password" name="password" id="password" required
                            placeholder="Enter password or DOB (YYYY-MM-DD)"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-apex-500 focus:border-apex-500 text-slate-900 placeholder-slate-400 font-medium text-sm transition">
                    </div>

                    <!-- Info Box -->
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 flex items-start space-x-3 text-xs text-slate-600">
                        <svg class="w-4 h-4 text-apex-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <span class="font-bold text-slate-800">First time logging in?</span> Use your <strong>Date of Birth (YYYY-MM-DD)</strong> or <strong>Mobile Number</strong> as default password.
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-4 bg-apex-600 hover:bg-apex-700 active:bg-apex-800 text-white font-bold rounded-xl shadow-lg shadow-apex-600/30 hover:shadow-apex-600/40 transition flex items-center justify-center space-x-2 text-sm">
                        <span>Sign In to Patient Portal</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </form>

                <!-- JWT Security Badge Footer -->
                <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                    <div class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-400">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>256-bit JWT Encrypted Session</span>
                    </div>
                </div>
            </div>

            <div class="text-center text-xs text-slate-400">
                Need help accessing your account? Call Hospital Helpline: <span class="font-semibold text-slate-600">+91 98765 43210</span>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Ekta Care Clinic CRM &bull; All Patient Data Secured & Encrypted
    </footer>

</body>
</html>
