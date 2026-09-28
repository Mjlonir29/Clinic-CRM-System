@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'send', recipientPhone: '', templateType: 'Appointment_Reminder', messageText: '' }">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-extrabold rounded-2xl text-xs flex items-center justify-between shadow-sm">
            <span>✓ {{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold ml-4">&times;</button>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Patient Engagement</span>
                <span>/</span>
                <span class="text-teal-800">WhatsApp & SMS Communication Hub</span>
            </div>
            <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">WhatsApp & SMS Hub</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Send instant WhatsApp messages, appointment reminders, invoice PDFs, and prescription sharing.</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 space-x-6 text-xs font-black uppercase tracking-wider">
        <button @click="tab = 'send'" :class="tab === 'send' ? 'border-b-2 border-teal-700 text-teal-800 pb-3' : 'text-slate-400 pb-3 hover:text-slate-700'">
            📲 WhatsApp Direct Dispatch
        </button>
        <button @click="tab = 'templates'" :class="tab === 'templates' ? 'border-b-2 border-teal-700 text-teal-800 pb-3' : 'text-slate-400 pb-3 hover:text-slate-700'">
            📄 Quick Message Templates
        </button>
        <button @click="tab = 'logs'" :class="tab === 'logs' ? 'border-b-2 border-teal-700 text-teal-800 pb-3' : 'text-slate-400 pb-3 hover:text-slate-700'">
            📜 Sent Communication Logs
        </button>
    </div>

    <!-- Tab 1: WhatsApp Direct Dispatch -->
    <div x-show="tab === 'send'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Dispatch Form -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-5">
            <h3 class="font-heading font-black text-slate-900 text-sm">Send WhatsApp Message / Document Share</h3>
            
            <form method="POST" action="{{ route('communication.send.whatsapp') }}" target="_blank" class="space-y-4 text-xs">
                @csrf
                
                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Select Patient *</label>
                    <select name="patient_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold" onchange="
                        let selected = this.options[this.selectedIndex];
                        if(selected.dataset.phone) {
                            document.getElementById('recipient_phone_input').value = selected.dataset.phone;
                        }
                    ">
                        <option value="">-- Choose Patient or Enter Custom Phone --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" data-phone="{{ $p->phone }}">{{ $p->full_name }} ({{ $p->phone }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Recipient Mobile Number (WhatsApp) *</label>
                    <input type="text" id="recipient_phone_input" name="phone" placeholder="e.g. +91 9876543210" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900" />
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Template / Purpose</label>
                    <select name="template_type" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold" onchange="
                        let msgInput = document.getElementById('message_content_input');
                        if (this.value === 'Appointment_Reminder') {
                            msgInput.value = 'Hello! Your consultation appointment is confirmed at Metro Care Family Clinic. Please arrive 10 minutes before your scheduled time. Location: https://maps.google.com';
                        } else if (this.value === 'Invoice_Share') {
                            msgInput.value = 'Dear Patient, your invoice statement has been issued. You can view your bill details and make payment online here: http://127.0.0.1:8000/patient/login Thank you!';
                        } else if (this.value === 'Prescription_Share') {
                            msgInput.value = 'Hello, your prescription medication advice has been generated by your doctor. Please log in to your patient portal to download your PDF prescription.';
                        } else if (this.value === 'Followup_Alert') {
                            msgInput.value = 'Reminder from Metro Care Clinic: Your follow-up health checkup is due this week. Please book your preferred slot online or reply to confirm.';
                        }
                    ">
                        <option value="Appointment_Reminder">Appointment Reminder & Location</option>
                        <option value="Invoice_Share">Digital Invoice PDF & Payment Link</option>
                        <option value="Prescription_Share">Prescription Summary & Portal Link</option>
                        <option value="Followup_Alert">Chronic Care & Post-Op Follow-up Alert</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 uppercase mb-1">Message Content *</label>
                    <textarea id="message_content_input" name="message" rows="4" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-900">Hello! Your consultation appointment is confirmed at Metro Care Family Clinic. Please arrive 10 minutes before your scheduled time. Location: https://maps.google.com</textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-md cursor-pointer transition-all flex items-center justify-center space-x-2">
                    <span>📲 Launch WhatsApp Web / App</span>
                    <span>→</span>
                </button>
            </form>
        </div>

        <!-- Quick Action Cards -->
        <div class="space-y-4">
            <!-- Quick Invoice Sharing -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-5 space-y-3">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Quick Invoice Sharing</h4>
                <div class="space-y-2">
                    @foreach($invoices->take(4) as $inv)
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 block">{{ $inv->invoice_number }}</span>
                                <span class="text-[10px] text-slate-500 font-semibold">{{ $inv->patient->full_name ?? 'Patient' }}</span>
                            </div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inv->patient->phone ?? '919876543210') }}?text={{ urlencode('Invoice ' . $inv->invoice_number . ' for ₹' . number_format($inv->total_amount, 2) . ' issued by Metro Care Clinic. View details: http://127.0.0.1:8000/invoices/' . $inv->id) }}" target="_blank" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg">
                                Share WhatsApp
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Prescription Sharing -->
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-5 space-y-3">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider">Quick Prescription Sharing</h4>
                <div class="space-y-2">
                    @foreach($prescriptions->take(4) as $rx)
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 block">Rx #{{ $rx->id }} - {{ $rx->diagnosis ?? 'Prescription' }}</span>
                                <span class="text-[10px] text-slate-500 font-semibold">{{ $rx->patient->full_name ?? 'Patient' }}</span>
                            </div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $rx->patient->phone ?? '919876543210') }}?text={{ urlencode('Your prescription for ' . ($rx->diagnosis ?: 'consultation') . ' is ready. Download PDF: http://127.0.0.1:8000/prescriptions/' . $rx->id) }}" target="_blank" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg">
                                Share Rx
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Message Templates -->
    <div x-show="tab === 'templates'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card space-y-2">
            <span class="px-2.5 py-0.5 bg-teal-50 text-teal-800 text-[10px] font-black rounded uppercase">Template 1</span>
            <h4 class="font-heading font-black text-slate-900 text-sm">Appointment Reminder & Directions</h4>
            <p class="text-xs text-slate-600 font-medium bg-slate-50 p-3 rounded-2xl border border-slate-100">
                "Hello {Patient Name}! Your consultation appointment with Dr. {Doctor Name} is confirmed for {Date} at {Time}. Clinic Location: https://maps.google.com. Reply CANCEL if unable to attend."
            </p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card space-y-2">
            <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 text-[10px] font-black rounded uppercase">Template 2</span>
            <h4 class="font-heading font-black text-slate-900 text-sm">Invoice PDF & Payment Link</h4>
            <p class="text-xs text-slate-600 font-medium bg-slate-50 p-3 rounded-2xl border border-slate-100">
                "Dear {Patient Name}, your invoice #{Invoice Number} for ₹{Total Amount} has been generated by Metro Care Clinic. Pay online or view bill details: {Link}"
            </p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card space-y-2">
            <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-800 text-[10px] font-black rounded uppercase">Template 3</span>
            <h4 class="font-heading font-black text-slate-900 text-sm">Prescription & Medication Guide</h4>
            <p class="text-xs text-slate-600 font-medium bg-slate-50 p-3 rounded-2xl border border-slate-100">
                "Dear {Patient Name}, your prescription for {Diagnosis} has been updated. Access your full medication schedule and dosage instructions here: {Portal Link}"
            </p>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card space-y-2">
            <span class="px-2.5 py-0.5 bg-amber-50 text-amber-800 text-[10px] font-black rounded uppercase">Template 4</span>
            <h4 class="font-heading font-black text-slate-900 text-sm">Chronic Care & Follow-up Alert</h4>
            <p class="text-xs text-slate-600 font-medium bg-slate-50 p-3 rounded-2xl border border-slate-100">
                "Metro Care Health Notice: It is time for your periodic checkup ({Condition}). Please schedule your follow-up appointment online at http://127.0.0.1:8000/patient/book"
            </p>
        </div>
    </div>

    <!-- Tab 3: Sent Communication Logs -->
    <div x-show="tab === 'logs'" class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 space-y-4">
        <h3 class="font-heading font-black text-slate-900 text-sm">Sent Communication History Log</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                        <th class="p-3.5">Timestamp</th>
                        <th class="p-3.5">Recipient Phone</th>
                        <th class="p-3.5">Patient</th>
                        <th class="p-3.5">Channel</th>
                        <th class="p-3.5">Template Type</th>
                        <th class="p-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($logs as $log)
                        <tr>
                            <td class="p-3.5 font-bold text-slate-900">{{ $log->created_at }}</td>
                            <td class="p-3.5 font-mono text-teal-800 font-bold">{{ $log->recipient_phone }}</td>
                            <td class="p-3.5 font-bold text-slate-900">{{ $log->patient->full_name ?? 'N/A' }}</td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    {{ $log->channel }}
                                </span>
                            </td>
                            <td class="p-3.5 font-semibold text-slate-700">{{ str_replace('_', ' ', $log->template_type) }}</td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                                    {{ $log->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400 font-medium">No communication logs recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-2 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
