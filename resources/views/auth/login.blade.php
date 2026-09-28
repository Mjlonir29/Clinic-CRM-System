<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - Ekta Care Clinic CRM</title>

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
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50" x-data="{ loginEmail: '{{ old('login', $savedLogin ?? 'doctor@cliniccrm.com') }}', password: '{{ $savedPassword ?? 'password' }}', showPassword: false }">
    <div class="min-h-full flex">
        
        <!-- Left Side: Clean Professional Hospital CRM Panel (No photo, No demo testimonial) -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-apex-900 via-apex-800 to-slate-900 p-12 lg:p-16 flex-col justify-between relative overflow-hidden text-white">
            <!-- Background Decorative Glows -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Brand Header -->
            <div class="relative z-10 space-y-4">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Ekta Care Logo" class="w-12 h-12 object-contain rounded-xl bg-white p-1 shadow-lg shadow-emerald-500/20">
                    <div>
                        <h1 class="text-2xl font-heading font-extrabold tracking-tight text-white">Ekta Care Clinic</h1>
                        <p class="text-xs font-semibold text-emerald-300">Clinical Management & CRM System</p>
                    </div>
                </div>
            </div>

            <!-- Center Feature Cards List (Replacing photo & demo description) -->
            <div class="relative z-10 space-y-6 my-auto max-w-lg">
                <div class="space-y-2">
                    <h2 class="text-2xl font-extrabold font-heading tracking-tight text-white">Integrated Healthcare Management</h2>
                    <p class="text-xs text-slate-300 leading-relaxed">Unified portal for doctors, staff, receptionists, accountants, and patients.</p>
                </div>

                <div class="space-y-4">
                    <!-- Feature 1 -->
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-300 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Electronic Medical Records & Prescriptions</h3>
                            <p class="text-xs text-slate-300 mt-0.5">Real-time patient history, vitals tracking, diagnosis notes, and digital Rx issuing.</p>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-sky-500/20 text-sky-300 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Smart Appointment Scheduling</h3>
                            <p class="text-xs text-slate-300 mt-0.5">Streamlined front-desk check-in, calendar view, and 24/7 online patient booking.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-white">Billing & Payment Processing (₹)</h3>
                            <p class="text-xs text-slate-300 mt-0.5">Indian Rupee currency formatting, UPI/Cash receipts, and financial reporting.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom System Status -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <div class="flex items-center space-x-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Hospital Systems & Database Online</span>
                </div>
                <span>Secured 256-bit Portal</span>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-8 sm:p-12 lg:p-16 bg-white overflow-y-auto">
            
            <div class="max-w-md w-full mx-auto my-auto py-6 space-y-8">
                
                <!-- Sign In Header -->
                <div class="space-y-2">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-apex-50 text-apex-700 text-xs font-bold border border-apex-200 mb-2">
                        <svg class="w-3.5 h-3.5 text-apex-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <span>Staff & Provider Portal</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 tracking-tight">Sign In</h2>
                    <p class="text-xs text-slate-500 font-medium">Enter your registered email or username to access your account.</p>
                </div>

                @if($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center space-x-2">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf

                    <!-- Input 1: Email or Username -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Email or Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="login" 
                                x-model="loginEmail" 
                                required 
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-apex-600 focus:bg-white focus:ring-4 focus:ring-apex-600/10 transition-all" 
                                placeholder="Enter email or username"
                            />
                        </div>
                    </div>

                    <!-- Input 2: Password -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                name="password" 
                                x-model="password" 
                                required 
                                class="w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-apex-600 focus:bg-white focus:ring-4 focus:ring-apex-600/10 transition-all" 
                                placeholder="••••••••••••••••"
                            />
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 text-xs font-bold">
                                <span x-text="showPassword ? '🙈' : '👁️'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Options: Keep me signed in & Forgot Password under password box -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center text-slate-600 font-semibold cursor-pointer">
                            <input type="checkbox" name="remember" checked class="rounded border-slate-300 text-apex-600 focus:ring-apex-600 mr-2">
                            Keep me signed in for 30 days
                        </label>
                        <a href="{{ route('password.request') }}" class="text-xs text-apex-600 font-extrabold hover:underline">Forgot password?</a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full py-3.5 bg-apex-600 hover:bg-apex-700 active:bg-apex-800 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-apex-600/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                        <span>Sign In to Account</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </form>

            </div>

        </div>
    </div>
</body>
</html>
