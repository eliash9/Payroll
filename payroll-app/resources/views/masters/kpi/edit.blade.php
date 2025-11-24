<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit KPI</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('kpi.update', $kpi->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id', $kpi->company_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Nama</label>
                        <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $kpi->name) }}">
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="code" class="w-full border rounded px-3 py-2" required value="{{ old('code', $kpi->code) }}">
                    </div>
                    <div>
                        <label class="text-sm">Tipe Nilai</label>
                        <select name="type" class="w-full border rounded px-3 py-2" required>
                            @foreach(['numeric','percent','boolean'] as $type)
                                <option value="{{ $type }}" @selected(old('type', $kpi->type) === $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Target Default</label>
                        <input name="target_default" type="number" step="0.01" class="w-full border rounded px-3 py-2" value="{{ old('target_default', $kpi->target_default) }}">
                    </div>
                    <div>
                        <label class="text-sm">Bobot Default</label>
                        <input name="weight_default" type="number" step="0.01" class="w-full border rounded px-3 py-2" value="{{ old('weight_default', $kpi->weight_default) }}">
                    </div>
                    <div>
                        <label class="text-sm">Periode</label>
                        <select name="period_type" class="w-full border rounded px-3 py-2" required>
                            @foreach(['monthly','weekly','quarterly','yearly'] as $period)
                                <option value="{{ $period }}" @selected(old('period_type', $kpi->period_type) === $period)>{{ ucfirst($period) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Kategori</label>
                        <select name="category" class="w-full border rounded px-3 py-2" required>
                            @foreach(['individual','team','division'] as $cat)
                                <option value="{{ $cat }}" @selected(old('category', $kpi->category) === $cat)>{{ ucfirst($cat) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-sm">Deskripsi</label>
                    <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $kpi->description) }}</textarea>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('kpi.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
