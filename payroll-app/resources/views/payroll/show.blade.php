<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Periode Payroll') }} : {{ $period->code }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('payroll.periods.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md text-sm hover:bg-gray-600">
                    Kembali
                </a>
                @if(!in_array($period->status, ['approved', 'closed']))
                    @if($period->status === 'calculated')
                        <form action="{{ route('payroll.periods.approve', $period->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui periode ini? Data akan dikunci dan tidak dapat diubah lagi.')">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                                Setujui & Kunci Payroll
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('payroll.periods.destroy', $period->id) }}" method="POST" onsubmit="return confirm('Hapus periode ini beserta seluruh data hitungannya?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">
                            Hapus Periode
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Status Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Berhasil!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Gagal!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Nama Periode</p>
                        <p class="font-medium text-lg">{{ $period->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Rentang Tanggal</p>
                        <p class="font-medium text-lg">{{ \Carbon\Carbon::parse($period->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($period->end_date)->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                            {{ $period->status === 'approved' ? 'bg-green-100 text-green-800' : 
                               ($period->status === 'calculated' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($period->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Pengeluaran (Net)</p>
                        <p class="font-bold text-xl text-emerald-600">Rp {{ number_format($totalNet, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Process Logic Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Regular Payroll Card -->
                <div class="bg-white shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Payroll Karyawan Reguler</h3>
                                <p class="text-sm text-gray-500">Bisyarah Pokok, Tunjangan, Overtime, BPJS</p>
                            </div>
                            <div class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $generatedRegular }} / {{ $regularCount }} Slip
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex gap-2">
                                @if(!in_array($period->status, ['approved', 'closed']))
                                    <form action="{{ route('payroll.periods.generate.regular', $period->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                                            onclick="return confirm('Proses ini akan menghitung ulang gaji reguler. Lanjutkan? Edit manual yang anda lakukan akan hilang.')">
                                            {{ $generatedRegular > 0 ? 'Hitung Ulang (Reguler)' : 'Hitung Bisyarah Reguler' }}
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('payroll.periods.preview.regular', $period->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                    Preview Detail
                                </a>
                                <div class="dropdown inline-block relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                        <span>Laporan</span>
                                        <svg class="fill-current h-4 w-4 ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <ul x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md overflow-hidden shadow-xl z-50 border border-gray-200" style="display: none;">
                                        <li>
                                            <a href="{{ route('reports.payroll', ['period_id' => $period->id, 'export' => 'excel']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export Excel</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('reports.payroll', ['period_id' => $period->id, 'export' => 'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export PDF</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volunteer Payroll Card -->
                <div class="bg-white shadow-sm sm:rounded-lg border-l-4 border-emerald-500">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Payroll Relawan</h3>
                                <p class="text-sm text-gray-500">Upah Jam, Komisi Fundraising, Bonus Target</p>
                            </div>
                            <div class="bg-emerald-100 text-emerald-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $generatedVolunteer }} / {{ $volunteerCount }} Slip
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex gap-2">
                                @if(!in_array($period->status, ['approved', 'closed']))
                                    <form action="{{ route('payroll.periods.generate.volunteer', $period->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:border-emerald-900 focus:ring ring-emerald-300 disabled:opacity-25 transition ease-in-out duration-150"
                                            onclick="return confirm('Proses ini akan menghitung ulang gaji relawan. Lanjutkan?')">
                                            {{ $generatedVolunteer > 0 ? 'Hitung Ulang (Relawan)' : 'Hitung Bisyarah Relawan' }}
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('payroll.periods.preview.volunteer', $period->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                                    Preview Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Generated Slips -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Slip Bisyarah ({{ $headers->total() }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross Income</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Potongan</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net Income</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($headers as $header)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $header->employee->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $header->employee->employee_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $header->employee->is_volunteer ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $header->employee->is_volunteer ? 'Relawan' : 'Reguler' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        Rp {{ number_format($header->gross_income, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                        Rp {{ number_format($header->total_deduction, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                        Rp {{ number_format($header->net_income, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <a href="{{ route('payslips.show', [$period->id, $header->employee_id]) }}" class="text-indigo-600 hover:text-indigo-900" target="_blank">Lihat</a>
                                        @if(!in_array($period->status, ['approved', 'closed']))
                                            <a href="{{ route('payslips.edit', [$period->id, $header->employee_id]) }}" class="text-orange-600 hover:text-orange-900">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 text-sm">
                                        Belum ada slip bisyarah yang digenerate. Silakan klik tombol "Hitung Bisyarah" di atas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $headers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
