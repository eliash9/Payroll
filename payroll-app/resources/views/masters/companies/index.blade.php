<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Instansi</h2>
            <a href="{{ route('companies.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
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
                    <th class="px-3 py-2">Nama Instansi</th>
                    <th class="px-3 py-2">Kontak</th>
                    <th class="px-3 py-2">NPWP</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($companies as $company)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $company->code }}</td>
                        <td class="px-3 py-2">{{ $company->name }}</td>
                        <td class="px-3 py-2">
                            <div>{{ $company->phone }}</div>
                            <div>{{ $company->email }}</div>
                        </td>
                        <td class="px-3 py-2">{{ $company->npwp }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('companies.edit', $company->id) }}">Edit</a>
                            <form method="post" action="{{ route('companies.destroy', $company->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus Instansi?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
