<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">Halo, {{ $employee->full_name }}</h2>
                <p class="text-sm text-slate-500">Periode {{ $period }}</p>
            </div>
            <form method="get" class="flex items-center gap-2">
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <input type="month" name="period" value="{{ $period }}" class="border rounded px-2 py-1 text-sm">
                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Ganti</button>
            </form>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto space-y-6">
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white shadow-sm rounded p-4">
                <p class="text-xs text-slate-500">Total Donasi</p>
                <p class="text-2xl font-semibold">Rp {{ number_format($donation->total_amount ?? 0,0,',','.') }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $donation->total_transactions ?? 0 }} transaksi</p>
            </div>
            <div class="bg-white shadow-sm rounded p-4">
                <p class="text-xs text-slate-500">Jam Aktif</p>
                <p class="text-2xl font-semibold">{{ number_format($totalHours,1) }} jam</p>
                <p class="text-xs text-slate-500 mt-1">Hourly rate: Rp {{ number_format($employee->hourly_rate,0,',','.') }}</p>
            </div>
            <div class="bg-white shadow-sm rounded p-4">
                <p class="text-xs text-slate-500">Estimasi Komisi</p>
                <p class="text-2xl font-semibold">Rp {{ number_format($commission,0,',','.') }}</p>
                <p class="text-xs text-slate-500 mt-1">Rate: {{ $employee->commission_rate }}% @if($employee->max_commission_cap) (cap Rp {{ number_format($employee->max_commission_cap,0,',','.') }}) @endif</p>
            </div>
        </div>

        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="bg-white shadow-sm rounded p-4">
                <h2 class="font-semibold mb-2">Ringkasan Pendapatan</h2>
                <ul class="text-sm space-y-2">
                    <li>Hourly income: Rp {{ number_format($hourlyIncome,0,',','.') }}</li>
                    <li>Komisi fundraising: Rp {{ number_format($commission,0,',','.') }}</li>
                    <li><strong>Total estimasi: Rp {{ number_format($hourlyIncome + $commission,0,',','.') }}</strong></li>
                </ul>
            </div>

            <div class="bg-white shadow-sm rounded p-4">
                <h2 class="font-semibold mb-2">Status Klaim Pengeluaran</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500">
                                <th class="pb-2">Tanggal</th>
                                <th class="pb-2">Ket</th>
                                <th class="pb-2">Jumlah</th>
                                <th class="pb-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($claims as $claim)
                                <tr class="border-t">
                                    <td class="py-2">{{ \Carbon\Carbon::parse($claim->date)->format('d/m') }}</td>
                                    <td class="py-2">{{ Str::limit($claim->description, 20) }}</td>
                                    <td class="py-2">Rp {{ number_format($claim->amount,0,',','.') }}</td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 rounded text-xs {{ $claim->status === 'approved' ? 'bg-green-100 text-green-800' : ($claim->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($claim->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-2 text-center text-slate-500">Tidak ada klaim</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
