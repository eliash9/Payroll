<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Komponen Payroll</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('payroll-components.store') }}" class="space-y-4">
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
                        <input name="code" class="w-full border rounded px-3 py-2" required value="{{ old('code') }}">
                    </div>
                    <div>
                        <label class="text-sm">Tipe</label>
                        <select name="type" class="w-full border rounded px-3 py-2" required>
                            <option value="earning" @selected(old('type') === 'earning')>Earning</option>
                            <option value="deduction" @selected(old('type') === 'deduction')>Deduction</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Kategori</label>
                        <select name="category" class="w-full border rounded px-3 py-2" required>
                            @foreach(['fixed','variable','kpi','bpjs','tax','loan','other'] as $cat)
                                <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ strtoupper($cat) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Metode Perhitungan</label>
                        <select name="calculation_method" class="w-full border rounded px-3 py-2" required>
                            @foreach(['manual','formula','attendance_based','kpi_based'] as $method)
                                <option value="{{ $method }}" @selected(old('calculation_method') === $method)>{{ str_replace('_',' ', ucfirst($method)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Sequence</label>
                        <input name="sequence" type="number" class="w-full border rounded px-3 py-2" value="{{ old('sequence', 0) }}">
                    </div>
                </div>
                <div>
                    <label class="text-sm">Formula (opsional)</label>
                    <textarea name="formula" class="w-full border rounded px-3 py-2">{{ old('formula') }}</textarea>
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="is_taxable" value="1" @checked(old('is_taxable', true))>
                        Kena Pajak
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="show_in_payslip" value="1" @checked(old('show_in_payslip', true))>
                        Tampil di Slip
                    </label>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('payroll-components.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
