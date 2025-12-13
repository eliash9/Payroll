<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Jabatan</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('positions.update', $position->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id', $position->company_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Departemen</label>
                        <select name="department_id" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih</option>
                            @foreach($departments as $id => $name)
                                <option value="{{ $id }}" @selected(old('department_id', $position->department_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Job Profile</label>
                        <select name="job_id" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih Job Profile</option>
                            @foreach($jobs as $id => $title)
                                <option value="{{ $id }}" @selected(old('job_id', $position->job_id) == $id)>{{ $title }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Mengaitkan dengan profil pekerjaan (JD & Kualifikasi).</p>
                    </div>
                    <div>
                        <label class="text-sm">Nama Jabatan</label>
                        <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $position->name) }}">
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code', $position->code) }}">
                    </div>
                    <div>
                        <label class="text-sm">Atasan Langsung (Reports To)</label>
                        <select name="parent_id" class="w-full border rounded px-3 py-2">
                            <option value="">Tidak ada</option>
                            @foreach($parents as $id => $name)
                                <option value="{{ $id }}" @selected(old('parent_id', $position->parent_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Grade</label>
                        <input name="grade" class="w-full border rounded px-3 py-2" value="{{ old('grade', $position->grade) }}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm">Deskripsi</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $position->description) }}</textarea>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('positions.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
