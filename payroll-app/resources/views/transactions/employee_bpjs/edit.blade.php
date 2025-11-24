<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit BPJS Karyawan</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('employee-bpjs.update', $bpjs->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Karyawan</label>
                        <select name="employee_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($employees as $id => $name)
                                <option value="{{ $id }}" @selected(old('employee_id', $bpjs->employee_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">BPJS Kesehatan</label>
                        <input name="bpjs_kesehatan_number" class="w-full border rounded px-3 py-2" value="{{ old('bpjs_kesehatan_number', $bpjs->bpjs_kesehatan_number) }}">
                    </div>
                    <div>
                        <label class="text-sm">Kelas BPJS</label>
                        <input name="bpjs_kesehatan_class" class="w-full border rounded px-3 py-2" value="{{ old('bpjs_kesehatan_class', $bpjs->bpjs_kesehatan_class) }}">
                    </div>
                    <div>
                        <label class="text-sm">BPJS Ketenagakerjaan</label>
                        <input name="bpjs_ketenagakerjaan_number" class="w-full border rounded px-3 py-2" value="{{ old('bpjs_ketenagakerjaan_number', $bpjs->bpjs_ketenagakerjaan_number) }}">
                    </div>
                    <div>
                        <label class="text-sm">Mulai Berlaku</label>
                        <input name="start_date" type="date" class="w-full border rounded px-3 py-2" value="{{ old('start_date', $bpjs->start_date) }}">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $bpjs->is_active))>
                    <label for="is_active" class="text-sm">Aktif</label>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('employee-bpjs.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
