<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Cabang</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('branches.update', $branch->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id', $branch->company_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Nama</label>
                        <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $branch->name) }}">
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code', $branch->code) }}">
                    </div>
                    <div>
                        <label class="text-sm">Telepon</label>
                        <input name="phone" class="w-full border rounded px-3 py-2" value="{{ old('phone', $branch->phone) }}">
                    </div>
                    <div>
                        <label class="text-sm">Latitude</label>
                        <input name="latitude" type="number" step="any" class="w-full border rounded px-3 py-2" value="{{ old('latitude', $branch->latitude) }}">
                    </div>
                    <div>
                        <label class="text-sm">Longitude</label>
                        <input name="longitude" type="number" step="any" class="w-full border rounded px-3 py-2" value="{{ old('longitude', $branch->longitude) }}">
                    </div>
                    <div>
                        <label class="text-sm">Grade</label>
                        <select name="grade" class="w-full border rounded px-3 py-2">
                            <option value="">Pilih Grade</option>
                            <option value="A" @selected(old('grade', $branch->grade) == 'A')>A</option>
                            <option value="B" @selected(old('grade', $branch->grade) == 'B')>B</option>
                            <option value="C" @selected(old('grade', $branch->grade) == 'C')>C</option>
                            <option value="D" @selected(old('grade', $branch->grade) == 'D')>D</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-sm">Alamat</label>
                    <textarea name="address" class="w-full border rounded px-3 py-2">{{ old('address', $branch->address) }}</textarea>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('branches.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
