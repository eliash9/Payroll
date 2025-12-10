<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Lokasi Kerja Custom</h2>
            <a href="{{ route('work-locations.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Tambah</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto">
        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="mb-4">
            <form method="get" action="{{ route('work-locations.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Lokasi..." class="border rounded px-3 py-2 w-full sm:w-64 text-sm" />
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 transition">Cari</button>
                @if(request('search'))
                    <a href="{{ route('work-locations.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300 transition flex items-center">Reset</a>
                @endif
            </form>
        </div>

        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Nama Lokasi</th>
                    <th class="px-3 py-2">Radius</th>
                    <th class="px-3 py-2">Lat / Long</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($locations as $location)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $location->name }}</td>
                        <td class="px-3 py-2">{{ $location->radius }} meter</td>
                        <td class="px-3 py-2">
                            {{ $location->latitude }}, {{ $location->longitude }}
                        </td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('work-locations.edit', $location->id) }}">Edit</a>
                            <form method="post" action="{{ route('work-locations.destroy', $location->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus lokasi?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $locations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
