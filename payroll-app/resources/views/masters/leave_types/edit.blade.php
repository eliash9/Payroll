<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Jenis Cuti/Izin</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('leave-types.update', $leaveType->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id', $leaveType->company_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Nama</label>
                        <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $leaveType->name) }}">
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code', $leaveType->code) }}">
                    </div>
                    <div>
                        <label class="text-sm">Quota Default (hari)</label>
                        <input name="default_quota_days" class="w-full border rounded px-3 py-2" type="number" step="0.01" min="0" value="{{ old('default_quota_days', $leaveType->default_quota_days) }}">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_paid" value="1" @checked(old('is_paid', $leaveType->is_paid))>
                        Berbayar
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_annual_quota" value="1" @checked(old('is_annual_quota', $leaveType->is_annual_quota))>
                        Potong Cuti Tahunan
                    </label>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('leave-types.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
