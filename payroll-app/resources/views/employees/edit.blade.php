<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Karyawan</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('employees.update', $employee->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" id="company_id" class="w-full border rounded px-3 py-2" required>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}" @selected(old('company_id', $employee->company_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="employee_code" class="w-full border rounded px-3 py-2" required value="{{ $employee->employee_code }}">
                    </div>
                    <div>
                        <label class="text-sm">Nama</label>
                        <input name="full_name" class="w-full border rounded px-3 py-2" required value="{{ $employee->full_name }}">
                    </div>
                    <div>
                        <label class="text-sm">Email</label>
                        <input name="email" class="w-full border rounded px-3 py-2" type="email" value="{{ $employee->email }}">
                    </div>
                    <div>
                        <label class="text-sm">Cabang</label>
                        <select name="branch_id" id="branch_id" class="w-full border rounded px-3 py-2">
                            <option value="">-</option>
                            @foreach($branches as $id => $name)
                                <option value="{{ $id }}" @selected($employee->branch_id==$id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Departemen</label>
                        <select name="department_id" id="department_id" class="w-full border rounded px-3 py-2">
                            <option value="">-</option>
                            @foreach($departments as $id => $name)
                                <option value="{{ $id }}" @selected($employee->department_id==$id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Jabatan</label>
                        <select name="position_id" id="position_id" class="w-full border rounded px-3 py-2">
                            <option value="">-</option>
                            @foreach($positions as $id => $name)
                                <option value="{{ $id }}" @selected($employee->position_id==$id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm">Basic Salary</label>
                        <input name="basic_salary" type="number" step="0.01" class="w-full border rounded px-3 py-2" value="{{ $employee->basic_salary }}">
                    </div>
                    <div>
                        <label class="text-sm">Hourly Rate</label>
                        <input name="hourly_rate" type="number" step="0.01" class="w-full border rounded px-3 py-2" value="{{ $employee->hourly_rate }}">
                    </div>
                    <div>
                        <label class="text-sm">Commission %</label>
                        <input name="commission_rate" type="number" step="0.01" class="w-full border rounded px-3 py-2" value="{{ $employee->commission_rate }}">
                    </div>
                </div>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm">Status Karyawan</label>
                        <select name="status" class="w-full border rounded px-3 py-2" required>
                            @foreach(['active'=>'Aktif','inactive'=>'Nonaktif','suspended'=>'Suspended','terminated'=>'Terminated'] as $val=>$label)
                                <option value="{{ $val }}" @selected(old('status',$employee->status)===$val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Tipe Kepegawaian</label>
                        <select name="employment_type" class="w-full border rounded px-3 py-2" required>
                            @foreach(['permanent'=>'Permanent','contract'=>'Contract','intern'=>'Intern','outsourcing'=>'Outsourcing'] as $val=>$label)
                                <option value="{{ $val }}" @selected(old('employment_type',$employee->employment_type)===$val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input type="checkbox" name="is_volunteer" value="1" id="is_volunteer" @checked(old('is_volunteer', $employee->is_volunteer))>
                        <label for="is_volunteer" class="text-sm">Relawan</label>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('employees.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const companySelect = document.getElementById('company_id');
        const branchSelect = document.getElementById('branch_id');
        const deptSelect = document.getElementById('department_id');
        const positionSelect = document.getElementById('position_id');

        async function loadOptions(endpoint, selectEl, selectedId) {
            selectEl.innerHTML = '<option value=\"\">Memuat...</option>';
            const res = await fetch(endpoint);
            const data = await res.json();
            selectEl.innerHTML = '<option value=\"\">-</option>';
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = item.name;
                if (selectedId && Number(selectedId) === Number(item.id)) opt.selected = true;
                selectEl.appendChild(opt);
            });
        }

        async function onCompanyChange() {
            const companyId = companySelect.value;
            if (!companyId) {
                branchSelect.innerHTML = '<option value=\"\">-</option>';
                deptSelect.innerHTML = '<option value=\"\">-</option>';
                positionSelect.innerHTML = '<option value=\"\">-</option>';
                return;
            }
            await loadOptions(`/api/companies/${companyId}/branches`, branchSelect, '{{ $employee->branch_id }}');
            await loadOptions(`/api/companies/${companyId}/departments`, deptSelect, '{{ $employee->department_id }}');
            await loadOptions(`/api/companies/${companyId}/positions`, positionSelect, '{{ $employee->position_id }}');
        }

        companySelect?.addEventListener('change', onCompanyChange);
        onCompanyChange();
    </script>
    @endpush
</x-app-layout>
