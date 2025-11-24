{{-- resources/views/sensipay/payments/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approval Pembayaran Orang Tua') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- FLASH MESSAGE --}}
            @if(session('success'))
                <div class="mb-4 rounded-md bg-green-100 p-3 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 rounded-md bg-red-100 p-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 text-gray-900">

                    {{-- JUDUL --}}
                    <div class="mb-4 flex items-center justify-between">
                        <h1 class="text-lg font-semibold">
                            Approval Pembayaran Orang Tua
                        </h1>
                    </div>

                    {{-- FILTER STATUS --}}
                    @php
                        $currentStatus = $status ?? null;
                    @endphp

                    <div class="mb-4 flex flex-wrap gap-2">
                        <a href="{{ route('sensipay.payments.index') }}"
                           class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                  {{ $currentStatus === null ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Semua
                        </a>

                        <a href="{{ route('sensipay.payments.index', ['status' => 'pending']) }}"
                           class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                  {{ $currentStatus === 'pending' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' }}">
                            Pending
                        </a>

                        <a href="{{ route('sensipay.payments.index', ['status' => 'approved']) }}"
                           class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                  {{ $currentStatus === 'approved' ? 'bg-green-600 text-white' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                            Approved
                        </a>

                        <a href="{{ route('sensipay.payments.index', ['status' => 'rejected']) }}"
                           class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                  {{ $currentStatus === 'rejected' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                            Rejected
                        </a>
                    </div>

                    {{-- TABEL PEMBAYARAN --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Tanggal</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Invoice</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Siswa</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Orang Tua</th>
                                    <th class="px-3 py-2 text-right font-medium text-gray-600">Jumlah</th>
                                    <th class="px-3 py-2 text-center font-medium text-gray-600">Bukti</th>
                                    <th class="px-3 py-2 text-center font-medium text-gray-600">Status</th>
                                    <th class="px-3 py-2 text-center font-medium text-gray-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($payments as $payment)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ optional($payment->created_at)->format('d-m-Y H:i') }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ $payment->invoice->invoice_code ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ $payment->invoice->student->name ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap">
                                            {{ $payment->invoice->student->parent->name ?? '-' }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-right">
                                            Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}
                                        </td>

                                        {{-- BUKTI --}}
                                        <td class="px-3 py-2 whitespace-nowrap text-center">
                                            @if(!empty($payment->proof_path))
                                                <a href="{{ asset('storage/' . $payment->proof_path) }}"
                                                   target="_blank"
                                                   class="text-xs text-blue-600 underline hover:text-blue-800">
                                                    Lihat
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>

                                        {{-- STATUS --}}
                                        <td class="px-3 py-2 whitespace-nowrap text-center">
                                            @if($payment->status === 'pending')
                                                <span class="inline-flex rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-semibold text-yellow-800">
                                                    Pending
                                                </span>
                                            @elseif($payment->status === 'approved')
                                                <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800">
                                                    Approved
                                                </span>
                                            @else
                                                <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">
                                                    Rejected
                                                </span>
                                            @endif
                                        </td>

                                        {{-- AKSI --}}
                                        <td class="px-3 py-2 whitespace-nowrap text-center">
                                            @if($payment->status === 'pending')
                                                <form action="{{ route('sensipay.payments.approve', $payment) }}"
                                                      method="POST"
                                                      class="inline-block">
                                                    @csrf
                                                    <button type="submit"
                                                            class="mb-1 rounded bg-green-600 px-3 py-1 text-xs font-semibold text-white hover:bg-green-700">
                                                        Setuju
                                                    </button>
                                                </form>

                                                <form action="{{ route('sensipay.payments.reject', $payment) }}"
                                                      method="POST"
                                                      class="inline-block"
                                                      onsubmit="return confirm('Yakin tolak pembayaran ini?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">
                                                        Tolak
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">Tidak ada aksi</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-3 py-4 text-center text-gray-500">
                                            Belum ada data pembayaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
