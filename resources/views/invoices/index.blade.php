@extends('layouts.app')

@section('content')
@php
    $invoicesFormatted = $invoices->map(function($inv) {
        return [
            'id' => $inv->id,
            'number' => $inv->invoice_number,
            'date' => $inv->invoice_date,
            'due_date' => $inv->due_date,
            'status' => $inv->status,
            'patient_id' => $inv->patient_id,
            'patient_name' => $inv->patient ? $inv->patient->full_name : 'Patient Record',
            'patient_mrn' => $inv->patient ? $inv->patient->patient_id : 'PAT-000',
            'patient_phone' => $inv->patient ? ($inv->patient->phone ?? '') : '',
            'subtotal' => (float) $inv->subtotal,
            'tax' => (float) $inv->tax,
            'discount' => (float) $inv->discount,
            'total_amount' => (float) $inv->total_amount,
            'paid_amount' => (float) $inv->paid_amount,
            'due_amount' => (float) $inv->due_amount,
            'items' => $inv->items ? $inv->items->map(function($i) {
                return [
                    'id' => $i->id,
                    'service_name' => $i->service_name,
                    'quantity' => (int) $i->quantity,
                    'rate' => (float) $i->rate,
                    'total' => (float) $i->total,
                ];
            })->values()->toArray() : [],
        ];
    })->values()->toArray();

    $firstInvData = count($invoicesFormatted) > 0 ? $invoicesFormatted[0] : null;
@endphp

<script>
    function initInvoiceLedger() {
        return {
            tab: 'all',
            paymentMethod: 'UPI',
            invoicesList: @json($invoicesFormatted),
            selectedInvoice: @json($firstInvData),
            selectInv(id) {
                let found = this.invoicesList.find(i => i.id === id);
                if (found) {
                    this.selectedInvoice = found;
                }
            }
        };
    }
</script>

