<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Tarif BPJS</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('bpjs-rates.update', $bpjsRate->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id', $bpjsRate->company_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Program</label>
                        <select name="program" class="w-full border rounded px-3 py-2" required>
                            @foreach(['bpjs_kesehatan','jht','jkk','jkm','jp'] as $program)
                                <option value="{{ $program }}" @selected(old('program', $bpjsRate->program) === $program)>{{ strtoupper($program) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Persen Perusahaan</label>
                        <input name="employer_rate" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2" required value="{{ old('employer_rate', $bpjsRate->employer_rate) }}">
                    </div>
                    <div>
                        <label class="text-sm">Persen Karyawan</label>
                        <input name="employee_rate" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2" required value="{{ old('employee_rate', $bpjsRate->employee_rate) }}">
                    </div>
                    <div>
                        <label class="text-sm">Cap Min</label>
                        <input name="salary_cap_min" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2" value="{{ old('salary_cap_min', $bpjsRate->salary_cap_min) }}">
                    </div>
                    <div>
                        <label class="text-sm">Cap Max</label>
                        <input name="salary_cap_max" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2" value="{{ old('salary_cap_max', $bpjsRate->salary_cap_max) }}">
                    </div>
                    <div>
                        <label class="text-sm">Berlaku Dari</label>
                        <input name="effective_from" type="date" class="w-full border rounded px-3 py-2" required value="{{ old('effective_from', \Illuminate\Support\Str::of($bpjsRate->effective_from)->substr(0,10)) }}">
                    </div>
                    <div>
                        <label class="text-sm">Berlaku Sampai</label>
                        <input name="effective_to" type="date" class="w-full border rounded px-3 py-2" value="{{ old('effective_to', $bpjsRate->effective_to ? \Illuminate\Support\Str::of($bpjsRate->effective_to)->substr(0,10) : '') }}">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('bpjs-rates.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
