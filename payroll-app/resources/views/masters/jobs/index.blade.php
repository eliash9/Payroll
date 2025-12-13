<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Master Profil Pekerjaan</h2>
            <a href="{{ route('jobs.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Tambah Profil</a>
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
                    <th class="px-3 py-2">Nama Pekerjaan</th>
                    <th class="px-3 py-2">Deskripsi</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($jobs as $job)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $job->code }}</td>
                        <td class="px-3 py-2 font-medium">{{ $job->title }}</td>
                        <td class="px-3 py-2">{{ Str::limit($job->description, 100) }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('jobs.edit', $job->id) }}">Edit</a>
                            <form method="post" action="{{ route('jobs.destroy', $job->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus profil pekerjaan ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
