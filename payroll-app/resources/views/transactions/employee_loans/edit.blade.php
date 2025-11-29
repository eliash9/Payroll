<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Pinjaman</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4 space-y-4">
            <div>
                <div class="text-sm text-slate-600">Karyawan</div>
                <div class="font-semibold">
                    @php
                        $emp = $employees->firstWhere('id', $loan->employee_id);
                    @endphp
                    {{ $emp?->full_name }} ({{ $emp?->employee_code }})
                </div>
            </div>
            <form method="post" action="{{ route('employee-loans.update', $loan->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Nomor Pinjaman</label>
                        <input name="loan_number" class="w-full border rounded px-3 py-2" required value="{{ old('loan_number', $loan->loan_number) }}">
                    </div>
                    <div>
                        <label class="text-sm">Status</label>
                        <select name="status" class="w-full border rounded px-3 py-2" required>
                            @foreach(['active'=>'Aktif','completed'=>'Lunas','cancelled'=>'Batal'] as $val=>$label)
                                <option value="{{ $val }}" @selected(old('status', $loan->status)===$val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Cicilan per Periode</label>
                        <input name="installment_amount" type="number" step="0.01" class="w-full border rounded px-3 py-2 text-right" required value="{{ old('installment_amount', $loan->installment_amount) }}">
                    </div>
                    <div>
                        <label class="text-sm">Sisa Pokok</label>
                        <input name="remaining_amount" type="number" step="0.01" class="w-full border rounded px-3 py-2 text-right" required value="{{ old('remaining_amount', $loan->remaining_amount) }}">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('employee-loans.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
