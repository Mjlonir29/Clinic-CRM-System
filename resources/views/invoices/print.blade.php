<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @media print {
            body { background: white; -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-slate-100 font-['Plus_Jakarta_Sans'] p-6 min-h-screen">
    
    <div class="no-print max-w-3xl mx-auto mb-4 flex justify-between items-center">
        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-xs font-bold text-slate-600 hover:underline">← Back to Invoice</a>
        <button onclick="window.print()" class="px-5 py-2 bg-teal-600 text-white font-bold text-xs rounded-xl shadow">
            🖨️ Print Invoice Statement
        </button>
    </div>

    <div class="max-w-3xl mx-auto bg-white p-10 rounded-2xl shadow-lg border space-y-6">
        <!-- Clinic Branding Header -->
        <div class="flex justify-between border-b-2 border-slate-900 pb-6">
            <div>
                <h1 class="text-2xl font-black text-teal-700">{{ $settings['clinic_name'] ?? 'Metro Care Family Clinic' }}</h1>
                <p class="text-xs text-slate-500 font-medium">{{ $settings['clinic_address'] ?? '742 Evergreen Terrace, Springfield, IL' }}</p>
                <p class="text-xs text-slate-500 font-medium">Ph: {{ $settings['clinic_phone'] ?? '+1 (555) 100-2000' }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-slate-100 text-slate-800">
                    {{ $invoice->status }}
                </span>
                <h2 class="text-xl font-black text-slate-900 mt-2">{{ $invoice->invoice_number }}</h2>
                <p class="text-xs text-slate-500 font-medium">Date: {{ $invoice->invoice_date }}</p>
            </div>
        </div>

        <!-- Demographics -->
        <div class="grid grid-cols-3 gap-4 text-xs bg-slate-50 p-4 rounded-xl border">
            <div>
                <span class="text-slate-400 font-bold uppercase block">Billed Patient</span>
                <span class="font-extrabold text-slate-900 text-sm">{{ $invoice->patient->full_name }}</span>
                <span class="text-slate-500 block">ID: {{ $invoice->patient->patient_id }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase block">Phone / Email</span>
                <span class="font-bold text-slate-800">{{ $invoice->patient->phone }}</span>
                <span class="text-slate-500 block">{{ $invoice->patient->email ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase block">Attending Physician</span>
                <span class="font-bold text-slate-800">{{ $invoice->doctor->name ?? 'Dr. Robert Carter' }}</span>
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-left text-xs border border-slate-200 rounded-lg overflow-hidden">
            <thead class="bg-slate-100 text-slate-500 font-bold uppercase">
                <tr>
                    <th class="p-3">#</th>
                    <th class="p-3">Service</th>
                    <th class="p-3 text-center">Qty</th>
                    <th class="p-3 text-right">Rate</th>
                    <th class="p-3 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->items as $idx => $item)
                    <tr>
                        <td class="p-3 font-bold text-slate-400">{{ $idx + 1 }}</td>
                        <td class="p-3 font-extrabold text-slate-900">{{ $item->service_name }}</td>
                        <td class="p-3 text-center font-bold">{{ $item->quantity }}</td>
                        <td class="p-3 text-right">₹{{ number_format($item->rate, 2) }}</td>
                        <td class="p-3 text-right font-bold">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="flex justify-end text-xs">
            <div class="w-64 bg-slate-50 p-4 rounded-xl border space-y-2">
                <div class="flex justify-between font-semibold">
                    <span>Subtotal:</span>
                    <span>₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between font-semibold">
                    <span>Discount:</span>
                    <span>-₹{{ number_format($invoice->discount, 2) }}</span>
                </div>
                <div class="flex justify-between font-semibold">
                    <span>Tax:</span>
                    <span>+₹{{ number_format($invoice->tax, 2) }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-sm pt-2 border-t text-slate-900">
                    <span>Grand Total:</span>
                    <span>₹{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-emerald-700">
                    <span>Paid:</span>
                    <span>₹{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-rose-600 border-t pt-1">
                    <span>Due Amount:</span>
                    <span>₹{{ number_format($invoice->due_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="pt-8 border-t text-[11px] text-slate-400 text-center">
            <p>{{ $settings['invoice_footer'] ?? 'Thank you for choosing Metro Care Clinic.' }}</p>
        </div>
    </div>
</body>
</html>
