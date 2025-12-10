<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Template Survey') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Header & Filters -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
                    <form method="GET" class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode atau Judul..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        
                        <select name="program_id" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Semua Program</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </form>

                    <a href="{{ route('laz.survey-templates.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        + Template Baru
                    </a>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($templates as $template)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $template->code }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $template->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $template->program->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $template->is_active ? 'Aktif' : 'Non-Aktif' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('laz.survey-templates.show', $template) }}" class="text-blue-600 hover:text-blue-900 mr-3">Lihat</a>
                                        <a href="{{ route('laz.survey-templates.edit', $template) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="{{ route('laz.survey-templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Hapus template ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada template survey.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $templates->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
