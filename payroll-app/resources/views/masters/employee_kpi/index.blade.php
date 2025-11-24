<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">KPI Karyawan</h2>
            <a href="{{ route('employee-kpi.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
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
                    <th class="px-3 py-2">KPI</th>
                    <th class="px-3 py-2">Target</th>
                    <th class="px-3 py-2">Bobot</th>
                    <th class="px-3 py-2">Periode Aktif</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($assignments as $item)
                    <tr class="border-t">
                        <td class="px-3 py-2">
                            <div class="font-semibold">{{ $item->employee_name }}</div>
                            <div class="text-xs text-slate-500">{{ $item->employee_code }}</div>
                        </td>
                        <td class="px-3 py-2">
                            <div class="font-semibold">{{ $item->kpi_name }}</div>
                            <div class="text-xs text-slate-500">{{ $item->kpi_code }}</div>
                        </td>
                        <td class="px-3 py-2">{{ $item->target }}</td>
                        <td class="px-3 py-2">{{ $item->weight }}</td>
                        <td class="px-3 py-2">{{ $item->start_date }} - {{ $item->end_date ?? '...' }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('employee-kpi.edit', $item->id) }}">Edit</a>
                            <form method="post" action="{{ route('employee-kpi.destroy', $item->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus assignment KPI?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
