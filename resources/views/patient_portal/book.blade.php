<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Patient Appointment - Ekta Care Clinic</title>

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
<body class="min-h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col" x-data="{
    selectedDoctorId: '{{ $doctors->first()->id ?? 1 }}',
    selectedTimeSlot: '{{ $timeSlots[0] }}',
    selectedType: 'General Consultation',
    appointmentDate: '{{ $today }}'
}">

    <!-- Top Public Hospital Banner -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            
            <!-- Logo & Brand -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('login') }}" class="flex items-center space-x-3 group">
                    <img src="{{ asset('images/logo.png') }}" alt="Ekta Care Logo" class="w-10 h-10 object-contain rounded-xl border border-slate-100 shadow-sm group-hover:scale-105 transition-transform">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-heading font-extrabold text-slate-900 tracking-tight">Ekta Care Clinic</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-100 text-teal-800 border border-teal-200">Patient Portal</span>
                        </div>
                        <p class="text-xs font-semibold text-slate-500">24/7 Direct Online Appointment Booking</p>
                    </div>
                </a>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center space-x-3">
                <div class="hidden md:flex items-center space-x-2 text-xs font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    <span>Emergency Hotline: <strong>+91 1800 123 4567</strong></span>
                </div>
                <a href="{{ route('patient_portal.login') }}" class="px-4 py-2 bg-apex-600 hover:bg-apex-700 text-white font-extrabold text-xs rounded-xl transition-all shadow-md shadow-apex-600/20 flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Patient Login</span>
                </a>
                <a href="{{ route('login') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-xl transition-all border border-slate-200 flex items-center space-x-1">
                    <span>Staff Sign In</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Booking Success Receipt Modal Card -->
        @if(session('booking_success'))
            @php $res = session('booking_success'); @endphp
            <div class="bg-gradient-to-br from-emerald-950 via-apex-900 to-slate-900 rounded-3xl p-8 text-white shadow-2xl border border-teal-500/30 relative overflow-hidden animate-fade-in">
                <!-- Background Glow -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex items-center justify-between pb-6 border-b border-white/10">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-2xl border border-emerald-500/30">
                            ✓
                        </div>
                        <div>
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-extrabold uppercase tracking-widest bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Confirmed & Scheduled</span>
                            <h2 class="text-2xl font-heading font-extrabold text-white mt-1">Appointment Successfully Booked!</h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Reference Code</span>
                        <span class="text-xl font-heading font-extrabold text-teal-300">{{ $res['appointment_number'] }}</span>
                    </div>
                </div>

                <!-- Receipt Detail Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 py-6 text-xs border-b border-white/10">
                    <div>
                        <span class="text-slate-400 font-bold block mb-1">Patient Name</span>
                        <span class="text-sm font-extrabold text-white">{{ $res['patient_name'] }}</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">📞 {{ $res['phone'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-1">Attending Specialist</span>
                        <span class="text-sm font-extrabold text-teal-300">{{ $res['doctor_name'] }}</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Department of Medicine</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-1">Date & Time Slot</span>
                        <span class="text-sm font-extrabold text-white">{{ $res['date'] }}</span>
                        <span class="text-[11px] text-emerald-400 font-bold block mt-0.5">🕒 {{ $res['time'] }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold block mb-1">Clinic Room</span>
                        <span class="text-sm font-extrabold text-white">{{ $res['location'] }}</span>
                        <span class="text-[11px] text-slate-400 block mt-0.5">Please arrive 10 min early</span>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="pt-6 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-xs font-semibold text-slate-300">
                        📧 A confirmation SMS/Email notification has been sent to your contact address.
                    </p>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('patient.book') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition-all border border-white/20">
                            Book Another Appointment
                        </a>
                        <a href="{{ route('login') }}" class="px-5 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs rounded-xl shadow-lg transition-all">
                            Go to Staff CRM Login →
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Hero Header -->
        <div class="bg-gradient-to-r from-apex-700 via-apex-600 to-teal-600 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
            <div class="relative z-10 max-w-2xl space-y-3">
                <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold text-teal-100">
                    🏥 Direct Hospital Consultation Booking
                </span>
                <h1 class="text-3xl sm:text-4xl font-heading font-extrabold tracking-tight">Schedule Your Doctor Visit</h1>
                <p class="text-sm text-teal-100 font-medium leading-relaxed">
                    Select your preferred specialist doctor, appointment date, and time slot. Your booking will be immediately registered in the hospital schedule.
                </p>
            </div>
            <!-- Decorative Medical Graphic -->
            <div class="absolute right-6 -bottom-6 opacity-20 pointer-events-none hidden md:block">
                <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-1.99.9-1.99 2L3 19c0 1.1.89 2 1.99 2H19c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-1 11h-4v4h-4v-4H6v-4h4V6h4v4h4v4z"/>
                </svg>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        <!-- Main Booking Form Card -->
        <form method="POST" action="{{ route('patient.book.store') }}" class="bg-white rounded-3xl p-8 shadow-lg border border-slate-200 space-y-8">
            @csrf

            <!-- Section 1: Patient Information -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 text-apex-600 font-extrabold flex items-center justify-center text-xs">1</div>
                    <h3 class="text-base font-heading font-extrabold text-slate-900">Patient Personal Details</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">First Name *</label>
                        <input type="text" name="first_name" required placeholder="e.g. John" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Last Name *</label>
                        <input type="text" name="last_name" required placeholder="e.g. Smith" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Phone Number *</label>
                        <input type="tel" name="phone" required placeholder="+91 98765 43210" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" name="email" placeholder="patient@example.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Gender *</label>
                        <select name="gender" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Age</label>
                        <input type="number" name="age" value="35" min="1" max="120" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 2: Select Specialist Doctor -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 text-apex-600 font-extrabold flex items-center justify-center text-xs">2</div>
                    <h3 class="text-base font-heading font-extrabold text-slate-900">Select Specialist Doctor</h3>
                </div>

                <input type="hidden" name="doctor_id" :value="selectedDoctorId">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($doctors as $doc)
                        <div 
                            @click="selectedDoctorId = '{{ $doc->id }}'"
                            :class="selectedDoctorId == '{{ $doc->id }}' ? 'border-apex-600 bg-teal-50/60 ring-2 ring-apex-600/20 shadow-md' : 'border-slate-200 bg-white hover:border-slate-300'"
                            class="p-4 rounded-2xl border cursor-pointer transition-all flex items-start space-x-3 relative"
                        >
                            <div class="w-12 h-12 rounded-2xl bg-apex-600 text-white font-extrabold flex items-center justify-center text-base shrink-0 shadow-sm">
                                {{ strtoupper(substr($doc->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-extrabold text-slate-900 truncate">{{ $doc->name }}</h4>
                                <p class="text-[11px] font-semibold text-apex-600 mt-0.5">{{ $doc->specialization ?? 'General Medicine' }}</p>
                                <p class="text-[10px] text-slate-500 font-medium mt-1">📍 Room {{ $doc->cabin_number ?? 'OPD 1' }} • Available</p>
                            </div>
                            <div x-show="selectedDoctorId == '{{ $doc->id }}'" class="w-5 h-5 rounded-full bg-apex-600 text-white flex items-center justify-center text-[10px] font-bold">
                                ✓
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Section 3: Date & Preferred Time Slot -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 text-apex-600 font-extrabold flex items-center justify-center text-xs">3</div>
                    <h3 class="text-base font-heading font-extrabold text-slate-900">Select Date & Time Slot</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Appointment Date *</label>
                        <input 
                            type="date" 
                            name="appointment_date" 
                            x-model="appointmentDate"
                            min="{{ $today }}" 
                            required 
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Available Time Slots *</label>
                        <input type="hidden" name="appointment_time" :value="selectedTimeSlot">
                        
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                            @foreach($timeSlots as $slot)
                                <button 
                                    type="button" 
                                    @click="selectedTimeSlot = '{{ $slot }}'"
                                    :class="selectedTimeSlot === '{{ $slot }}' ? 'bg-apex-600 text-white font-extrabold shadow-sm' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold'"
                                    class="py-2.5 px-3 rounded-xl text-xs text-center transition-all"
                                >
                                    {{ $slot }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Visit Details & Symptoms -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 border-b border-slate-100 pb-3">
                    <div class="w-7 h-7 rounded-lg bg-teal-50 text-apex-600 font-extrabold flex items-center justify-center text-xs">4</div>
                    <h3 class="text-base font-heading font-extrabold text-slate-900">Consultation Type & Reason for Visit</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Consultation Type *</label>
                        <select name="appointment_type" x-model="selectedType" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all">
                            <option value="General Consultation">General Consultation</option>
                            <option value="Follow-up">Follow-up Visit</option>
                            <option value="Telehealth">Telehealth Virtual Visit</option>
                            <option value="Emergency">Urgent Care / Emergency</option>
                            <option value="Special Care">Specialist Care Consultation</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Known Allergies (Optional)</label>
                        <input type="text" name="allergies" placeholder="e.g. Penicillin, Sulfa, None" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-extrabold text-slate-700 mb-1.5">Reason for Visit / Symptoms</label>
                        <textarea name="reason_for_visit" rows="3" placeholder="Describe your primary symptoms, duration, or reason for requesting a consultation..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:bg-white focus:outline-none focus:border-apex-600 focus:ring-4 focus:ring-apex-600/10 transition-all"></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit CTA Bar -->
            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                <div class="text-xs font-semibold text-slate-500 flex items-center space-x-1.5">
                    <span>🔒 Safe & Encrypted • HIPAA Clinic Protected</span>
                </div>

                <button type="submit" class="px-8 py-4 bg-apex-600 hover:bg-apex-700 text-white font-extrabold text-xs rounded-2xl shadow-xl shadow-apex-600/30 transition-all transform hover:-translate-y-0.5 flex items-center space-x-2">
                    <span>⚡ Confirm & Book Clinic Appointment</span>
                    <span>→</span>
                </button>
            </div>
        </form>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500 font-semibold space-y-1">
            <p>© 2026 Ekta Care Clinic. All rights reserved.</p>
            <p class="text-[10px] text-slate-400">If you are experiencing a life-threatening emergency, please dial 112 / 102 (Ambulance) immediately.</p>
        </div>
    </footer>
</body>
</html>
