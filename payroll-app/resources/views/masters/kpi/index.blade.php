<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master KPI</h2>
            <div class="flex items-center gap-2">
                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'help-kpi')" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700 transition">Bantuan</button>
                <a href="{{ route('kpi.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">Tambah</a>
            </div>
        </div>
    </x-slot>

    <x-modal name="help-kpi" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Panduan Master KPI</h2>
            
            <div class="space-y-4 text-sm text-gray-600">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <h3 class="font-bold text-blue-800 mb-2">Apa itu Master KPI?</h3>
                    <p>KPI (Key Performance Indicator) adalah indikator terukur yang digunakan untuk menilai apakah karyawan telah mencapai target kinerjanya.</p>
                </div>

                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                    <h3 class="font-bold text-gray-800 mb-2">Contoh KPI:</h3>
                    <ul class="list-disc list-inside space-y-1">
                        <li><span class="font-medium">Kehadiran (Absensi):</span> Menilai persentase kehadiran tepat waktu.</li>
                        <li><span class="font-medium">Penyelesaian Tiket Support:</span> Menilai jumlah tiket yang diselesaikan.</li>
                        <li><span class="font-medium">Kepuasan Pelanggan:</span> Menilai rating rata-rata dari feedback pelanggan.</li>
                    </ul>
                </div>

                <div class="bg-green-50 p-3 rounded border border-green-100">
                    <h3 class="font-bold text-green-800 mb-1">Efek ke Sistem:</h3>
                    <p>Daftar KPI ini akan menjadi pilihan saat Anda atau Manager membuat <b>Formulir Penilaian Kinerja</b>. Skor yang didapat dari realisasi KPI ini akan menentukan Nilai Akhir kinerja karyawan.</p>
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
                    <th class="px-3 py-2">Kode</th>
                    <th class="px-3 py-2">Nama</th>
                    <th class="px-3 py-2">Tipe</th>
                    <th class="px-3 py-2">Period</th>
                    <th class="px-3 py-2">Kategori</th>
                    <th class="px-3 py-2">Target Default</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($kpis as $kpi)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $kpi->code }}</td>
                        <td class="px-3 py-2">{{ $kpi->name }}</td>
                        <td class="px-3 py-2">{{ ucfirst($kpi->type) }}</td>
                        <td class="px-3 py-2">{{ ucfirst($kpi->period_type) }}</td>
                        <td class="px-3 py-2">{{ ucfirst($kpi->category) }}</td>
                        <td class="px-3 py-2">{{ $kpi->target_default }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('kpi.edit', $kpi->id) }}">Edit</a>
                            <form method="post" action="{{ route('kpi.destroy', $kpi->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus KPI?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $kpis->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
