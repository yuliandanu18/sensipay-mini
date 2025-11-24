@extends('sensipay.layout')

@section('title', 'Edit Invoice - Sensipay')

@section('content')
@php
    $hasPayment = ($invoice->paid_amount ?? 0) > 0;
    $remaining  = $invoice->remaining ?? max(0, ($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0));
@endphp

<h1 class="text-xl font-semibold mb-4">
    Edit Invoice {{ $invoice->invoice_code }}
</h1>

{{-- ALERT SESSION --}}
@if(session('error'))
    <div class="mb-3 rounded-md bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-800">
        {{ session('error') }}
    </div>
@endif

@if(session('warning'))
    <div class="mb-3 rounded-md bg-amber-50 border border-amber-200 px-3 py-2 text-xs text-amber-800">
        {{ session('warning') }}
    </div>
@endif

@if(session('success'))
    <div class="mb-3 rounded-md bg-emerald-50 border border-emerald-200 px-3 py-2 text-xs text-emerald-800">
        {{ session('success') }}
    </div>
@endif

{{-- INFO STATUS LUNAS --}}
@if($invoice->is_paid)
    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs text-emerald-800">
        <div class="font-semibold text-[13px]">
            ✅ Invoice ini sudah LUNAS.
        </div>
        <p class="mt-1">
            Data dikunci agar riwayat pembayaran tetap rapi. Jika ada ketidaksesuaian status,
            gunakan tombol <span class="font-semibold">Recalc Status</span> untuk sinkron ulang
            dengan nilai pembayaran.
        </p>
    </div>
@endif

<form method="post"
      action="{{ route('sensipay.invoices.update', $invoice) }}"
      class="bg-white rounded-xl shadow-sm p-4 space-y-4">
    @csrf
    @method('put')

    <div class="text-xs text-slate-500 mb-2">
        Dibuat: {{ $invoice->created_at?->format('d/m/Y H:i') }}
    </div>

    {{-- INFO TOTAL / TERBAYAR / SISA --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs mb-2">
        <div class="rounded-lg bg-slate-50 px-3 py-2">
            <div class="text-slate-500">Total Tagihan</div>
            <div class="font-semibold text-slate-800">
                Rp {{ number_format($invoice->total_amount ?? 0, 0, ',', '.') }}
            </div>
        </div>
        <div class="rounded-lg bg-slate-50 px-3 py-2">
            <div class="text-slate-500">Sudah Dibayar</div>
            <div class="font-semibold text-emerald-700">
                Rp {{ number_format($invoice->paid_amount ?? 0, 0, ',', '.') }}
            </div>
        </div>
        <div class="rounded-lg bg-slate-50 px-3 py-2">
            <div class="text-slate-500">Sisa Tagihan</div>
            <div class="font-semibold text-red-600">
                Rp {{ number_format($remaining, 0, ',', '.') }}
            </div>
        </div>
    </div>

    @if($hasPayment)
        <div class="mb-2 rounded-md bg-amber-50 border border-amber-200 px-3 py-2 text-[11px] text-amber-800">
            Invoice ini sudah punya pembayaran.
            Mengubah <span class="font-semibold">Total Tagihan</span> akan mempengaruhi sisa yang harus dibayar orang tua.
        </div>
    @endif

    {{-- Kalau sudah paid, kita tetap tampilkan form tapi field penting bisa dibiarkan aktif/atau tidak.
         Karena controller sudah blokir update, perubahan tidak akan disimpan. --}}
    
    <div>
        <label class="block text-sm font-medium mb-1">Siswa</label>
        <select name="student_id"
                class="w-full rounded-md border-slate-300 text-sm"
                @if($invoice->is_paid) disabled @endif>
            @foreach($students as $student)
                <option value="{{ $student->id }}" @selected(old('student_id', $invoice->student_id) == $student->id)>
                    {{ $student->name }} - {{ $student->grade }} ({{ $student->school }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Program</label>
        <select name="program_id"
                class="w-full rounded-md border-slate-300 text-sm"
                @if($invoice->is_paid) disabled @endif>
            <option value="">-- Tidak ada / Custom --</option>
            @foreach($programs as $program)
                <option value="{{ $program->id }}" @selected(old('program_id', $invoice->program_id) == $program->id)>
                    {{ $program->name }} ({{ $program->level }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Total Tagihan (Rp)</label>
            <input type="number"
                   name="total_amount"
                   id="total_amount"
                   value="{{ old('total_amount', $invoice->total_amount) }}"
                   class="w-full rounded-md border-slate-300 text-sm"
                   min="0"
                   @if($invoice->is_paid) disabled @endif>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Terbayar (Rp)</label>
            <input type="number"
                   value="{{ $invoice->paid_amount }}"
                   disabled
                   class="w-full rounded-md border-slate-300 text-sm bg-slate-50">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Sisa (otomatis)</label>
            <input type="text"
                   id="remaining_display"
                   value="{{ number_format($remaining, 0, ',', '.') }}"
                   class="w-full rounded-md border-slate-200 text-sm bg-slate-50"
                   readonly>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <select name="status"
                    class="w-full rounded-md border-slate-300 text-sm"
                    @if($invoice->is_paid) disabled @endif>
                @foreach(['unpaid' => 'Belum Lunas', 'partial' => 'Cicilan', 'paid' => 'Lunas'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('status', $invoice->status) == $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Jatuh Tempo</label>
        <input type="date"
               name="due_date"
               value="{{ old('due_date', optional($invoice->due_date)->format('Y-m-d')) }}"
               class="w-full rounded-md border-slate-300 text-sm">
    </div>

    <div class="flex justify-between items-center mt-2">
        {{-- Tombol Hapus --}}
        <button formaction="{{ route('sensipay.invoices.destroy', $invoice) }}"
                formmethod="post"
                onclick="return confirm('Hapus invoice ini?')"
                class="px-3 py-2 rounded-md border border-red-200 text-red-700 text-xs">
            @csrf
            @method('delete')
            Hapus
        </button>

        <div class="flex gap-2 items-center">
            {{-- Tombol Recalc Status --}}
            <form action="{{ route('sensipay.invoices.recalc-status', $invoice) }}"
                  method="post"
                  onsubmit="return confirm('Hitung ulang status berdasarkan pembayaran?')">
                @csrf
                <button type="submit"
                        class="px-3 py-2 rounded-md border border-slate-300 text-xs text-slate-700 hover:bg-slate-50">
                    Recalc Status
                </button>
            </form>

            <a href="{{ route('sensipay.invoices.show', $invoice) }}"
               class="px-3 py-2 rounded-md border text-sm">
                Batal
            </a>

            <button
                @if($invoice->is_paid) disabled class="px-3 py-2 rounded-md bg-slate-300 text-white text-sm cursor-not-allowed"
                @else class="px-3 py-2 rounded-md bg-sky-600 text-white text-sm hover:bg-sky-700"
                @endif
            >
                Simpan
            </button>
        </div>
    </div>
</form>

{{-- JS kecil untuk auto-hitungan Sisa --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const totalInput     = document.getElementById('total_amount');
    const remainingInput = document.getElementById('remaining_display');
    const paid           = {{ (int) ($invoice->paid_amount ?? 0) }};

    if (!totalInput || !remainingInput) return;

    function recalcRemaining() {
        const val = parseInt(totalInput.value || '0', 10);
        let remaining = val - paid;
        if (isNaN(remaining) || remaining < 0) remaining = 0;

        remainingInput.value = new Intl.NumberFormat('id-ID').format(remaining);
    }

    totalInput.addEventListener('input', recalcRemaining);
    recalcRemaining();
});
</script>
@endsection
