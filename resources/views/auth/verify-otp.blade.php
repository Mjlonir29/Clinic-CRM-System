<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify OTP Code - Ekta Care Clinic CRM</title>
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
                        <p class="text-xs font-semibold text-slate-500">Secure Staff OTP Verification</p>
                    </div>
                </div>

                <div class="inline-flex items-center px-3 py-1 bg-white/80 backdrop-blur-md rounded-full border border-teal-200/80 text-[11px] font-bold text-teal-800 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    256-Bit Encrypted OTP Protection
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
                    "Enter the 6-digit OTP sent to your registered email address to verify identity."
                </p>
            </div>
        </div>

        <!-- Right Side: OTP Verification Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-between p-8 sm:p-12 lg:p-16 bg-white overflow-y-auto">
            
            <!-- Top Action Links -->
            <div class="flex items-center justify-between text-xs font-bold text-slate-500 mb-2">
                <a href="{{ route('password.request') }}" class="text-apex-600 hover:text-apex-700 flex items-center">
                    ← Request New OTP
                </a>
                <div class="flex items-center space-x-1 cursor-default text-slate-600">
                    🌐 <span>English (US)</span>
                </div>
            </div>

            <!-- Main Form Card -->
            <div class="max-w-md w-full mx-auto my-auto py-6 space-y-6">
                
                <!-- Shield Icon Header -->
                <div class="flex items-center space-x-3">
                    <div class="w-11 h-11 rounded-2xl bg-teal-50 text-apex-600 flex items-center justify-center font-bold shadow-sm border border-teal-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-heading font-extrabold text-slate-900 tracking-tight">Enter 6-Digit OTP</h2>
                        <p class="text-[11px] font-semibold text-slate-500">OTP code dispatched to <span class="text-slate-800 font-extrabold">{{ $email }}</span></p>
                    </div>
                </div>

                @if(session('status'))
                    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs space-y-2">
                        <div class="font-extrabold flex items-center justify-between">
                            <span><span class="mr-1.5">📩</span> {{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.verify_otp') }}" class="space-y-5" id="otpForm">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <!-- 6 Digit OTP Inputs -->
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-2 text-center">Enter 6-Digit Verification Code</label>
                        <div class="flex items-center justify-center space-x-2 sm:space-x-3" id="otpBoxContainer">
                            @for($i = 0; $i < 6; $i++)
                                <input 
                                    type="text" 
                                    maxlength="1" 
                                    pattern="[0-9]"
                                    inputmode="numeric"
                                    class="otp-digit w-11 h-12 text-center text-lg font-black text-slate-900 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all font-mono"
                                    data-index="{{ $i }}"
                                    required
                                />
                            @endfor
                        </div>
                        <input type="hidden" name="otp" id="fullOtpInput" value="" />
                    </div>

                    <!-- Primary Action Button -->
                    <button type="submit" class="w-full py-3.5 bg-apex-600 hover:bg-apex-700 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-apex-600/30 transition-all transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                        <span>Verify OTP & Proceed</span>
                        <span>→</span>
                    </button>
                </form>

                <div class="pt-4 border-t border-slate-100 text-center text-xs font-semibold text-slate-500">
                    Didn't receive the OTP? 
                    <a href="{{ route('password.request') }}" class="text-apex-600 font-extrabold hover:underline ml-1">Resend OTP</a>
                </div>

            </div>
        </div>
    </div>

    <script>
        const digits = document.querySelectorAll('.otp-digit');
        const fullOtpInput = document.getElementById('fullOtpInput');
        const otpForm = document.getElementById('otpForm');

        function syncFullOtp() {
            let val = '';
            digits.forEach(d => val += d.value);
            fullOtpInput.value = val;
        }

        digits.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1) {
                    if (index < digits.length - 1) {
                        digits[index + 1].focus();
                    }
                }
                syncFullOtp();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    digits[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                if (/^\d{6}$/.test(pasteData)) {
                    autoFillOtp(pasteData);
                }
            });
        });

        function autoFillOtp(code) {
            if (!code || code.length !== 6) return;
            const charArray = code.split('');
            digits.forEach((d, i) => {
                d.value = charArray[i] || '';
            });
            syncFullOtp();
        }

        otpForm.addEventListener('submit', function(e) {
            syncFullOtp();
        });
    </script>
</body>
</html>
