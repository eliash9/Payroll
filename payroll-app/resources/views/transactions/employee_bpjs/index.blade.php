<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">BPJS Karyawan</h2>
            <a href="{{ route('employee-bpjs.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
        </div>
    </x-slot>

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
                    <th class="px-3 py-2">Karyawan</th>
                    <th class="px-3 py-2">BPJS Kes</th>
                    <th class="px-3 py-2">Kelas</th>
                    <th class="px-3 py-2">BPJS TK</th>
                    <th class="px-3 py-2">Mulai</th>
                    <th class="px-3 py-2">Aktif</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($bpjs as $item)
                    <tr class="border-t">
                        <td class="px-3 py-2">
                            <div class="font-semibold">{{ $item->full_name }}</div>
                            <div class="text-xs text-slate-500">{{ $item->employee_code }}</div>
                        </td>
                        <td class="px-3 py-2">{{ $item->bpjs_kesehatan_number }}</td>
                        <td class="px-3 py-2">{{ $item->bpjs_kesehatan_class }}</td>
                        <td class="px-3 py-2">{{ $item->bpjs_ketenagakerjaan_number }}</td>
                        <td class="px-3 py-2">{{ $item->start_date }}</td>
                        <td class="px-3 py-2">{{ $item->is_active ? 'Ya' : 'Tidak' }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('employee-bpjs.edit', $item->id) }}">Edit</a>
                            <form method="post" action="{{ route('employee-bpjs.destroy', $item->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus data?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $bpjs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
