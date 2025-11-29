<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Tarif BPJS</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('bpjs-rates.store') }}" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Pilih</option>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Program</label>
                        <select name="program" class="w-full border rounded px-3 py-2" required>
                            @foreach(['bpjs_kesehatan','jht','jkk','jkm','jp'] as $program)
                                <option value="{{ $program }}" @selected(old('program') === $program)>{{ strtoupper($program) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Persen Perusahaan</label>
                        <input name="employer_rate" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2 text-right" required value="{{ old('employer_rate') }}">
                    </div>
                    <div>
                        <label class="text-sm">Persen Karyawan</label>
                        <input name="employee_rate" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2 text-right" required value="{{ old('employee_rate') }}">
                    </div>
                    <div>
                        <label class="text-sm">Cap Min</label>
                        <input name="salary_cap_min" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2 text-right" value="{{ old('salary_cap_min') }}">
                    </div>
                    <div>
                        <label class="text-sm">Cap Max</label>
                        <input name="salary_cap_max" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2 text-right" value="{{ old('salary_cap_max') }}">
                    </div>
                    <div>
                        <label class="text-sm">Berlaku Dari</label>
                        <input name="effective_from" type="date" class="w-full border rounded px-3 py-2" required value="{{ old('effective_from') }}">
                    </div>
                    <div>
                        <label class="text-sm">Berlaku Sampai</label>
                        <input name="effective_to" type="date" class="w-full border rounded px-3 py-2" value="{{ old('effective_to') }}">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('bpjs-rates.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
