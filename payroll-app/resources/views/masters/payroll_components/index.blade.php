<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Komponen Payroll</h2>
            <div class="flex items-center gap-2">
                <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'help-payroll-components')" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700 transition">Bantuan</button>
                <a href="{{ route('payroll-components.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">Tambah</a>
            </div>
        </div>
    </x-slot>

    <x-modal name="help-payroll-components" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Panduan Komponen Payroll</h2>
            
            <div class="space-y-4 text-sm text-gray-600">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <h3 class="font-bold text-blue-800 mb-2">Apa itu Komponen Payroll?</h3>
                    <p>Menu ini digunakan untuk mendefinisikan item-item penyusun gaji karyawan, baik itu penambah (Tunjangan) maupun pengurang (Potongan).</p>
                </div>

                <div>
                    <h3 class="font-bold text-gray-800 mb-1">Cara Penggunaan:</h3>
                    <ul class="list-disc list-inside space-y-1 ml-1">
                        <li>Komponen <span class="font-semibold">Allowance (Tunjangan)</span> akan menambah Take Home Pay.</li>
                        <li>Komponen <span class="font-semibold">Deduction (Potongan)</span> akan mengurangi Take Home Pay.</li>
                        <li>Komponen <span class="font-semibold">Fixed</span> nilainya tetap setiap bulan.</li>
                        <li>Komponen <span class="font-semibold">Variable</span> nilainya bisa berubah setiap bulan (input manual saat payroll run).</li>
                    </ul>
                </div>

                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                    <h3 class="font-bold text-gray-800 mb-2">Contoh:</h3>
                    <ul class="list-disc list-inside space-y-1">
                        <li><span class="font-medium">Gaji Pokok:</span> Tipe Allowance, Fixed.</li>
                        <li><span class="font-medium">Tunjangan Makan:</span> Tipe Allowance, Fixed/Daily.</li>
                        <li><span class="font-medium">Potongan Kasbon:</span> Tipe Deduction, Variable.</li>
                    </ul>
                </div>

                <div class="bg-green-50 p-3 rounded border border-green-100">
                    <h3 class="font-bold text-green-800 mb-1">Efek ke Sistem:</h3>
                    <p>Komponen yang Anda buat di sini akan muncul di formulir <b>Kontrak Kerja Karyawan</b>. Saat Anda menjalankan proses <b>Payroll Run</b>, sistem akan menghitung total gaji berdasarkan komponen yang terpasang pada karyawan tersebut.</p>
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
                    <th class="px-3 py-2">Kategori</th>
                    <th class="px-3 py-2">Metode</th>
                    <th class="px-3 py-2">Seq</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($components as $component)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $component->code }}</td>
                        <td class="px-3 py-2">{{ $component->name }}</td>
                        <td class="px-3 py-2">{{ ucfirst($component->type) }}</td>
                        <td class="px-3 py-2">{{ strtoupper($component->category) }}</td>
                        <td class="px-3 py-2">{{ str_replace('_',' ', ucfirst($component->calculation_method)) }}</td>
                        <td class="px-3 py-2">{{ $component->sequence }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('payroll-components.edit', $component->id) }}">Edit</a>
                            <form method="post" action="{{ route('payroll-components.destroy', $component->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus komponen?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $components->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
