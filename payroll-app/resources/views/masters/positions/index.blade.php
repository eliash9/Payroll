<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between" x-data="{ showImportModal: false }">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jabatan</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('positions.export') }}" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Export</a>
                <button @click="showImportModal = true" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">Import</button>
                <a href="{{ route('positions.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Tambah</a>
            </div>

            <!-- Import Modal -->
            <div x-show="showImportModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showImportModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showImportModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <form action="{{ route('positions.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Import Jabatan</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">Unggah file Excel (.xlsx) berisi data jabatan. Pastikan format sesuai template.</p>
                                    <a href="{{ route('positions.import-template') }}" class="text-blue-600 hover:underline text-sm mb-4 block font-medium">Unduh Template Excel</a>
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
        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Kode</th>
                    <th class="px-3 py-2">Nama</th>
                    <th class="px-3 py-2">Departemen</th>
                    <th class="px-3 py-2">Job Profile</th>
                    <th class="px-3 py-2">Atasan</th>
                    <th class="px-3 py-2">Grade</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($positions as $position)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $position->code }}</td>
                        <td class="px-3 py-2 font-medium">
                            <div style="padding-left: {{ ($position->depth ?? 0) * 20 }}px">
                                @if(($position->depth ?? 0) > 0)
                                    <span class="text-slate-400 mr-1">↳</span>
                                @endif
                                {{ $position->name }}
                            </div>
                        </td>
                        <td class="px-3 py-2">{{ $position->department?->name ?? '-' }}</td>
                        <td class="px-3 py-2">
                            @if($position->job)
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium">{{ $position->job->title }}</span>
                                    <button onclick="openJobModal('{{ addslashes($position->job->title) }}', '{{ addslashes($position->job->description) }}', {{ $position->job->responsibilities->toJson() }}, {{ $position->job->requirements->toJson() }})" class="text-xs text-indigo-600 border border-indigo-200 bg-indigo-50 px-2 py-0.5 rounded hover:bg-indigo-100">
                                        Lihat
                                    </button>
                                </div>
                            @else
                                <span class="text-slate-400 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-3 py-2">{{ $position->parent?->name ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $position->grade ?? '-' }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('positions.edit', $position->id) }}">Edit</a>
                            <form method="post" action="{{ route('positions.destroy', $position->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus jabatan?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Job Detail Modal -->
    <div x-data="{ open: false, jobTitle: '', jobDesc: '', responsibilities: [], requirements: [] }" 
         x-on:open-modal-event.window="
            open = true; 
            jobTitle = $event.detail.title; 
            jobDesc = $event.detail.description; 
            responsibilities = $event.detail.responsibilities; 
            requirements = $event.detail.requirements;
         "
         x-show="open" 
         style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" @click="open = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="jobTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 mb-4" x-text="jobDesc"></p>

                                <div class="mb-4">
                                    <h4 class="font-semibold text-sm text-gray-700 mb-2">Tanggung Jawab Utama</h4>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <template x-for="resp in responsibilities">
                                            <li>
                                                <span x-text="resp.responsibility"></span>
                                                <span x-show="resp.is_primary" class="ml-2 text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">Utama</span>
                                            </li>
                                        </template>
                                        <li x-show="responsibilities.length === 0" class="text-slate-400 italic">Tidak ada data</li>
                                    </ul>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-sm text-gray-700 mb-2">Kualifikasi</h4>
                                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                        <template x-for="req in requirements">
                                            <li>
                                                <span class="font-medium text-xs uppercase bg-gray-100 px-1 py-0.5 rounded mr-1" x-text="req.type"></span>
                                                <span x-text="req.requirement"></span>
                                            </li>
                                        </template>
                                        <li x-show="requirements.length === 0" class="text-slate-400 italic">Tidak ada data</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openJobModal(title, description, responsibilities, requirements) {
            window.dispatchEvent(new CustomEvent('open-modal-event', {
                detail: {
                    title: title,
                    description: description,
                    responsibilities: responsibilities,
                    requirements: requirements
                }
            }));
        }
    </script>
</x-app-layout>