<div class="space-y-6" x-data="initInvoiceLedger()">

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 font-extrabold rounded-2xl text-xs flex items-center justify-between shadow-sm">
            <span>✓ {{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold ml-4">&times;</button>
        </div>
    @endif

    <!-- Header Breadcrumbs & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">
                <span>Clinical Operations</span>
                <span>/</span>
                <span class="text-teal-800">Financial Ledger & Billing</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight">Billing & Invoice</h1>
                <span class="px-2.5 py-0.5 text-[10px] font-extrabold bg-teal-50 text-teal-800 border border-teal-200/60 rounded-md uppercase">Batch #REC-{{ date('Y-M') }}</span>
            </div>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Patient account statements, billing ledger, and cash-flow reporting in Indian Rupees (₹).</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('reports.export', ['type' => 'financial']) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center border border-slate-200">
                Export CSV / Ledger
            </a>
            <a href="{{ route('reports.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center border border-slate-200">
                Tax Summary
            </a>
            <a href="{{ route('invoices.create') }}" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md shadow-teal-900/20 transition-all flex items-center transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                </svg>
                Create New Invoice
            </a>
        </div>
    </div>

    <!-- 4 Dynamic Financial Stat Cards Grid (Values in ₹) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Invoiced -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Invoiced (This Month)</span>
                <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">📄</span>
            </div>
            <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">₹{{ number_format($thisMonthInvoiced, 2) }}</p>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-3 pt-2 border-t border-slate-100">
                <span class="text-emerald-700 font-extrabold">📈 Active Period</span>
                <span>{{ $thisMonthInvoicedCount }} invoices issued</span>
            </div>
        </div>

        <!-- Received Revenue -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Received Revenue</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">✓</span>
            </div>
            <p class="text-3xl font-heading font-black text-teal-800 tracking-tight">₹{{ number_format($receivedRevenue, 2) }}</p>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-3 pt-2 border-t border-slate-100">
                <span class="text-emerald-700 font-extrabold">
                    {{ $thisMonthInvoiced > 0 ? number_format(($receivedRevenue / max($thisMonthInvoiced, 1)) * 100, 1) : '100' }}% collected
                </span>
                <span>₹{{ number_format($cardUpiPayments, 0) }} UPI/Card • ₹{{ number_format($cashPayments, 0) }} Cash</span>
            </div>
        </div>

        <!-- Pending / Due -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Pending / Due</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs">⏳</span>
            </div>
            <p class="text-3xl font-heading font-black text-slate-900 tracking-tight">₹{{ number_format($pendingDue, 2) }}</p>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-3 pt-2 border-t border-slate-100">
                <span class="text-amber-700 font-extrabold">{{ $pendingInvoicesCount }} invoices pending</span>
                <span>Standard Net 14</span>
            </div>
        </div>

        <!-- Overdue Invoices -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-card border-l-4 border-l-rose-500">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[10px] font-black text-rose-800 uppercase tracking-wider">Overdue Invoices</span>
                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center font-bold text-xs">⚠️</span>
            </div>
            <p class="text-3xl font-heading font-black text-rose-600 tracking-tight">{{ $overdueInvoicesCount }} <span class="text-xs font-bold text-slate-500">(₹{{ number_format($overdueAmount, 0) }})</span></p>
            <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mt-3 pt-2 border-t border-slate-100">
                <span class="text-rose-700 font-extrabold">Overdue Notice</span>
                <span class="text-rose-700">Auto-alerts active</span>
            </div>
        </div>

    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-card space-y-4">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="relative w-full sm:w-96">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search by invoice #, patient name, phone..." 
                    class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-medium"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <div class="flex items-center space-x-3">
                <select name="status" onchange="this.form.submit()" class="px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-teal-700 font-bold">
                    <option value="">All Invoices Status</option>
                    <option value="Paid" {{ request('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                    <option value="Unpaid" {{ request('status') === 'Unpaid' ? 'selected' : '' }}>Unpaid / Due</option>
                    <option value="Partially Paid" {{ request('status') === 'Partially Paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-4 py-2.5 bg-slate-900 text-white font-bold text-xs rounded-xl hover:bg-slate-800 transition-all">Filter</button>
            </div>
        </form>
    </div>

    <!-- Main Invoices Ledger Grid (Left 2 Cols) & Quick View Pane (Right 1 Col) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Invoices List Table -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 shadow-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-5">Invoice #</th>
                            <th class="py-3.5 px-5">Patient Details</th>
                            <th class="py-3.5 px-5">Date / Term</th>
                            <th class="py-3.5 px-5">Service Items</th>
                            <th class="py-3.5 px-5 text-right">Amount (₹) & Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($invoices as $inv)
                            <tr 
                                @click="selectInv({{ $inv->id }})"
                                class="hover:bg-teal-50/40 transition-colors cursor-pointer"
                                :class="selectedInvoice && selectedInvoice.id === {{ $inv->id }} ? 'bg-teal-50/60 font-bold border-l-4 border-l-teal-700' : ''"
                            >
                                <td class="py-4 px-5">
                                    <span class="font-extrabold text-teal-800 bg-teal-50 border border-teal-200/60 px-2 py-0.5 rounded">
                                        {{ $inv->invoice_number }}
                                    </span>
                                </td>

                                <td class="py-4 px-5">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-teal-800 text-white font-extrabold flex items-center justify-center text-xs flex-shrink-0">
                                            {{ strtoupper(substr($inv->patient->first_name ?? 'P', 0, 1) . substr($inv->patient->last_name ?? '', 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('patients.show', $inv->patient_id) }}" class="font-heading font-black text-slate-900 hover:text-teal-800 block">
                                                {{ $inv->patient->full_name ?? 'Patient' }}
                                            </a>
                                            <span class="text-[10px] text-slate-400 font-semibold">{{ $inv->patient->patient_id ?? '' }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-5">
                                    <span class="font-bold text-slate-900 block">{{ $inv->invoice_date }}</span>
                                    <span class="text-[10px] text-slate-400">Due: {{ $inv->due_date }}</span>
                                </td>

                                <td class="py-4 px-5">
                                    <span class="font-semibold text-slate-800 block">
                                        {{ count($inv->items) > 0 ? $inv->items->first()->service_name : 'Consultation Fee' }}
                                    </span>
                                    <span class="text-[10px] text-teal-700 font-bold block">{{ count($inv->items) }} billable line items</span>
                                </td>

                                <td class="py-4 px-5 text-right">
                                    <div class="font-heading font-black text-slate-900 text-sm">₹{{ number_format($inv->total_amount, 2) }}</div>
                                    
                                    <!-- Interactive Clickable Status Badge with Dropdown -->
                                    <div class="relative inline-block text-left mt-1" x-data="{ menuOpen: false }">
                                        <button 
                                            type="button" 
                                            @click.stop="menuOpen = !menuOpen" 
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider cursor-pointer hover:shadow-sm transition-all border
                                                @if($inv->status === 'Paid') bg-emerald-100 text-emerald-800 border-emerald-300 hover:bg-emerald-200
                                                @elseif($inv->status === 'Partially Paid') bg-amber-100 text-amber-800 border-amber-300 hover:bg-amber-200
                                                @elseif($inv->status === 'Cancelled') bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200
                                                @else bg-rose-100 text-rose-800 border-rose-300 hover:bg-rose-200 @endif
                                            "
                                            title="Click to update status"
                                        >
                                            <span>{{ $inv->status }}</span>
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <div 
                                            x-show="menuOpen" 
                                            @click.away="menuOpen = false" 
                                            x-cloak
                                            class="origin-top-right absolute right-0 mt-1 w-36 rounded-xl bg-white shadow-xl ring-1 ring-black/5 border border-slate-200 z-30 py-1 font-semibold text-xs"
                                        >
                                            <div class="px-3 py-1 text-[9px] font-black text-slate-400 uppercase border-b border-slate-100">Update Status</div>
                                            
                                            <form method="POST" action="{{ route('invoices.update-status', $inv->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Paid">
                                                <button type="submit" class="w-full text-left px-3 py-1.5 hover:bg-emerald-50 text-emerald-800 flex items-center justify-between">
                                                    <span>Paid</span>
                                                    @if($inv->status === 'Paid') ✓ @endif
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('invoices.update-status', $inv->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Unpaid">
                                                <button type="submit" class="w-full text-left px-3 py-1.5 hover:bg-rose-50 text-rose-800 flex items-center justify-between">
                                                    <span>Unpaid</span>
                                                    @if($inv->status === 'Unpaid') ✓ @endif
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('invoices.update-status', $inv->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Partially Paid">
                                                <button type="submit" class="w-full text-left px-3 py-1.5 hover:bg-amber-50 text-amber-800 flex items-center justify-between">
                                                    <span>Partially Paid</span>
                                                    @if($inv->status === 'Partially Paid') ✓ @endif
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('invoices.update-status', $inv->id) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Cancelled">
                                                <button type="submit" class="w-full text-left px-3 py-1.5 hover:bg-slate-100 text-slate-700 flex items-center justify-between">
                                                    <span>Cancelled</span>
                                                    @if($inv->status === 'Cancelled') ✓ @endif
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 font-medium">No invoice records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $invoices->links() }}
            </div>
        </div>

        <!-- Right Quick View & Record Payment Pane -->
        <div class="space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-card p-6 sticky top-20 space-y-6">
                
                <!-- Card Header with Active Invoice Badge & Status Switcher -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider block">Invoice Details & Quick Pay</span>
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-black bg-teal-50 text-teal-800 border border-teal-200/80 inline-block mt-0.5" x-text="selectedInvoice ? selectedInvoice.number : 'Select Invoice'"></span>
                    </div>

                    <!-- Quick Status Selector Dropdown -->
                    <template x-if="selectedInvoice && selectedInvoice.id > 0">
                        <form :action="'/invoices/' + selectedInvoice.id + '/status'" method="POST" class="flex items-center space-x-1">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="px-2.5 py-1 text-[11px] font-black rounded-lg border border-slate-200 bg-slate-50 text-slate-800 focus:outline-none focus:border-teal-700 shadow-sm cursor-pointer hover:bg-slate-100 transition-all">
                                <option value="Paid" :selected="selectedInvoice.status === 'Paid'">Status: Paid</option>
                                <option value="Unpaid" :selected="selectedInvoice.status === 'Unpaid'">Status: Unpaid</option>
                                <option value="Partially Paid" :selected="selectedInvoice.status === 'Partially Paid'">Status: Partially Paid</option>
                                <option value="Cancelled" :selected="selectedInvoice.status === 'Cancelled'">Status: Cancelled</option>
                            </select>
                        </form>
                    </template>
                </div>

                <!-- Main Content Pane when an invoice is selected -->
                <div x-show="selectedInvoice && selectedInvoice.id > 0" class="space-y-5">
                    
                    <!-- Patient Profile Header Box -->
                    <div class="p-4 rounded-2xl bg-teal-50/40 border border-teal-100 flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-800 text-white font-black flex items-center justify-center text-sm shrink-0 shadow-sm">
                            <span x-text="(selectedInvoice && selectedInvoice.patient_name ? selectedInvoice.patient_name : 'P').trim().charAt(0).toUpperCase()"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-heading font-black text-slate-900 text-sm truncate" x-text="selectedInvoice ? selectedInvoice.patient_name : 'Patient'"></h3>
                            <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500">
                                <span x-text="selectedInvoice ? (selectedInvoice.patient_mrn || 'PAT-000') : ''"></span>
                                <span x-show="selectedInvoice && selectedInvoice.patient_phone">•</span>
                                <span x-text="selectedInvoice ? (selectedInvoice.patient_phone || '') : ''"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Itemized Clinical Services Table -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Itemized Clinical Services</h4>
                            <span class="text-[10px] font-extrabold text-teal-800" x-text="(selectedInvoice && selectedInvoice.items ? selectedInvoice.items.length : 0) + ' Line Items'"></span>
                        </div>

                        <div class="divide-y divide-slate-100 border border-slate-200/80 rounded-2xl p-3 bg-slate-50/50 space-y-2">
                            <template x-for="item in (selectedInvoice ? selectedInvoice.items : [])" :key="item.id || item.service_name">
                                <div class="flex items-center justify-between pt-2 text-xs">
                                    <div class="pr-2">
                                        <span class="font-bold text-slate-900 block" x-text="item.service_name"></span>
                                        <span class="text-[10px] text-slate-400 font-semibold block" x-text="item.quantity + ' x ₹' + Number(item.rate).toFixed(2)"></span>
                                    </div>
                                    <span class="font-heading font-black text-slate-900 shrink-0" x-text="'₹' + Number(item.total).toFixed(2)"></span>
                                </div>
                            </template>
                            <template x-if="!selectedInvoice || !selectedInvoice.items || selectedInvoice.items.length === 0">
                                <div class="text-xs text-slate-500 font-medium py-2 text-center">Standard OPD Clinical Consultation</div>
                            </template>
                        </div>
                    </div>

                    <!-- Invoice Financial Total Breakdown -->
                    <div class="space-y-2 pt-2 border-t border-slate-100 text-xs">
                        <div class="flex justify-between text-slate-500 font-medium">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-900" x-text="'₹' + Number(selectedInvoice ? selectedInvoice.subtotal : 0).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-slate-500 font-medium">
                            <span>Tax (GST)</span>
                            <span class="font-bold text-slate-900" x-text="'₹' + Number(selectedInvoice ? selectedInvoice.tax : 0).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-slate-500 font-medium" x-show="selectedInvoice && selectedInvoice.discount > 0">
                            <span>Discount</span>
                            <span class="font-bold text-emerald-700" x-text="'-₹' + Number(selectedInvoice ? selectedInvoice.discount : 0).toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between text-sm font-heading font-black text-slate-900 pt-2 border-t border-slate-100">
                            <span>Total Payable</span>
                            <span class="text-teal-800" x-text="'₹' + Number(selectedInvoice ? selectedInvoice.total_amount : 0).toFixed(2)"></span>
                        </div>

                        <div class="flex justify-between text-xs font-extrabold text-emerald-700 pt-1">
                            <span>Amount Paid</span>
                            <span x-text="'₹' + Number(selectedInvoice ? selectedInvoice.paid_amount : 0).toFixed(2)"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200/80 flex justify-between items-center text-xs font-black text-rose-800 mt-2" x-show="selectedInvoice && selectedInvoice.due_amount > 0">
                            <span>Remaining Balance Due:</span>
                            <span class="text-sm font-heading" x-text="'₹' + Number(selectedInvoice ? selectedInvoice.due_amount : 0).toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- Quick Record Payment Form -->
                    <template x-if="selectedInvoice && selectedInvoice.due_amount > 0 && selectedInvoice.status !== 'Cancelled'">
                        <form :action="'/invoices/' + selectedInvoice.id + '/payments'" method="POST" class="space-y-3 pt-3 border-t border-slate-100">
                            @csrf
                            <input type="hidden" name="payment_date" value="{{ date('Y-m-d') }}">
                            
                            <div>
                                <label class="block text-[10px] font-black text-slate-700 uppercase tracking-wider mb-1">Record Payment Amount (₹)</label>
                                <input type="number" step="0.01" name="amount" :value="selectedInvoice.due_amount" required class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 focus:border-teal-700 focus:outline-none" />
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-slate-700 uppercase tracking-wider mb-1">Payment Method</label>
                                <select name="payment_method" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 focus:border-teal-700 focus:outline-none cursor-pointer">
                                    <option value="UPI">UPI / Google Pay / PhonePe</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Credit / Debit Card</option>
                                    <option value="Net Banking">Net Banking</option>
                                </select>
                            </div>

                            <button type="submit" class="w-full py-3 bg-teal-800 hover:bg-teal-900 text-white font-extrabold text-xs rounded-xl shadow-md shadow-teal-900/20 cursor-pointer transition-all flex items-center justify-center space-x-1">
                                <span>Record Payment Receipt</span>
                                <span>→</span>
                            </button>
                        </form>
                    </template>

                    <!-- Links Footer -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold">
                        <a :href="'/invoices/' + selectedInvoice.id + '/print'" target="_blank" class="text-teal-800 hover:underline flex items-center gap-1.5">
                            <span>🖨️ Print PDF Receipt</span>
                        </a>
                        <a :href="'/invoices/' + selectedInvoice.id" class="text-slate-600 hover:text-slate-900">View Full Statement →</a>
                    </div>
                </div>

                <!-- Fallback empty state when no invoice selected -->
                <div x-show="!selectedInvoice || selectedInvoice.id === 0" class="py-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 font-bold text-xl flex items-center justify-center mx-auto">📄</div>
                    <p class="text-slate-500 font-semibold text-xs">Select any invoice row from the ledger table on the left to view itemized breakdown & record payment.</p>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection
