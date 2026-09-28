<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ekta Care Clinic') }} - Clinical Management CRM</title>

    <!-- Google Fonts: Outfit for headings/metrics & Plus Jakarta Sans for body -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
                        teal: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#006654',
                            900: '#004d3f',
                            950: '#00332a',
                        }
                    },
                    boxShadow: {
                        'glow': '0 0 25px -5px rgba(13, 148, 136, 0.25)',
                        'card': '0 4px 20px -2px rgba(15, 23, 42, 0.05)',
                        'soft': '0 10px 30px -5px rgba(0, 102, 84, 0.08)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js & Chart.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white; color: black; font-size: 12pt; }
        }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #0d9488; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-[#f4f8f7] selection:bg-teal-600 selection:text-white" x-data="{ 
    mobileMenuOpen: false, 
    notificationsOpen: false,
    searchOpen: false, 
    searchResults: null, 
    searchLoading: false, 
    searchQuery: '',
    currentLocation: 'Main Campus - Wing 3B'
}">
    <div class="min-h-full flex flex-col md:flex-row">
        
        <!-- Sidebar Navigation (Desktop) -->
        <aside class="hidden md:flex md:w-72 md:flex-col md:fixed md:inset-y-0 border-r border-slate-200/90 bg-white z-30 shadow-card">
            <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto">
                
                <!-- Clinic Brand Header -->
                <div class="flex items-center flex-shrink-0 px-6 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Ekta CRM Logo" class="w-10 h-10 object-contain mr-3 transform transition-transform hover:scale-105 rounded-xl border border-slate-100 shadow-sm">
                    <div>
                        <div class="flex items-center space-x-1.5">
                            <h1 class="text-lg font-heading font-extrabold text-slate-900 tracking-tight leading-none">Ekta CRM</h1>
                            <span class="text-[9px] font-black bg-teal-100 text-teal-800 px-1.5 py-0.5 rounded uppercase tracking-wider">v2.4</span>
                        </div>
                        <p class="text-[11px] font-bold text-teal-700 mt-1 uppercase tracking-wider">Clinical CRM System</p>
                    </div>
                </div>

                <!-- Active Doctor Duty Status Card -->
                <div class="mx-4 mb-6 p-3.5 rounded-2xl bg-gradient-to-r from-teal-50/70 to-emerald-50/60 border border-teal-100 flex items-center justify-between group">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-10 rounded-xl bg-teal-800 text-white font-extrabold flex items-center justify-center text-sm shadow-md">
                                {{ strtoupper(substr(auth()->user()->name ?? 'D', 0, 1)) }}
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <div class="flex items-center space-x-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] font-bold text-emerald-700">On Duty</span>
                            </div>
                        </div>
                    </div>
                    <span class="text-[10px] font-extrabold bg-white px-2 py-1 rounded-lg border border-teal-200/80 text-teal-800 shadow-2xs">
                        {{ auth()->user()->role->name ?? 'Attending' }}
                    </span>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 space-y-1 text-xs">
                    <p class="px-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Main Navigation</p>

                    @if(auth()->user()->role_slug !== 'nurse' && (auth()->user()->hasPermission('dashboard.view') || auth()->user()->role_slug === 'admin' || auth()->user()->role_slug === 'clinic_manager'))
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 font-bold rounded-2xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'text-slate-600 hover:bg-teal-50/60 hover:text-teal-900' }}">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-teal-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            Dashboard
                        </div>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('appointments.view') || auth()->user()->role_slug === 'admin' || auth()->user()->role_slug === 'clinic_manager')
                    <a href="{{ route('appointments.index') }}" class="flex items-center justify-between px-3.5 py-2.5 font-bold rounded-2xl transition-all duration-200 group {{ request()->routeIs('appointments.*') ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'text-slate-600 hover:bg-teal-50/60 hover:text-teal-900' }}">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('appointments.*') ? 'text-white' : 'text-slate-400 group-hover:text-teal-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Appointment
                        </div>
                    </a>
                    @endif

                    <a href="{{ route('doctors.index') }}" class="flex items-center justify-between px-3.5 py-2.5 font-bold rounded-2xl transition-all duration-200 group {{ request()->routeIs('doctors.*') ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'text-slate-600 hover:bg-teal-50/60 hover:text-teal-900' }}">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('doctors.*') ? 'text-white' : 'text-slate-400 group-hover:text-teal-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Doctors
                        </div>
                    </a>

                    @if(auth()->user()->hasPermission('patients.view') || auth()->user()->role_slug === 'admin' || auth()->user()->role_slug === 'clinic_manager')
                    <a href="{{ route('patients.index') }}" class="flex items-center justify-between px-3.5 py-2.5 font-bold rounded-2xl transition-all duration-200 group {{ request()->routeIs('patients.*') ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'text-slate-600 hover:bg-teal-50/60 hover:text-teal-900' }}">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('patients.*') ? 'text-white' : 'text-slate-400 group-hover:text-teal-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Patient
                        </div>
                    </a>
                    @endif

                    @if(auth()->user()->role_slug !== 'clinic_manager' && (auth()->user()->hasPermission('prescriptions.view') || auth()->user()->role_slug === 'admin'))
                    <a href="{{ route('prescriptions.index') }}" class="flex items-center justify-between px-3.5 py-2.5 font-bold rounded-2xl transition-all duration-200 group {{ request()->routeIs('prescriptions.*') ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'text-slate-600 hover:bg-teal-50/60 hover:text-teal-900' }}">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('prescriptions.*') ? 'text-white' : 'text-slate-400 group-hover:text-teal-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Prescriptions (Rx)
                        </div>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('invoices.view') || auth()->user()->role_slug === 'admin' || auth()->user()->role_slug === 'clinic_manager')
                    <a href="{{ route('invoices.index') }}" class="flex items-center justify-between px-3.5 py-2.5 font-bold rounded-2xl transition-all duration-200 group {{ request()->routeIs('invoices.*') ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'text-slate-600 hover:bg-teal-50/60 hover:text-teal-900' }}">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('invoices.*') ? 'text-white' : 'text-slate-400 group-hover:text-teal-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Billing & Invoices
                        </div>
                    </a>
                    @endif

                    @if(auth()->user()->role_slug !== 'clinic_manager' && (auth()->user()->role_slug === 'admin' || auth()->user()->hasPermission('reports.view') || auth()->user()->hasPermission('settings.edit')))
                    <p class="px-3 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider pt-4 mb-2">Clinic Admin</p>

                    @if(auth()->user()->hasPermission('reports.view') || auth()->user()->role_slug === 'admin')
                    <a href="{{ route('reports.index') }}" class="flex items-center justify-between px-3.5 py-2.5 font-bold rounded-2xl transition-all duration-200 group {{ request()->routeIs('reports.*') ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'text-slate-600 hover:bg-teal-50/60 hover:text-teal-900' }}">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('reports.*') ? 'text-white' : 'text-slate-400 group-hover:text-teal-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Reports & Financials
                        </div>
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('subaccounts.manage') || auth()->user()->role_slug === 'admin')
                    <a href="{{ route('subaccounts.index') }}" class="flex items-center justify-between px-3.5 py-2.5 font-bold rounded-2xl transition-all duration-200 group {{ request()->routeIs('subaccounts.*') ? 'bg-teal-800 text-white shadow-lg shadow-teal-900/20' : 'text-slate-600 hover:bg-teal-50/60 hover:text-teal-900' }}">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3 transition-transform group-hover:scale-110 {{ request()->routeIs('subaccounts.*') ? 'text-white' : 'text-slate-400 group-hover:text-teal-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            Staff Directory
                        </div>
                    </a>
                    @endif
                    @endif
                </nav>

                <!-- Footer HIPAA & Logout -->
                <div class="px-4 pt-4 mt-4 border-t border-slate-200/80 space-y-3">
                    <div class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between text-[10px] text-slate-500 font-bold">
                        <span class="flex items-center">
                            <svg class="w-3 h-3 text-teal-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            HIPAA Compliant
                        </span>
                        <span class="text-teal-700">256-Bit SSL</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 text-xs font-bold text-rose-600 bg-rose-50/80 hover:bg-rose-100 rounded-xl transition-all border border-rose-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Sign Out Session
                        </button>
                    </form>
                </div>

            </div>
        </aside>

        <!-- Mobile Drawer Navigation -->
        <div x-show="mobileMenuOpen" class="relative z-50 md:hidden" x-cloak>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="mobileMenuOpen = false"></div>
            <div class="fixed inset-y-0 left-0 w-4/5 max-w-xs bg-white p-6 shadow-2xl flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                        <div class="flex items-center">
                            <div class="w-9 h-9 rounded-xl bg-teal-800 flex items-center justify-center text-white mr-3 shadow-md">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <span class="font-extrabold text-slate-900 text-base font-heading">Ekta Care Clinic</span>
                        </div>
                        <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <nav class="mt-6 space-y-1 text-sm font-bold text-slate-700">
                        @if(auth()->user()->role_slug !== 'nurse' && (auth()->user()->hasPermission('dashboard.view') || auth()->user()->role_slug === 'admin' || auth()->user()->role_slug === 'clinic_manager'))
                        <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-2xl hover:bg-teal-50 hover:text-teal-800">Dashboard</a>
                        @endif

                        @if(auth()->user()->hasPermission('appointments.view') || auth()->user()->role_slug === 'admin' || auth()->user()->role_slug === 'clinic_manager')
                        <a href="{{ route('appointments.index') }}" class="block px-4 py-3 rounded-2xl hover:bg-teal-50 hover:text-teal-800">Appointments</a>
                        @endif

                        @if(auth()->user()->hasPermission('patients.view') || auth()->user()->role_slug === 'admin' || auth()->user()->role_slug === 'clinic_manager')
                        <a href="{{ route('patients.index') }}" class="block px-4 py-3 rounded-2xl hover:bg-teal-50 hover:text-teal-800">Patients Directory</a>
                        @endif

                        @if(auth()->user()->role_slug !== 'clinic_manager' && (auth()->user()->hasPermission('prescriptions.view') || auth()->user()->role_slug === 'admin'))
                        <a href="{{ route('prescriptions.index') }}" class="block px-4 py-3 rounded-2xl hover:bg-teal-50 hover:text-teal-800">Prescriptions (Rx)</a>
                        @endif

                        @if(auth()->user()->hasPermission('invoices.view') || auth()->user()->role_slug === 'admin' || auth()->user()->role_slug === 'clinic_manager')
                        <a href="{{ route('invoices.index') }}" class="block px-4 py-3 rounded-2xl hover:bg-teal-50 hover:text-teal-800">Billing & Invoices</a>
                        @endif

                        @if(auth()->user()->role_slug !== 'clinic_manager' && (auth()->user()->hasPermission('reports.view') || auth()->user()->role_slug === 'admin'))
                        <a href="{{ route('reports.index') }}" class="block px-4 py-3 rounded-2xl hover:bg-teal-50 hover:text-teal-800">Reports</a>
                        @endif

                        @if(auth()->user()->role_slug !== 'clinic_manager' && (auth()->user()->hasPermission('subaccounts.manage') || auth()->user()->role_slug === 'admin'))
                        <a href="{{ route('subaccounts.index') }}" class="block px-4 py-3 rounded-2xl hover:bg-teal-50 hover:text-teal-800">Staff Subaccounts</a>
                        @endif
                    </nav>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-center py-3 font-bold text-rose-600 bg-rose-50 rounded-2xl">Sign Out</button>
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="md:pl-72 flex flex-col flex-1 min-w-0">
            
            <!-- Glass Top Header Bar -->
            <header class="sticky top-0 z-20 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-8 py-3 flex items-center justify-between no-print shadow-sm">
                <div class="flex items-center space-x-4">
                    <button @click="mobileMenuOpen = true" class="md:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Global Search Bar with ⌘K Badge -->
                    <div class="relative w-64 sm:w-96">
                        <form action="{{ route('global.search') }}" method="GET" class="relative">
                            <input 
                                type="text" 
                                name="q" 
                                x-model="searchQuery"
                                @input.debounce.300ms="
                                    if (searchQuery.length >= 2) {
                                        searchLoading = true;
                                        fetch('{{ route('global.search') }}?q=' + encodeURIComponent(searchQuery), { headers: { 'Accept': 'application/json' } })
                                            .then(r => r.json())
                                            .then(data => { searchResults = data; searchLoading = false; searchOpen = true; });
                                    } else {
                                        searchResults = null;
                                        searchOpen = false;
                                    }
                                "
                                @click.away="searchOpen = false"
                                placeholder="Search Patients, Appointments, Rx, Invoices..." 
                                class="w-full pl-10 pr-12 py-2 text-xs bg-slate-100/90 hover:bg-slate-100 focus:bg-white border border-slate-200/80 focus:border-teal-700 rounded-xl focus:outline-none focus:ring-4 focus:ring-teal-700/10 transition-all font-medium"
                            />
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="absolute right-3 top-2 text-[10px] font-bold text-slate-400 bg-white border border-slate-200 px-1.5 py-0.5 rounded shadow-2xs hidden sm:inline">⌘K</span>
                        </form>

                        <!-- Instant Live Search Dropdown -->
                        <div x-show="searchOpen && searchResults" class="absolute left-0 right-0 top-12 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50 divide-y divide-slate-100 text-xs" x-cloak>
                            <template x-if="searchResults && searchResults.patients && searchResults.patients.length > 0">
                                <div class="p-3">
                                    <div class="font-extrabold text-[10px] text-teal-800 uppercase tracking-wider mb-2">Patients</div>
                                    <template x-for="p in searchResults.patients" :key="p.id">
                                        <a :href="'/patients/' + p.id" class="flex items-center justify-between p-2 hover:bg-teal-50/60 rounded-xl transition-all">
                                            <div>
                                                <span class="font-bold text-slate-900" x-text="p.first_name + ' ' + p.last_name"></span>
                                                <span class="text-slate-400 ml-1" x-text="'(' + p.patient_id + ')'"></span>
                                            </div>
                                            <span class="text-slate-500 font-medium" x-text="p.phone"></span>
                                        </a>
                                    </template>
                                </div>
                            </template>

                            <template x-if="searchResults && searchResults.appointments && searchResults.appointments.length > 0">
                                <div class="p-3">
                                    <div class="font-extrabold text-[10px] text-teal-800 uppercase tracking-wider mb-2">Appointments</div>
                                    <template x-for="a in searchResults.appointments" :key="a.id">
                                        <a :href="'/appointments/' + a.id" class="flex items-center justify-between p-2 hover:bg-teal-50/60 rounded-xl transition-all">
                                            <div>
                                                <span class="font-bold text-teal-800" x-text="a.appointment_number"></span>
                                                <span class="text-slate-800 ml-2" x-text="a.patient ? a.patient.first_name + ' ' + a.patient.last_name : ''"></span>
                                            </div>
                                            <span class="text-slate-500 font-medium" x-text="a.appointment_date + ' ' + a.appointment_time"></span>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Header Actions & Workable User Avatar Profile -->
                <div class="flex items-center space-x-3.5">
                    
                    <!-- Location Dropdown Selector -->
                    <div class="relative" x-data="{ locationOpen: false }">
                        <button @click="locationOpen = !locationOpen" class="hidden lg:flex items-center px-3 py-1.5 rounded-xl bg-slate-100/90 border border-slate-200/80 text-xs font-bold text-slate-700 space-x-1.5 hover:bg-slate-200/60 transition-all">
                            <svg class="w-3.5 h-3.5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span x-text="currentLocation">Main Campus - Wing 3B</span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="locationOpen" @click.away="locationOpen = false" class="absolute right-0 top-10 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-50 text-xs font-bold space-y-1" x-cloak>
                            <button @click="currentLocation = 'Main Campus - Wing 3B'; locationOpen = false" class="w-full text-left px-3 py-2 rounded-xl hover:bg-teal-50 text-teal-800">Main Campus - Wing 3B</button>
                            <button @click="currentLocation = 'North Clinic - Suite 101'; locationOpen = false" class="w-full text-left px-3 py-2 rounded-xl hover:bg-teal-50 text-slate-700">North Clinic - Suite 101</button>
                            <button @click="currentLocation = 'East Wing - OPD Cardiology'; locationOpen = false" class="w-full text-left px-3 py-2 rounded-xl hover:bg-teal-50 text-slate-700">East Wing - OPD Cardiology</button>
                        </div>
                    </div>

                    <!-- Notification Bell with Dropdown -->
                    <div class="relative">
                        @php
                            $navNotifications = \App\Models\ClinicNotification::where('user_id', auth()->id())->latest()->take(5)->get();
                            $unreadNavCount = \App\Models\ClinicNotification::where('user_id', auth()->id())->where('is_read', false)->count();
                        @endphp
                        <button @click="notificationsOpen = !notificationsOpen" class="relative p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 01-6 0v-1m6 0H9"></path>
                            </svg>
                            @if($unreadNavCount > 0)
                                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                            @endif
                        </button>

                        <div x-show="notificationsOpen" @click.away="notificationsOpen = false" class="absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 z-50 space-y-3" x-cloak>
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <h4 class="font-heading font-black text-xs text-slate-900">Clinic Notifications</h4>
                                @if($unreadNavCount > 0)
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black bg-rose-100 text-rose-800">{{ $unreadNavCount }} New</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-slate-100 text-slate-600">Up to date</span>
                                @endif
                            </div>
                            <div class="space-y-2 text-xs max-h-60 overflow-y-auto">
                                @forelse($navNotifications as $notif)
                                    <a href="{{ $notif->link ?: '#' }}" class="block p-2.5 rounded-xl bg-teal-50/70 border border-teal-200/80 hover:bg-teal-100/60 transition-colors">
                                        <span class="font-bold text-teal-900 block">{{ $notif->title }}</span>
                                        <span class="text-[10px] text-teal-700 font-medium block">{{ $notif->message }}</span>
                                        <span class="text-[9px] text-slate-400 font-semibold block mt-1">{{ $notif->created_at->diffForHumans() }}</span>
                                    </a>
                                @empty
                                    <div class="py-4 text-center text-slate-400 font-semibold text-xs">
                                        No new notifications.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Workable User Profile Avatar Button ("D") with Dynamic Particular Info Popout -->
                    <div class="relative flex items-center pl-2 border-l border-slate-200" x-data="{ userMenuOpen: false }">
                        <button @click="userMenuOpen = !userMenuOpen" title="Click to view Account Info & Settings" class="w-9 h-9 rounded-xl bg-teal-800 hover:bg-teal-900 active:bg-teal-950 text-white font-heading font-black flex items-center justify-center text-sm shadow-md transition-all transform hover:scale-105 cursor-pointer focus:outline-none ring-2 ring-teal-700/20">
                            {{ strtoupper(substr(auth()->user()->name ?? 'D', 0, 1)) }}
                        </button>

                        <!-- User Details Popout Dropdown Menu -->
                        <div x-show="userMenuOpen" @click.away="userMenuOpen = false" class="absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-4 z-50 space-y-3" x-cloak>
                            
                            <!-- User Profile Header Info -->
                            <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                                <div class="w-12 h-12 rounded-2xl bg-teal-800 text-white font-heading font-black flex items-center justify-center text-lg shadow-md flex-shrink-0">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'D', 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-heading font-black text-slate-900 text-sm truncate">{{ auth()->user()->name }}</h4>
                                    <p class="text-[11px] text-slate-500 font-medium truncate">{{ auth()->user()->email }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black bg-teal-50 text-teal-800 border border-teal-200 uppercase">
                                            {{ auth()->user()->role->name ?? ucfirst(auth()->user()->role_slug) }}
                                        </span>
                                        <span class="flex items-center text-[10px] font-bold text-emerald-700">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1 animate-pulse"></span>
                                            On Duty
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Particular Details Grid -->
                            <div class="space-y-2 text-xs font-semibold text-slate-600 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-slate-400 font-bold uppercase text-[9px]">Username:</span>
                                    <span class="font-mono text-slate-900 font-bold bg-white px-2 py-0.5 rounded border border-slate-200">{{ auth()->user()->username ?? 'N/A' }}</span>
                                </div>
                                @if(auth()->user()->phone)
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-slate-400 font-bold uppercase text-[9px]">Phone:</span>
                                    <span class="font-bold text-slate-900">{{ auth()->user()->phone }}</span>
                                </div>
                                @endif
                                @if(auth()->user()->qualifications)
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-slate-400 font-bold uppercase text-[9px]">Degrees:</span>
                                    <span class="font-bold text-teal-800">{{ auth()->user()->qualifications }}</span>
                                </div>
                                @endif
                                @if(auth()->user()->registration_number)
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="text-slate-400 font-bold uppercase text-[9px]">MCI Reg #:</span>
                                    <span class="font-mono text-slate-900 font-bold">{{ auth()->user()->registration_number }}</span>
                                </div>
                                @endif
                                <div class="flex justify-between items-center text-[11px] pt-1 border-t border-slate-200/60">
                                    <span class="text-slate-400 font-bold uppercase text-[9px]">Role Status:</span>
                                    <span class="text-emerald-700 font-extrabold uppercase text-[10px]">● Active Account</span>
                                </div>
                            </div>

                            <!-- Quick Navigation Links -->
                            <div class="pt-1 space-y-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-3 py-2 rounded-xl text-xs font-extrabold text-rose-600 hover:bg-rose-50 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Sign Out Session
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between shadow-card" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-xs">✓</div>
                            <p class="text-xs font-extrabold">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-emerald-700 hover:text-emerald-950 font-black text-lg">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center justify-between shadow-card" x-data="{ show: true }" x-show="show">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center font-black text-xs">!</div>
                            <p class="text-xs font-extrabold">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-rose-700 hover:text-rose-950 font-black text-lg">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
