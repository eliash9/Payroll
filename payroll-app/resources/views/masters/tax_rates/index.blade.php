<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tarif Pajak (PPh21)</h2>
            <div class="flex items-center gap-2">
                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'help-tax-rates')" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700 transition">Bantuan</button>
                <a href="{{ route('tax-rates.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">Tambah</a>
            </div>
        </div>
    </x-slot>

    <x-modal name="help-tax-rates" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Panduan Tarif Pajak (PPh 21)</h2>
            
            <div class="space-y-4 text-sm text-gray-600">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <h3 class="font-bold text-blue-800 mb-2">Apa itu Tarif Pajak?</h3>
                    <p>Halaman ini berisi tabel lapisan tarif progresif PPh 21 (Tax Brackets) yang digunakan untuk menghitung pajak terhutang berdasarkan Penghasilan Kena Pajak (PKP).</p>
                </div>

                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                    <h3 class="font-bold text-gray-800 mb-2">Contoh Lapisan (UU HPP):</h3>
                    <ul class="list-disc list-inside space-y-1">
                        <li>0 - 60 Juta: <span class="font-bold">5%</span></li>
                        <li>> 60 Juta - 250 Juta: <span class="font-bold">15%</span></li>
                        <li>> 250 Juta - 500 Juta: <span class="font-bold">25%</span></li>
                        <li>dst...</li>
                    </ul>
                </div>

                <div class="bg-green-50 p-3 rounded border border-green-100">
                    <h3 class="font-bold text-green-800 mb-1">Efek ke Sistem:</h3>
                    <p>Saat perhitungan gaji, sistem akan:</p>
                    <ol class="list-decimal list-inside ml-1">
                        <li>Menghitung Penghasilan Netto setahun.</li>
                        <li>Mengurangi dengan PTKP (Penghasilan Tidak Kena Pajak) untuk mendapat PKP.</li>
                        <li>Mengalikan PKP dengan tarif bertingkat yang Anda atur di sini untuk mendapatkan angka Pajak Terhutang Setahun.</li>
                    </ol>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button x-on:click="$dispatch('close')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Tutup</button>
            </div>
        </div>
    </x-modal>

    <div class="py-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif
        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Tahun</th>
                    <th class="px-3 py-2">Range Min</th>
                    <th class="px-3 py-2">Range Max</th>
                    <th class="px-3 py-2">Tarif %</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($taxRates as $rate)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $rate->year }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($rate->range_min, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">{{ $rate->range_max ? number_format($rate->range_max, 0, ',', '.') : 'Tak Terbatas' }}</td>
                        <td class="px-3 py-2">{{ $rate->rate_percent }}%</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('tax-rates.edit', $rate->id) }}">Edit</a>
                            <form method="post" action="{{ route('tax-rates.destroy', $rate->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus tarif pajak?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $taxRates->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
