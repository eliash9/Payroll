<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $program->exists ? __('Edit Program') : __('Tambah Program') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-xl font-semibold mb-4">{{ $program->exists ? 'Edit Program' : 'Tambah Program' }}</h1>
                <form action="{{ $program->exists ? route('laz.programs.update', $program) : route('laz.programs.store') }}" method="POST" class="space-y-3">
                    @csrf
                    @if ($program->exists) @method('PUT') @endif
                    <div>
                        <label class="text-sm text-slate-600">Nama</label>
                        <input name="name" value="{{ old('name', $program->name) }}" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Kategori</label>
                        <input name="category" value="{{ old('category', $program->category) }}" class="mt-1 w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">Deskripsi</label>
                        <textarea name="description" rows="3" class="mt-1 w-full border rounded px-3 py-2">{{ old('description', $program->description) }}</textarea>
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">Syarat & Ketentuan Khusus</label>
                        <span class="text-xs text-gray-500 block mb-1">Jelaskan persyaratan khusus jika ada (misal: "Hanya untuk warga Desa X", "Usia 18-35 tahun"). Kosongkan jika tidak ada.</span>
                        <textarea name="specific_requirements" rows="3" class="mt-1 w-full border rounded px-3 py-2 placeholder-gray-300" placeholder="Contoh: Pemohon wajib berdomisili di wilayah kecamatan Sidogiri...">{{ old('specific_requirements', $program->specific_requirements) }}</textarea>
                    </div>

                    <div>
                        <label class="text-sm text-slate-600 block mb-1">Dokumen Wajib</label>
                        <span class="text-xs text-gray-500 block mb-2">Pilih dokumen yang wajib dilampirkan pemohon.</span>
                        
                        @php
                            $commonDocs = ['KTP', 'KK', 'SKTM', 'Proposal', 'Foto Rumah', 'Surat Keterangan Usaha', 'RAB'];
                            $currentDocs = old('required_documents', $program->required_documents) ?? [];
                        @endphp

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 bg-slate-50 p-4 rounded border">
                            @foreach ($commonDocs as $doc)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="required_documents[]" value="{{ $doc }}" 
                                        @checked(in_array($doc, $currentDocs))>
                                    <span class="text-sm">{{ $doc }}</span>
                                </label>
                            @endforeach
                        </div>
                        
                        <div class="mt-2 text-xs text-gray-400">
                             * Dokumen "Lainnya" selalu tersedia secara default di formulir.
                        </div>
                    </div>

                    <div>
                        <label class="text-sm text-slate-600">Jenis penerima</label>
                        <select name="allowed_recipient_type" class="mt-1 w-full border rounded px-3 py-2">
                            @foreach (['individual'=>'Perorangan','organization'=>'Lembaga','both'=>'Keduanya'] as $key=>$label)
                                <option value="{{ $key }}" @selected(old('allowed_recipient_type', $program->allowed_recipient_type)==$key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Cakupan/Wilayah</label>
                        <input name="coverage_scope" value="{{ old('coverage_scope', $program->coverage_scope) }}" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                        <label class="text-sm text-slate-600">Aktif</label>
                    </div>
                    <div class="flex gap-3">
                        <button class="px-4 py-2 bg-emerald-600 text-white rounded">Simpan</button>
                        <a href="{{ route('laz.programs.index') }}" class="px-4 py-2 border rounded">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
