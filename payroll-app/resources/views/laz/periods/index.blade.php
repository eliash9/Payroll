<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Periode Program') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-xl font-semibold">Periode Program</h1>
                    <a href="{{ route('laz.periods.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded">Tambah</a>
                </div>
                <table class="w-full text-sm border">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-2 border">Program</th>
                            <th class="p-2 border">Nama Periode</th>
                            <th class="p-2 border">Buka</th>
                            <th class="p-2 border">Tutup</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($periods as $period)
                            <tr>
                                <td class="p-2 border">{{ $period->program->name ?? '-' }}</td>
                                <td class="p-2 border">{{ $period->name }}</td>
                                <td class="p-2 border">{{ $period->open_at?->format('d M Y') }}</td>
                                <td class="p-2 border">{{ $period->close_at?->format('d M Y') }}</td>
                                <td class="p-2 border">{{ $period->status }}</td>
                                <td class="p-2 border">
                                    <a href="{{ route('laz.periods.edit', $period) }}" class="text-emerald-700">Edit</a>
                                    <form action="{{ route('laz.periods.destroy', $period) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('Hapus periode?')" class="text-red-600 ml-2">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $periods->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
