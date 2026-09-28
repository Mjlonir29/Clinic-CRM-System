<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Patient Portal - {{ $patient->full_name }} | Ekta Care Clinic</title>

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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col justify-between" x-data="{ activeTab: 'notifications' }">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('patient_portal.dashboard') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Ekta Care Logo" class="w-10 h-10 object-contain rounded-xl border border-slate-100 shadow-sm">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-heading font-extrabold text-xl tracking-tight text-slate-900">Ekta Care</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">Patient Account</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">JWT Authenticated Portal</p>
                    </div>
                </a>
            </div>

            <!-- Patient User Profile & Actions -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('patient.book') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-apex-600 hover:bg-apex-700 rounded-xl transition shadow-md shadow-apex-600/20">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Book Appointment
                </a>

                <div class="flex items-center space-x-3 pl-3 border-l border-slate-200">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-slate-900">{{ $patient->full_name }}</div>
                        <div class="text-xs text-slate-500 font-medium">{{ $patient->patient_id }} &bull; {{ $patient->phone }}</div>
                    </div>

                    <form action="{{ route('patient_portal.logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition border border-slate-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Sub Banner for Patient Information -->
    <div class="bg-gradient-to-r from-apex-900 via-apex-800 to-slate-900 text-white py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 relative z-10">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-3xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center text-emerald-300 font-heading font-extrabold text-2xl shadow-inner">
                    {{ strtoupper(substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-extrabold font-heading tracking-tight">{{ $patient->full_name }}</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">Active Patient</span>
                    </div>
                    <div class="text-sm text-slate-300 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 font-medium">
                        <span><strong>Patient ID:</strong> {{ $patient->patient_id }}</span>
                        <span>&bull;</span>
                        <span><strong>Age / Gender:</strong> {{ $patient->age ?? 'N/A' }} Yrs / {{ $patient->gender }}</span>
                        <span>&bull;</span>
                        <span><strong>Blood Group:</strong> {{ $patient->blood_group ?? 'N/A' }}</span>
                        <span>&bull;</span>
                        <span><strong>Phone:</strong> {{ $patient->phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center space-x-3">
                <a href="#lab-documents-section" @click="activeTab = 'documents'" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-xl text-xs font-semibold backdrop-blur border border-white/20 transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Lab Reports ({{ $patient->documents->count() }})</span>
                </a>

                <a href="#notifications-section" @click="activeTab = 'notifications'" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs transition flex items-center space-x-1.5 shadow-lg shadow-emerald-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"></path>
                    </svg>
                    <span>Inbox Notifications</span>
                    @if($unreadCount > 0)
                        <span class="ml-1 bg-slate-950 text-emerald-400 text-[10px] font-black px-1.5 py-0.5 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-8">

        <!-- Flash Alert -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center space-x-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="font-medium flex-1">{{ session('success') }}</div>
            </div>
        @endif

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Notifications Card -->
            <div @click="activeTab = 'notifications'" class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Account Inbox</span>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold font-heading text-slate-900">{{ $patient->notifications->count() }}</div>
                    <div class="text-xs text-slate-500 mt-0.5 flex items-center space-x-1">
                        @if($unreadCount > 0)
                            <span class="text-emerald-600 font-bold">{{ $unreadCount }} new unread updates</span>
                        @else
                            <span>All notifications up to date</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lab Reports Card -->
            <div @click="activeTab = 'documents'" class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Lab & Medical Reports</span>
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold font-heading text-slate-900">{{ $patient->documents->count() }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Uploaded test & lab documents</div>
                </div>
            </div>

            <!-- Appointments Card -->
            <div @click="activeTab = 'appointments'" class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Appointments</span>
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold font-heading text-slate-900">{{ $patient->appointments->count() }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Total scheduled visits</div>
                </div>
            </div>

            <!-- Prescriptions Card -->
            <div @click="activeTab = 'prescriptions'" class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition cursor-pointer group">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold tracking-wider uppercase text-slate-400">Prescriptions</span>
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-extrabold font-heading text-slate-900">{{ $patient->prescriptions->count() }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Doctor prescribed medications</div>
                </div>
            </div>

        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-slate-200 flex items-center space-x-6 overflow-x-auto pb-1 scrollbar-none">
            <button @click="activeTab = 'notifications'" :class="activeTab === 'notifications' ? 'border-apex-600 text-apex-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-semibold'" class="py-3 px-1 border-b-2 text-sm whitespace-nowrap transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"></path>
                </svg>
                <span>Patient Inbox & Notifications</span>
                @if($unreadCount > 0)
                    <span class="px-2 py-0.5 bg-rose-500 text-white rounded-full text-xs font-bold">{{ $unreadCount }}</span>
                @endif
            </button>

            <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'border-apex-600 text-apex-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-semibold'" class="py-3 px-1 border-b-2 text-sm whitespace-nowrap transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Lab Reports & Documents ({{ $patient->documents->count() }})</span>
            </button>

            <button @click="activeTab = 'appointments'" :class="activeTab === 'appointments' ? 'border-apex-600 text-apex-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-semibold'" class="py-3 px-1 border-b-2 text-sm whitespace-nowrap transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Appointments History</span>
            </button>

            <button @click="activeTab = 'prescriptions'" :class="activeTab === 'prescriptions' ? 'border-apex-600 text-apex-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-semibold'" class="py-3 px-1 border-b-2 text-sm whitespace-nowrap transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.6 15.12a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                <span>Prescriptions & Advice</span>
            </button>

            <button @click="activeTab = 'invoices'" :class="activeTab === 'invoices' ? 'border-apex-600 text-apex-700 font-extrabold' : 'border-transparent text-slate-500 hover:text-slate-700 font-semibold'" class="py-3 px-1 border-b-2 text-sm whitespace-nowrap transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Billing Receipts (₹)</span>
            </button>
        </div>

        <!-- TAB 1: NOTIFICATIONS & ALERT INBOX -->
        <div x-show="activeTab === 'notifications'" id="notifications-section" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold font-heading text-slate-900">Patient Notification Inbox</h2>
                    <p class="text-xs text-slate-500">Real-time alerts for recent lab reports, appointment reminders & clinic updates</p>
                </div>
            </div>

            @if($patient->notifications->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Your Inbox is Clear</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">There are no pending notifications for your account. When lab reports are ready or appointments approach, you'll be notified here.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($patient->notifications as $notif)
                        <div class="bg-white rounded-2xl p-5 border {{ $notif->is_read ? 'border-slate-200 bg-white' : 'border-emerald-300 bg-emerald-50/40 shadow-sm' }} transition flex items-start justify-between space-x-4">
                            <div class="flex items-start space-x-4">
                                <!-- Icon based on type -->
                                <div class="w-10 h-10 rounded-2xl shrink-0 flex items-center justify-center {{ $notif->type === 'lab_report_ready' ? 'bg-sky-100 text-sky-700' : ($notif->type === 'appointment_reminder' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700') }}">
                                    @if($notif->type === 'lab_report_ready')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    @elseif($notif->type === 'appointment_reminder')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>

                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-slate-900 text-sm">{{ $notif->title }}</span>
                                        @if(!$notif->is_read)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-600 text-white uppercase tracking-wider">NEW</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $notif->message }}</p>
                                    <div class="text-[11px] text-slate-400 font-medium">Received {{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->format('d M Y, h:i A') }})</div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 shrink-0">
                                @if($notif->link)
                                    <a href="{{ $notif->link }}" target="_blank" class="px-3 py-1.5 text-xs font-bold text-apex-700 bg-apex-50 hover:bg-apex-100 rounded-xl transition border border-apex-200 flex items-center space-x-1">
                                        <span>View Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                @endif

                                @if(!$notif->is_read)
                                    <form action="{{ route('patient_portal.notifications.read', $notif->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition border border-slate-200">
                                            Mark Read
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 2: LAB REPORTS & DOCUMENTS -->
        <div x-show="activeTab === 'documents'" id="lab-documents-section" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold font-heading text-slate-900">Lab Test Reports & Medical Files</h2>
                    <p class="text-xs text-slate-500">Official medical test results, pathology reports, and uploaded documents</p>
                </div>
            </div>

            @if($patient->documents->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">No Medical Documents Available</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">When lab test results or diagnostic scans are uploaded by clinic doctors, they will appear here instantly for download.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($patient->documents as $doc)
                        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition flex items-center justify-between">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $doc->title }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">Uploaded on {{ $doc->created_at->format('d M Y') }} &bull; {{ strtoupper($doc->file_type) }}</div>
                                    @if($doc->notes)
                                        <p class="text-xs text-slate-600 italic mt-1">"{{ $doc->notes }}"</p>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ $doc->file_path }}" target="_blank" download class="px-3.5 py-2 text-xs font-bold text-apex-700 bg-apex-50 hover:bg-apex-100 rounded-xl transition border border-apex-200 flex items-center space-x-1 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span>Download</span>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 3: APPOINTMENTS -->
        <div x-show="activeTab === 'appointments'" id="appointments-section" class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold font-heading text-slate-900">Scheduled Appointments</h2>
                    <p class="text-xs text-slate-500">Your upcoming and past consultation appointments</p>
                </div>
                <a href="{{ route('patient.book') }}" class="px-4 py-2 bg-apex-600 text-white font-bold rounded-xl text-xs hover:bg-apex-700 transition shadow-md shadow-apex-600/20 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Book New Appointment</span>
                </a>
            </div>

            @if($patient->appointments->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
                    <h3 class="text-base font-bold text-slate-800">No Appointments Recorded</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Book your first doctor appointment online in seconds.</p>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-extrabold text-slate-400 tracking-wider">
                                <tr>
                                    <th class="py-3.5 px-5">Date & Time</th>
                                    <th class="py-3.5 px-5">Consulting Doctor</th>
                                    <th class="py-3.5 px-5">Consultation Type</th>
                                    <th class="py-3.5 px-5">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($patient->appointments as $apt)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-5">
                                            <div class="font-bold text-slate-900">{{ date('d M Y', strtotime($apt->appointment_date)) }}</div>
                                            <div class="text-xs text-slate-500">{{ $apt->appointment_time }}</div>
                                        </td>
                                        <td class="py-4 px-5">
                                            <div class="font-bold text-slate-900">{{ $apt->doctor ? $apt->doctor->name : 'Specialist Doctor' }}</div>
                                            <div class="text-xs text-slate-500">{{ $apt->doctor ? $apt->doctor->specialization : 'General Medicine' }}</div>
                                        </td>
                                        <td class="py-4 px-5 text-slate-700">{{ $apt->type ?? 'General Consultation' }}</td>
                                        <td class="py-4 px-5">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $apt->status === 'Confirmed' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($apt->status === 'Completed' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-800 border border-amber-200') }}">
                                                {{ $apt->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- TAB 4: PRESCRIPTIONS -->
        <div x-show="activeTab === 'prescriptions'" class="space-y-4">
            <div>
                <h2 class="text-xl font-extrabold font-heading text-slate-900">Doctor Prescriptions</h2>
                <p class="text-xs text-slate-500">Official medication advice issued during consultations</p>
            </div>

            @if($patient->prescriptions->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
                    <h3 class="text-base font-bold text-slate-800">No Prescriptions On File</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Prescriptions issued by your doctor will be listed here with full dosage details.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($patient->prescriptions as $pres)
                        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div>
                                    <div class="text-xs text-slate-400 font-extrabold uppercase tracking-wider">Rx Prescription</div>
                                    <div class="font-extrabold text-slate-900 text-base">Date: {{ date('d M Y', strtotime($pres->prescription_date)) }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-slate-500">Prescribed by</div>
                                    <div class="font-bold text-apex-700 text-sm">{{ $pres->doctor ? $pres->doctor->name : 'Specialist Doctor' }}</div>
                                </div>
                            </div>

                            @if($pres->diagnosis)
                                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-xs text-slate-700">
                                    <strong>Diagnosis / Notes:</strong> {{ $pres->diagnosis }}
                                </div>
                            @endif

                            @if($pres->items && $pres->items->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-50 text-slate-500 uppercase font-bold">
                                            <tr>
                                                <th class="py-2.5 px-3">Medicine Name</th>
                                                <th class="py-2.5 px-3">Dosage</th>
                                                <th class="py-2.5 px-3">Frequency</th>
                                                <th class="py-2.5 px-3">Duration</th>
                                                <th class="py-2.5 px-3">Instructions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 font-medium">
                                            @foreach($pres->items as $item)
                                                <tr>
                                                    <td class="py-2.5 px-3 font-bold text-slate-900">{{ $item->medicine_name }}</td>
                                                    <td class="py-2.5 px-3 text-slate-700">{{ $item->dosage }}</td>
                                                    <td class="py-2.5 px-3 text-slate-700">{{ $item->frequency }}</td>
                                                    <td class="py-2.5 px-3 text-slate-700">{{ $item->duration }}</td>
                                                    <td class="py-2.5 px-3 text-slate-600">{{ $item->instructions ?? 'Take after meal' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- TAB 5: INVOICES & BILLING (₹) -->
        <div x-show="activeTab === 'invoices'" class="space-y-4">
            <div>
                <h2 class="text-xl font-extrabold font-heading text-slate-900">Invoices & Payment Receipts (₹)</h2>
                <p class="text-xs text-slate-500">Billing details formatted in Indian standard currency (INR / ₹)</p>
            </div>

            @if($patient->invoices->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-200">
                    <h3 class="text-base font-bold text-slate-800">No Invoices Found</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Payment receipts for your consultations and clinic services will be stored here.</p>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase font-extrabold text-slate-400 tracking-wider">
                                <tr>
                                    <th class="py-3.5 px-5">Invoice #</th>
                                    <th class="py-3.5 px-5">Date</th>
                                    <th class="py-3.5 px-5">Total Amount (₹)</th>
                                    <th class="py-3.5 px-5">Paid Amount (₹)</th>
                                    <th class="py-3.5 px-5">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($patient->invoices as $inv)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-5 font-bold text-slate-900">{{ $inv->invoice_number }}</td>
                                        <td class="py-4 px-5 text-slate-600">{{ date('d M Y', strtotime($inv->invoice_date)) }}</td>
                                        <td class="py-4 px-5 font-extrabold text-slate-900">₹{{ number_format($inv->total_amount, 2) }}</td>
                                        <td class="py-4 px-5 font-bold text-emerald-700">₹{{ number_format($inv->paid_amount, 2) }}</td>
                                        <td class="py-4 px-5">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $inv->status === 'Paid' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                                {{ $inv->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Ekta Care Clinic Patient Portal &bull; Secured with JWT Auth
    </footer>

</body>
</html>
