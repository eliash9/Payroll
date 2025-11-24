<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit KPI Karyawan</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('employee-kpi.update', $assignment->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Karyawan</label>
                        <select name="employee_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($employees as $id => $name)
                                <option value="{{ $id }}" @selected(old('employee_id', $assignment->employee_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">KPI</label>
                        <select name="kpi_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($kpis as $id => $name)
                                <option value="{{ $id }}" @selected(old('kpi_id', $assignment->kpi_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Target</label>
                        <input name="target" type="number" step="0.01" class="w-full border rounded px-3 py-2" value="{{ old('target', $assignment->target) }}">
                    </div>
                    <div>
                        <label class="text-sm">Bobot</label>
                        <input name="weight" type="number" step="0.01" class="w-full border rounded px-3 py-2" value="{{ old('weight', $assignment->weight) }}">
                    </div>
                    <div>
                        <label class="text-sm">Mulai</label>
                        <input name="start_date" type="date" class="w-full border rounded px-3 py-2" required value="{{ old('start_date', $assignment->start_date) }}">
                    </div>
                    <div>
                        <label class="text-sm">Selesai</label>
                        <input name="end_date" type="date" class="w-full border rounded px-3 py-2" value="{{ old('end_date', $assignment->end_date) }}">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('employee-kpi.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
