<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengguna</h2>
            <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto">
        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif
        <div class="bg-white shadow-sm rounded p-4 space-y-4">
            <form method="get" class="flex items-center gap-3">
                <input name="q" value="{{ request('q') }}" class="border rounded px-3 py-2" placeholder="Cari nama/email">
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Cari</button>
            </form>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="bg-slate-100 text-left">
                        <th class="px-3 py-2">Nama</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Company</th>
                        <th class="px-3 py-2">Role</th>
                        <th class="px-3 py-2">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $user->name }}</td>
                            <td class="px-3 py-2">{{ $user->email }}</td>
                            <td class="px-3 py-2">{{ $user->company_name ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $user->role ?? '-' }}</td>
                            <td class="px-3 py-2 space-x-2">
                                <a class="text-blue-600 underline" href="{{ route('users.edit', $user->id) }}">Edit</a>
                                <form method="post" action="{{ route('users.destroy', $user->id) }}" class="inline">
                                    @csrf
                                    @method('delete')
                                    <button class="text-red-600 underline" onclick="return confirm('Hapus user?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
