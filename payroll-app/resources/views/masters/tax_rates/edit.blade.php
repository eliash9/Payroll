<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Tarif Pajak</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('tax-rates.update', $taxRate->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id', $taxRate->company_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Tahun</label>
                        <input name="year" type="number" min="2000" class="w-full border rounded px-3 py-2" required value="{{ old('year', $taxRate->year) }}">
                    </div>
                    <div>
                        <label class="text-sm">Range Min</label>
                        <input name="range_min" type="number" min="0" class="w-full border rounded px-3 py-2" required value="{{ old('range_min', $taxRate->range_min) }}">
                    </div>
                    <div>
                        <label class="text-sm">Range Max</label>
                        <input name="range_max" type="number" min="0" class="w-full border rounded px-3 py-2" value="{{ old('range_max', $taxRate->range_max) }}">
                    </div>
                    <div>
                        <label class="text-sm">Tarif %</label>
                        <input name="rate_percent" type="number" step="0.01" min="0" class="w-full border rounded px-3 py-2" required value="{{ old('rate_percent', $taxRate->rate_percent) }}">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('tax-rates.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
