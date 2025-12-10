<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between" x-data="{ showImportModal: false }">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cabang</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('branches.export') }}" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Export</a>
                <button @click="showImportModal = true" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">Import</button>
                <a href="{{ route('branches.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Tambah</a>
            </div>

            <!-- Import Modal -->
            <div x-show="showImportModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showImportModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('branches.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Import Cabang</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">Unggah file Excel (.xlsx) berisi data cabang. Pastikan format sesuai template.</p>
                                    <a href="{{ route('branches.import-template') }}" class="text-blue-600 hover:underline text-sm mb-4 block font-medium">Unduh Template Excel</a>
                                    <input type="file" name="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required accept=".xlsx,.xls,.csv">
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">Import</button>
                                <button type="button" @click="showImportModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto">
        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded px-3 py-2">
                {{ session('error') }}
            </div>
        @endif
        
        <div class="mb-4">
            <form method="get" action="{{ route('branches.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode atau Nama Cabang..." class="border rounded px-3 py-2 w-full sm:w-64 text-sm" />
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700 transition">Cari</button>
                @if(request('search'))
                    <a href="{{ route('branches.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-300 transition flex items-center">Reset</a>
                @endif
            </form>
        </div>

        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2 w-12">No</th>
                    <th class="px-3 py-2">Kode</th>
                    <th class="px-3 py-2">Nama</th>
                    <th class="px-3 py-2">Wilayah</th>
                    <th class="px-3 py-2">Telepon</th>
                    <th class="px-3 py-2">Lat / Long</th>
                    <th class="px-3 py-2">Grade</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($branches as $index => $branch)
                    <tr class="border-t">
                        <td class="px-3 py-2 text-center">{{ $branches->firstItem() + $index }}</td>
                        <td class="px-3 py-2">{{ $branch->code }}</td>
                        <td class="px-3 py-2">
                            {{ $branch->name }}
                            @if($branch->is_headquarters)
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded ml-2">Induk</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">
                            <div class="text-xs">
                                <div>{{ $branch->city_name }}</div>
                                <div class="text-gray-500">{{ $branch->province_name }}</div>
                            </div>
                        </td>
                        <td class="px-3 py-2">{{ $branch->phone }}</td>
                        <td class="px-3 py-2">
                            @if($branch->latitude && $branch->longitude)
                                <div class="text-xs">
                                    <div>{{ Str::limit($branch->latitude, 8) }}</div>
                                    <div>{{ Str::limit($branch->longitude, 8) }}</div>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-3 py-2">{{ $branch->grade ?? '-' }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('branches.edit', $branch->id) }}">Edit</a>
                            <form method="post" action="{{ route('branches.destroy', $branch->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus cabang?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $branches->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
