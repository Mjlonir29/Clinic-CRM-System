<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Ekta Care Clinic CRM</title>
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
<body class="h-full font-sans antialiased text-slate-800 bg-white">
<div class="min-h-full flex">
    
    <!-- Left Side: Clinical Branding & Testimonial Panel -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-emerald-50/80 via-teal-50/60 to-cyan-50/80 p-12 flex-col justify-between relative border-r border-teal-100/60 overflow-hidden">
        
        <!-- Top Badges -->
        <div class="space-y-3 z-10">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-apex-600 flex items-center justify-center text-white shadow-lg shadow-apex-600/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-xl font-heading font-extrabold text-slate-900 tracking-tight">Ekta Care Clinic</h1>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-100 text-teal-800 border border-teal-200">OS v2.4</span>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">Secure Staff Password Recovery</p>
                    </div>
                </div>

                <div class="inline-flex items-center px-3 py-1 bg-white/80 backdrop-blur-md rounded-full border border-teal-200/80 text-[11px] font-bold text-teal-800 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    256-Bit Encrypted Account Recovery
                </div>
            </div>

            <!-- Middle Featured Doctor Photo Card -->
            <div class="relative my-auto py-6 z-10 max-w-lg mx-auto">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-slate-900">
                    <img src="{{ asset('images/doctor.png') }}" alt="Featured Doctor" class="w-full h-80 object-cover object-center opacity-95">
                </div>
            </div>

            <!-- Bottom Testimonial Card -->
            <div class="bg-white/90 backdrop-blur-md p-6 rounded-3xl border border-teal-100 shadow-lg z-10 max-w-lg mx-auto w-full">
                <p class="text-xs font-semibold text-slate-700 leading-relaxed italic">
                    "Password recovery requests are audited and processed with high security to maintain staff portal integrity."
                </p>
            </div>
        </div>

        <!-- Right Side: Forgot Password Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-8 sm:p-12 lg:p-16 bg-white overflow-y-auto">
            
            <!-- Top Action Links -->
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-2">
                <a href="{{ route('login') }}" class="text-apex-600 hover:text-apex-700 flex items-center">
                    ← Back to Sign In
                </a>
                <div class="flex items-center space-x-1 cursor-default text-slate-600">
                    🌐 <span>English (US)</span>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="max-w-md w-full mx-auto my-auto py-6 space-y-6">
                
                <!-- Lock Icon Header -->
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl bg-teal-50 text-apex-600 flex items-center justify-center font-bold shadow-sm border border-teal-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-heading font-extrabold text-slate-900 tracking-tight">Forgot Password?</h2>
                        <p class="text-[11px] font-semibold text-slate-500">Enter your registered work email to receive a 6-digit OTP code</p>
                    </div>
                </div>

                @if(session('status'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs space-y-2">
                        <div class="font-extrabold flex items-center">
                            <span class="mr-1.5">✓</span> {{ session('status') }}
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <!-- Email Input -->
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Email Address / Registered ID</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-slate-400 font-medium text-xs">@</span>
                            <input 
                                type="email" 
                                name="email" 
                                required 
                                value="{{ old('email') }}"
                                class="w-full pl-9 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-semibold focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all" 
                                placeholder="doctor@cliniccrm.com"
                            />
                        </div>
                    </div>

                    <!-- Primary Action Button -->
                    <button type="submit" class="w-full py-3.5 bg-apex-600 hover:bg-apex-700 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-apex-600/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                        <span>Send 6-Digit OTP Code</span>
                        <span>→</span>
                    </button>
                </form>

                <div class="pt-4 border-t border-slate-100 text-center text-xs font-semibold text-slate-500">
                    Remembered your password? <a href="{{ route('login') }}" class="text-apex-600 font-extrabold hover:underline">Sign In</a>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
