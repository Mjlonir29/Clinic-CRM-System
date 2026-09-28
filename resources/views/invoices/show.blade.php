@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ payModalOpen: false }">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Invoice Statement</h1>
            <p class="text-xs font-semibold text-teal-600 mt-0.5">Invoice #: {{ $invoice->invoice_number }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('invoices.update-status', $invoice->id) }}" method="POST" class="inline-block">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-800 font-extrabold text-xs rounded-xl focus:outline-none focus:border-teal-700 cursor-pointer shadow-sm">
                    <option value="Paid" {{ $invoice->status === 'Paid' ? 'selected' : '' }}>Status: Paid</option>
                    <option value="Unpaid" {{ $invoice->status === 'Unpaid' ? 'selected' : '' }}>Status: Unpaid</option>
                    <option value="Partially Paid" {{ $invoice->status === 'Partially Paid' ? 'selected' : '' }}>Status: Partially Paid</option>
                    <option value="Cancelled" {{ $invoice->status === 'Cancelled' ? 'selected' : '' }}>Status: Cancelled</option>
                </select>
            </form>
            @if($invoice->due_amount > 0 && $invoice->status !== 'Cancelled')
                <button @click="payModalOpen = true" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-500/20 transition-all flex items-center">
                    💳 Record Payment
                </button>
            @endif
            <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-all inline-flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <span>Print / Download PDF</span>
            </a>
        </div>
    </div>

    <!-- Printable Invoice Statement Card -->
    <div class="bg-white p-8 sm:p-12 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
        <!-- Clinic Branding & Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b-2 border-slate-900 pb-6 gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-teal-700">{{ $settings['clinic_name'] ?? 'Metro Care Family Clinic' }}</h2>
                <p class="text-xs text-slate-500 font-medium">{{ $settings['clinic_address'] ?? '742 Evergreen Terrace, Springfield' }}</p>
                <p class="text-xs text-slate-500 font-medium">Phone: {{ $settings['clinic_phone'] ?? '+1 (555) 100-2000' }}</p>
            </div>
            <div class="sm:text-right">
                <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider
                    @if($invoice->status === 'Paid') bg-emerald-100 text-emerald-800
                    @elseif($invoice->status === 'Partially Paid') bg-amber-100 text-amber-800
                    @elseif($invoice->status === 'Cancelled') bg-rose-100 text-rose-800
                    @else bg-rose-100 text-rose-800 @endif
                ">
                    {{ $invoice->status }}
                </span>
                <h3 class="text-lg font-black text-slate-900 mt-2">{{ $invoice->invoice_number }}</h3>
                <p class="text-xs text-slate-500 font-medium">Date: {{ $invoice->invoice_date }}</p>
            </div>
        </div>

        <!-- Billed To Patient Details -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase block">Billed To</span>
                <span class="font-extrabold text-slate-900 text-sm">{{ $invoice->patient->full_name }}</span>
                <span class="text-slate-500 block">ID: {{ $invoice->patient->patient_id }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase block">Contact Phone</span>
                <span class="font-bold text-slate-800">{{ $invoice->patient->phone }}</span>
                <span class="text-slate-500 block">{{ $invoice->patient->email ?? 'N/A' }}</span>
            </div>
            <div>
                <span class="text-slate-400 font-bold uppercase block">Attending Doctor</span>
                <span class="font-bold text-slate-800">{{ $invoice->doctor->name ?? 'Dr. Robert Carter' }}</span>
            </div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
            <thead class="bg-slate-50 border-b text-slate-400 font-bold uppercase">
                <tr>
                    <th class="p-3.5">#</th>
                    <th class="p-3.5">Service Description</th>
                    <th class="p-3.5 text-center">Qty</th>
                    <th class="p-3.5 text-right">Rate</th>
                    <th class="p-3.5 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->items as $idx => $item)
                    <tr>
                        <td class="p-3.5 font-bold text-slate-400">{{ $idx + 1 }}</td>
                        <td class="p-3.5 font-extrabold text-slate-900">
                            {{ $item->service_name }}
                            @if($item->description)
                                <span class="block text-[11px] text-slate-400 font-normal">{{ $item->description }}</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center font-bold text-slate-700">{{ $item->quantity }}</td>
                        <td class="p-3.5 text-right font-semibold text-slate-700">₹{{ number_format($item->rate, 2) }}</td>
                        <td class="p-3.5 text-right font-extrabold text-slate-900">₹{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals & Balance Summary -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end pt-4 border-t border-slate-100 text-xs">
            <div class="space-y-3 w-full sm:w-1/2">
                @if(count($invoice->payments) > 0)
                    <strong class="text-slate-400 font-bold uppercase block">Payment Transactions Log</strong>
                    <div class="space-y-1.5">
                        @foreach($invoice->payments as $p)
                            <div class="p-2.5 bg-emerald-50/60 border border-emerald-100 rounded-xl flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-emerald-900">{{ $p->payment_number }} ({{ $p->payment_method }})</span>
                                    <span class="text-[10px] text-emerald-700 block">{{ $p->payment_date }} • Ref: {{ $p->transaction_reference ?: 'N/A' }}</span>
                                </div>
                                <span class="font-extrabold text-emerald-800">₹{{ number_format($p->amount, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="w-full sm:w-72 bg-slate-50 p-4 rounded-2xl border space-y-2 mt-4 sm:mt-0">
                <div class="flex justify-between font-semibold">
                    <span class="text-slate-500">Subtotal:</span>
                    <span class="text-slate-900 font-bold">₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between font-semibold text-slate-500">
                    <span>Discount:</span>
                    <span>-₹{{ number_format($invoice->discount, 2) }}</span>
                </div>
                <div class="flex justify-between font-semibold text-slate-500">
                    <span>Tax:</span>
                    <span>+₹{{ number_format($invoice->tax, 2) }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-sm pt-2 border-t text-slate-900">
                    <span>Total Amount:</span>
                    <span>₹{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-emerald-700">
                    <span>Paid Amount:</span>
                    <span>₹{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-sm pt-1 border-t text-rose-600">
                    <span>Balance Due:</span>
                    <span>₹{{ number_format($invoice->due_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Record Payment Modal -->
    <div x-show="payModalOpen" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="payModalOpen = false"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b">
                    <h3 class="text-lg font-extrabold text-slate-900">Record Payment</h3>
                    <button @click="payModalOpen = false" class="text-slate-400 font-bold">&times;</button>
                </div>

                <form method="POST" action="{{ route('invoices.payments.store', $invoice->id) }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Payment Amount (₹) *</label>
                        <input type="number" step="0.01" name="amount" value="{{ $invoice->due_amount }}" required max="{{ $invoice->due_amount }}" class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl font-extrabold text-slate-900 text-sm" />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl" />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Payment Method *</label>
                        <select name="payment_method" required class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl font-bold">
                            <option value="Cash">Cash</option>
                            <option value="Card">Credit / Debit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="UPI">UPI Payment</option>
                            <option value="Other">Other Method</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Transaction Reference / Cheque #</label>
                        <input type="text" name="transaction_reference" placeholder="e.g. TXN-98471928" class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl" />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 uppercase mb-1">Notes</label>
                        <textarea name="notes" rows="2" placeholder="Payment receipt notes..." class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl"></textarea>
                    </div>

                    <div class="pt-3 flex justify-end space-x-3">
                        <button type="button" @click="payModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl">Cancel</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 text-white font-bold rounded-xl shadow-md">Confirm Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
