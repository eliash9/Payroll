<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pinjaman Karyawan</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto">
        @if(session('error'))
            <div class="mb-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded px-3 py-2">
                {{ session('error') }}
            </div>
        @endif
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('employee-loans.store') }}" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Pilih</option>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id')==$id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Karyawan</label>
                        <select name="employee_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Pilih</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" @selected(old('employee_id')==$emp->id)>
                                    {{ $emp->full_name }} ({{ $emp->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Nomor Pinjaman</label>
                        <input name="loan_number" class="w-full border rounded px-3 py-2" required value="{{ old('loan_number') }}">
                    </div>
                    <div>
                        <label class="text-sm">Nominal Pokok</label>
                        <input name="principal_amount" type="number" step="0.01" class="w-full border rounded px-3 py-2" required value="{{ old('principal_amount') }}">
                    </div>
                    <div>
                        <label class="text-sm">Cicilan per Periode</label>
                        <input name="installment_amount" type="number" step="0.01" class="w-full border rounded px-3 py-2" required value="{{ old('installment_amount') }}">
                    </div>
                    <div>
                        <label class="text-sm">Tenor (jumlah periode)</label>
                        <input name="tenor_months" type="number" class="w-full border rounded px-3 py-2" required value="{{ old('tenor_months') }}">
                    </div>
                    <div>
                        <label class="text-sm">Mulai Periode Payroll (ID)</label>
                        <select name="start_period_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Pilih</option>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" @selected(old('start_period_id')==$period->id)>
                                    {{ $period->code }} - {{ $period->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Periode yang dipilih akan menjadi cicilan pertama.</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan & Generate Jadwal</button>
                    <a href="{{ route('employee-loans.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
