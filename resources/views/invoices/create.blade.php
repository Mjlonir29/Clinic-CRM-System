@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="invoiceForm()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Create Invoice Statement</h1>
            <p class="text-xs font-semibold text-slate-500 mt-1">Generate itemized billing for consultation, procedures & medications</p>
        </div>
        <a href="{{ route('invoices.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 px-3.5 py-2 rounded-xl">
            ← Invoices Directory
        </a>
    </div>

    <form method="POST" action="{{ route('invoices.store') }}" class="space-y-6">
        @csrf
        @if($appointmentId)
            <input type="hidden" name="appointment_id" value="{{ $appointmentId }}">
        @endif

        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <h3 class="text-sm font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center">
                <span class="w-6 h-6 rounded-md bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs mr-2">💵</span>
                Invoice Header & Dates
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Select Patient *</label>
                    <select name="patient_id" required class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:border-teal-500">
                        <option value="">-- Choose Patient --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ $selectedPatientId == $p->id ? 'selected' : '' }}>
                                {{ $p->full_name }} ({{ $p->patient_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Due Date</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d') }}" class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs" />
                </div>
            </div>
        </div>

        <!-- Line Items Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900 flex items-center">
                    <span class="w-6 h-6 rounded-md bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs mr-2">📋</span>
                    Service / Billing Items
                </h3>
                <button type="button" @click="addItem()" class="px-3.5 py-1.5 bg-teal-50 text-teal-700 font-bold text-xs rounded-xl border border-teal-200 hover:bg-teal-100">
                    + Add Line Item
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/60 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
                        <div class="sm:col-span-5">
                            <label class="block font-bold text-slate-700 mb-1">Service / Description *</label>
                            <input type="text" :name="'items['+idx+'][service_name]'" x-model="item.service_name" required placeholder="e.g. Cardiology Consultation" class="w-full px-3 py-2 bg-white border rounded-xl" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1">Qty</label>
                            <input type="number" min="1" :name="'items['+idx+'][quantity]'" x-model.number="item.quantity" required class="w-full px-3 py-2 bg-white border rounded-xl" />
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block font-bold text-slate-700 mb-1">Rate (₹)</label>
                            <input type="number" step="0.01" min="0" :name="'items['+idx+'][rate]'" x-model.number="item.rate" required class="w-full px-3 py-2 bg-white border rounded-xl" />
                        </div>
                        <div class="sm:col-span-2 text-right pt-4">
                            <span class="font-extrabold text-slate-900 text-sm" x-text="'₹' + (item.quantity * item.rate).toFixed(2)"></span>
                            <button type="button" @click="removeItem(idx)" class="block text-[11px] font-bold text-rose-600 hover:underline mt-1 ml-auto" x-show="items.length > 1">
                                Remove
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Totals Summary Grid -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs">
                <div class="w-full sm:w-1/2 space-y-2">
                    <label class="block font-bold text-slate-700 uppercase">Payment & Billing Notes</label>
                    <textarea name="notes" rows="2" placeholder="Payment terms or instructions..." class="w-full px-3 py-2 bg-slate-50 border rounded-xl"></textarea>
                </div>
                <div class="w-full sm:w-80 bg-slate-50 p-4 rounded-2xl border space-y-2">
                    <div class="flex justify-between font-semibold">
                        <span class="text-slate-500">Subtotal:</span>
                        <span class="text-slate-900 font-bold" x-text="'₹' + calculateSubtotal().toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-semibold">Discount (₹):</span>
                        <input type="number" step="0.01" min="0" name="discount" x-model.number="discount" class="w-24 px-2 py-1 bg-white border rounded-lg text-right text-xs" />
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 font-semibold">Tax (₹):</span>
                        <input type="number" step="0.01" min="0" name="tax" x-model.number="tax" class="w-24 px-2 py-1 bg-white border rounded-lg text-right text-xs" />
                    </div>
                    <div class="flex justify-between font-extrabold text-base pt-2 border-t text-teal-800">
                        <span>Grand Total:</span>
                        <span x-text="'₹' + calculateGrandTotal().toFixed(2)"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('invoices.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white font-bold text-xs rounded-xl shadow-md shadow-teal-500/20">Generate Invoice</button>
        </div>
    </form>
</div>

<script>
    function invoiceForm() {
        return {
            discount: 0,
            tax: 0,
            items: [
                { service_name: 'Medical Consultation Fee', quantity: 1, rate: {{ (float) $defaultConsultationFee }} }
            ],
            addItem() {
                this.items.push({ service_name: '', quantity: 1, rate: 0 });
            },
            removeItem(idx) {
                if (this.items.length > 1) {
                    this.items.splice(idx, 1);
                }
            },
            calculateSubtotal() {
                return this.items.reduce((sum, item) => sum + (item.quantity * item.rate), 0);
            },
            calculateGrandTotal() {
                const sub = this.calculateSubtotal();
                return Math.max(0, sub - (this.discount || 0) + (this.tax || 0));
            }
        }
    }
</script>
@endsection
