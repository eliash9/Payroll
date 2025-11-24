<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Shift</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('shifts.store') }}" class="space-y-4">
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
                        <label class="text-sm">Nama</label>
                        <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name') }}">
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code') }}">
                    </div>
                    <div>
                        <label class="text-sm">Mulai</label>
                        <input name="start_time" type="time" class="w-full border rounded px-3 py-2" required value="{{ old('start_time') }}">
                    </div>
                    <div>
                        <label class="text-sm">Selesai</label>
                        <input name="end_time" type="time" class="w-full border rounded px-3 py-2" required value="{{ old('end_time') }}">
                    </div>
                    <div>
                        <label class="text-sm">Toleransi Telat (menit)</label>
                        <input name="tolerance_late_minutes" type="number" min="0" class="w-full border rounded px-3 py-2" value="{{ old('tolerance_late_minutes', 0) }}">
                    </div>
                    <div>
                        <label class="text-sm">Toleransi Pulang Awal (menit)</label>
                        <input name="tolerance_early_leave_minutes" type="number" min="0" class="w-full border rounded px-3 py-2" value="{{ old('tolerance_early_leave_minutes', 0) }}">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_night_shift" value="1" id="is_night_shift" @checked(old('is_night_shift'))>
                    <label for="is_night_shift" class="text-sm">Shift Malam</label>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('shifts.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
