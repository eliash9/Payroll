<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard Admin
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

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Company Overview Section -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Perusahaan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Employees -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Karyawan</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['employees'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Branches -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Cabang</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['branches'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Departments -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Departemen</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['departments'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Present Today -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-5 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Hadir Hari Ini</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['present_today'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fundraising Overview Section -->
        <div>
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ringkasan Fundraising ({{ \Carbon\Carbon::parse($period)->translatedFormat('F Y') }})</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Total Donasi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totals['amount'],0,',','.') }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totals['transactions'] }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Jam Aktif Fundraiser</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($totals['hours'],1) }} <span class="text-sm font-normal text-gray-500">jam</span></p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-xs text-slate-500 uppercase tracking-wider">Estimasi Komisi</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totals['commission'],0,',','.') }}</p>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Top Fundraisers Table -->
            <div class="bg-white shadow-sm rounded-lg p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-lg text-gray-800">Top Fundraiser</h2>
                    <span class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded">Top 10</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Total Donasi</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Trx</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 uppercase tracking-wider">Komisi</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topFundraisers as $row)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $row->full_name }}</td>
                                <td class="px-4 py-3 text-right text-green-600 font-semibold">Rp {{ number_format($row->total_amount,0,',','.') }}</td>
                                <td class="px-4 py-3 text-center">{{ $row->total_transactions }}</td>
                                <td class="px-4 py-3 text-center">{{ number_format($row->total_hours,1) }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($row->commission,0,',','.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500 italic">Belum ada data transaksi untuk periode ini.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h2 class="font-semibold text-lg text-gray-800 mb-4">Grafik Donasi</h2>
                <div class="relative h-64">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($chartLabels);
        const data = @json($chartData);
        const ctx = document.getElementById('barChart');
        
        // Only render chart if there is data
        if (labels.length > 0) {
            new Chart(ctx, {
                type: 'doughnut', // Changed to doughnut for variety/better fit in side panel, or stick to bar? Let's stick to bar but vertical
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Donasi',
                        data: data,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        } else {
            // Show placeholder text if no data
            ctx.parentElement.innerHTML = '<div class="flex items-center justify-center h-full text-gray-400 text-sm">Tidak ada data untuk ditampilkan</div>';
        }
    </script>
</x-app-layout>
