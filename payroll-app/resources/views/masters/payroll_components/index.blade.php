<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Komponen Payroll</h2>
            <a href="{{ route('payroll-components.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
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
