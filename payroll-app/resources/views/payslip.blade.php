<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Slip Bisyarah / Insentif</h1>
                <p class="text-sm text-slate-500">{{ $period->name }} ({{ $period->code }})</p>
            </div>
            <div class="text-right text-sm text-slate-600">
                <p>{{ $employee->full_name }}</p>
                <p>{{ $employee->employee_code }}</p>
                @if($employee->is_volunteer)
                    <span class="inline-block px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded">Relawan</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto space-y-4">
        <div class="bg-white shadow-sm rounded p-4">
            <h2 class="font-semibold mb-3">Rincian Komponen</h2>
            <table class="w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Komponen</th>
                    <th class="px-3 py-2">Kode</th>
                    <th class="px-3 py-2">Jenis</th>
                    <th class="px-3 py-2 text-right">Jumlah</th>
                    <th class="px-3 py-2 text-right">Kuantitas</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $loanTotal = 0;
                @endphp
                @foreach($details as $d)
                    @php
                        if ($d->code === 'LOAN_INSTALLMENT') {
                            $loanTotal += $d->amount;
                        }
                    @endphp
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $d->name }}</td>
                        <td class="px-3 py-2 text-xs text-slate-500">{{ $d->code }}</td>
                        <td class="px-3 py-2">{{ $d->type }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($d->amount,0,',','.') }}</td>
                        <td class="px-3 py-2 text-right">{{ $d->quantity ?? '-' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @if($loanTotal !== 0)
                <div class="mt-3 text-sm text-slate-600">
                    <span class="font-semibold">Total Potongan Pinjaman:</span>
                    Rp {{ number_format($loanTotal,0,',','.') }}
                </div>
            @endif
        </div>

        <div class="bg-white shadow-sm rounded p-4 grid grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-slate-500">Pendapatan Bruto (Bisyarah Kotor)</p>
                <p class="text-lg font-semibold">Rp {{ number_format($header->gross_income,0,',','.') }}</p>
            </div>
            <div>
                <p class="text-slate-500">Potongan</p>
                <p class="text-lg font-semibold">Rp {{ number_format($header->total_deduction,0,',','.') }}</p>
            </div>
            <div>
                <p class="text-slate-500">Take Home</p>
                <p class="text-lg font-semibold text-emerald-600">Rp {{ number_format($header->net_income,0,',','.') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
