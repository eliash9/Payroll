<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tarif BPJS</h2>
            <div class="flex items-center gap-2">
                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'help-bpjs-rates')" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700 transition">Bantuan</button>
                <a href="{{ route('bpjs-rates.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">Tambah</a>
            </div>
        </div>
    </x-slot>

    <x-modal name="help-bpjs-rates" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Panduan Tarif BPJS</h2>
            
            <div class="space-y-4 text-sm text-gray-600">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <h3 class="font-bold text-blue-800 mb-2">Apa itu Tarif BPJS?</h3>
                    <p>Halaman ini digunakan untuk menyimpan konfigurasi persentase iuran BPJS Kesehatan dan BPJS Ketenagakerjaan (JHT, JKK, JKM, JP) sesuai regulasi pemerintah yang berlaku.</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Kolom Penting:</h3>
                    <ul class="list-disc list-inside space-y-1 ml-1">
                        <li><span class="font-semibold">Perusahaan %</span>: Persentase yang ditanggung oleh pemberi kerja (benefit).</li>
                        <li><span class="font-semibold">Karyawan %</span>: Persentase yang dipotong langsung dari gaji karyawan (deduction).</li>
                        <li><span class="font-semibold">Batas Upah (Cap)</span>: Nilai maksimal atau minimal dasar pengali iuran (Contoh: BPJS Kesehatan maks 12jt).</li>
                    </ul>
                </div>

                <div class="bg-green-50 p-3 rounded border border-green-100">
                    <h3 class="font-bold text-green-800 mb-1">Efek ke Sistem:</h3>
                    <p>Sistem akan menggunakan tarif ini secara otomatis saat proses <b>Payroll Run</b>. Jika ada perubahan regulasi tarif dari pemerintah, Anda cukup mengupdate angka di sini, dan perhitungan gaji bulan berikutnya akan otomatis menyesuaikan tanpa perlu ubah data karyawan satu per satu.</p>
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
                    <th class="px-3 py-2">Program</th>
                    <th class="px-3 py-2">Perusahaan %</th>
                    <th class="px-3 py-2">Karyawan %</th>
                    <th class="px-3 py-2">Cap Min</th>
                    <th class="px-3 py-2">Cap Max</th>
                    <th class="px-3 py-2">Periode</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rates as $rate)
                    <tr class="border-t">
                        <td class="px-3 py-2 uppercase">{{ $rate->program }}</td>
                        <td class="px-3 py-2">{{ $rate->employer_rate }}%</td>
                        <td class="px-3 py-2">{{ $rate->employee_rate }}%</td>
                        <td class="px-3 py-2">{{ $rate->salary_cap_min }}</td>
                        <td class="px-3 py-2">{{ $rate->salary_cap_max }}</td>
                        <td class="px-3 py-2">{{ $rate->effective_from }} - {{ $rate->effective_to ?? '...' }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('bpjs-rates.edit', $rate->id) }}">Edit</a>
                            <form method="post" action="{{ route('bpjs-rates.destroy', $rate->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus tarif BPJS?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $rates->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
