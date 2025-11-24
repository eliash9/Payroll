<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard Fundraiser
                </h2>
                <p class="text-sm text-slate-500">Periode {{ $period }}</p>
            </div>
            <form method="get" class="flex items-center gap-2">
                <input type="month" name="period" value="{{ $period }}" class="border rounded px-2 py-1 text-sm">
                <input type="text" name="campaign" value="{{ $campaign }}" placeholder="Campaign (opsional)" class="border rounded px-2 py-1 text-sm">
                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Filter</button>
            </form>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto space-y-6">
        <div class="grid md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white shadow-sm rounded p-4">
                <p class="text-xs text-slate-500">Total Donasi</p>
                <p class="text-2xl font-semibold">Rp {{ number_format($totals['amount'],0,',','.') }}</p>
            </div>
            <div class="bg-white shadow-sm rounded p-4">
                <p class="text-xs text-slate-500">Transaksi</p>
                <p class="text-2xl font-semibold">{{ $totals['transactions'] }}</p>
            </div>
            <div class="bg-white shadow-sm rounded p-4">
                <p class="text-xs text-slate-500">Jam Aktif</p>
                <p class="text-2xl font-semibold">{{ number_format($totals['hours'],1) }} jam</p>
            </div>
            <div class="bg-white shadow-sm rounded p-4">
                <p class="text-xs text-slate-500">Estimasi Komisi</p>
                <p class="text-2xl font-semibold">Rp {{ number_format($totals['commission'],0,',','.') }}</p>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded p-4 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold">Top Fundraiser</h2>
                <span class="text-xs text-slate-500">Top 10 berdasarkan total donasi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="bg-slate-100 text-left">
                        <th class="px-3 py-2">Nama</th>
                        <th class="px-3 py-2">Total Donasi</th>
                        <th class="px-3 py-2">Transaksi</th>
                        <th class="px-3 py-2">Jam Aktif</th>
                        <th class="px-3 py-2">Komisi Est.</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($topFundraisers as $row)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $row->full_name }}</td>
                            <td class="px-3 py-2">Rp {{ number_format($row->total_amount,0,',','.') }}</td>
                            <td class="px-3 py-2">{{ $row->total_transactions }}</td>
                            <td class="px-3 py-2">{{ number_format($row->total_hours,1) }} jam</td>
                            <td class="px-3 py-2">Rp {{ number_format($row->commission,0,',','.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded p-4">
            <h2 class="font-semibold mb-4">Grafik Donasi per Fundraiser (Top 10)</h2>
            <canvas id="barChart" height="120"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($chartLabels);
        const data = @json($chartData);
        const ctx = document.getElementById('barChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Donasi',
                    data: data,
                    backgroundColor: 'rgba(59,130,246,0.6)',
                    borderColor: 'rgba(37,99,235,0.9)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>
