<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pengguna</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                @if($potentialUsers->isNotEmpty())
                <div class="bg-blue-50 p-4 rounded border border-blue-100 mb-4">
                    <label class="text-sm font-semibold text-blue-800">Pilih dari Karyawan (Opsional)</label>
                    <select id="employeeSelect" class="w-full border rounded px-3 py-2 mt-1 text-sm">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($potentialUsers as $emp)
                            <option value="{{ json_encode([
                                'name' => $emp->full_name,
                                'email' => $emp->email,
                                'company_id' => $emp->company_id,
                                'branch_id' => $emp->branch_id
                            ]) }}">
                                {{ $emp->full_name }} ({{ $emp->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-blue-600 mt-1">Memilih karyawan akan otomatis mengisi nama, email, perusahaan, dan cabang.</p>
                </div>
                @endif

                <div>
                    <label class="text-sm">Nama</label>
                    <input id="nameInput" name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name') }}">
                </div>
                <div>
                    <label class="text-sm">Email</label>
                    <input id="emailInput" name="email" type="email" class="w-full border rounded px-3 py-2" required value="{{ old('email') }}">
                </div>
                <div>
                    <label class="text-sm">Company</label>
                    <select id="companySelect" name="company_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">--Pilih company--</option>
                        @foreach($companies as $id => $name)
                            <option value="{{ $id }}" @selected(old('company_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm">Branch (Cabang)</label>
                    <select id="branchSelect" name="branch_id" class="w-full border rounded px-3 py-2">
                        <option value="">--Pilih cabang--</option>
                        @foreach($branches as $id => $name)
                            <option value="{{ $id }}" @selected(old('branch_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm">LAZ Roles</label>
                    <div class="grid grid-cols-2 gap-2 mt-1 border rounded p-3 bg-gray-50">
                        @foreach($lazRoles as $id => $name)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="laz_roles[]" value="{{ $id }}" @checked(in_array($id, old('laz_roles', [])))>
                                <span class="text-sm">{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="text-sm">Role</label>
                    <select name="role" class="w-full border rounded px-3 py-2">
                        @foreach(['admin','hr','manager','volunteer'] as $role)
                            <option value="{{ $role }}" @selected(old('role','admin') === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm">Password</label>
                    <input name="password" type="password" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('users.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
    </div>
    
    <script>
        document.getElementById('employeeSelect')?.addEventListener('change', function() {
            const val = this.value;
            if (val) {
                const data = JSON.parse(val);
                document.getElementById('nameInput').value = data.name;
                document.getElementById('emailInput').value = data.email;
                
                const companySelect = document.getElementById('companySelect');
                if (data.company_id) {
                    companySelect.value = data.company_id;
                }
                
                const branchSelect = document.getElementById('branchSelect');
                if (data.branch_id) {
                    branchSelect.value = data.branch_id;
                }
            } else {
                // Optional: Clear fields if reset
                document.getElementById('nameInput').value = '';
                document.getElementById('emailInput').value = '';
                document.getElementById('companySelect').value = '';
                document.getElementById('branchSelect').value = '';
            }
        });
    </script>
</x-app-layout>
