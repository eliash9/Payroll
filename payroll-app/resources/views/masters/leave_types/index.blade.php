<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Cuti/Izin</h2>
            <a href="{{ route('leave-types.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto">
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
                    <th class="px-3 py-2">Berbayar</th>
                    <th class="px-3 py-2">Potong Kuota</th>
                    <th class="px-3 py-2">Quota Default</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($leaveTypes as $type)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $type->code }}</td>
                        <td class="px-3 py-2">{{ $type->name }}</td>
                        <td class="px-3 py-2">{{ $type->is_paid ? 'Ya' : 'Tidak' }}</td>
                        <td class="px-3 py-2">{{ $type->is_annual_quota ? 'Ya' : 'Tidak' }}</td>
                        <td class="px-3 py-2">{{ $type->default_quota_days }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('leave-types.edit', $type->id) }}">Edit</a>
                            <form method="post" action="{{ route('leave-types.destroy', $type->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus jenis cuti?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $leaveTypes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
