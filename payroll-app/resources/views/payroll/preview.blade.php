<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $title }}</h2>
                @if($period)
                    <div class="text-sm text-gray-500">{{ $period->code }} ({{ $period->start_date }} s/d {{ $period->end_date }})</div>
                @endif
            </div>
            <a href="{{ route('payroll.periods.index') }}" class="text-blue-600 underline text-sm">Kembali ke daftar periode</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto space-y-4">
        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Karyawan</th>
                    <th class="px-3 py-2">Gross</th>
                    <th class="px-3 py-2">Potongan</th>
                    <th class="px-3 py-2">Net</th>
                    <th class="px-3 py-2">Rincian Komponen</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $row)
                    <tr class="border-t align-top">
                        <td class="px-3 py-2">
                            <div class="font-semibold">{{ $row['employee']->full_name }}</div>
                            <div class="text-xs text-slate-500">{{ $row['employee']->employee_code }}</div>
                        </td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($row['gross'],0,',','.') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($row['deduction'],0,',','.') }}</td>
                        <td class="px-3 py-2 font-semibold text-right">Rp {{ number_format($row['net'],0,',','.') }}</td>
                        <td class="px-3 py-2">
                            <table class="text-xs min-w-[320px]">
                                <thead>
                                <tr class="text-slate-500">
                                    <th class="text-left">Kode</th>
                                    <th class="text-left">Keterangan</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Jumlah</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($row['components'] as $comp)
                                    <tr>
                                        <td class="pr-2">{{ $comp['code'] }}</td>
                                        <td class="pr-2">{{ $comp['label'] }}</td>
                                        <td class="pr-2 text-right">{{ $comp['quantity'] ?? '-' }}</td>
                                        <td class="text-right">Rp {{ number_format($comp['amount'],0,',','.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
