<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Program') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-xl font-semibold">Program</h1>
                    <a href="{{ route('laz.programs.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded">Tambah</a>
                </div>
                <table class="w-full text-sm border">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-2 border">Nama</th>
                            <th class="p-2 border">Kategori</th>
                            <th class="p-2 border">Penerima</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($programs as $program)
                            <tr>
                                <td class="p-2 border">{{ $program->name }}</td>
                                <td class="p-2 border">{{ $program->category }}</td>
                                <td class="p-2 border">{{ $program->allowed_recipient_type }}</td>
                                <td class="p-2 border">{{ $program->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                <td class="p-2 border">
                                    <a href="{{ route('laz.programs.edit', $program) }}" class="text-emerald-700">Edit</a>
                                    <form action="{{ route('laz.programs.destroy', $program) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 ml-2" onclick="return confirm('Hapus program?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $programs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
