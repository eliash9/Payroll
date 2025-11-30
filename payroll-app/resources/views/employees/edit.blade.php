<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Karyawan</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto">
        <div class="bg-white shadow-lg rounded-xl overflow-hidden" x-data="{ step: 1 }">
            
            <!-- Stepper Header -->
            <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                <div class="flex items-center justify-between max-w-2xl mx-auto">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center cursor-pointer" @click="step = 1">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-colors"
                             :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500'">1</div>
                        <span class="text-xs font-medium mt-2" :class="step >= 1 ? 'text-blue-600' : 'text-slate-500'">Data Pribadi</span>
                    </div>
                    <div class="h-1 flex-1 mx-4 rounded" :class="step >= 2 ? 'bg-blue-600' : 'bg-slate-200'"></div>
                    
                    <!-- Step 2 -->
                    <div class="flex flex-col items-center cursor-pointer" @click="step = 2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-colors"
                             :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500'">2</div>
                        <span class="text-xs font-medium mt-2" :class="step >= 2 ? 'text-blue-600' : 'text-slate-500'">Kontak & Alamat</span>
                    </div>
                    <div class="h-1 flex-1 mx-4 rounded" :class="step >= 3 ? 'bg-blue-600' : 'bg-slate-200'"></div>

                    <!-- Step 3 -->
                    <div class="flex flex-col items-center cursor-pointer" @click="step = 3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-colors"
                             :class="step >= 3 ? 'bg-blue-600 text-white' : 'bg-slate-200 text-slate-500'">3</div>
                        <span class="text-xs font-medium mt-2" :class="step >= 3 ? 'text-blue-600' : 'text-slate-500'">Kepegawaian</span>
                    </div>
                </div>
            </div>

            <form method="post" action="{{ route('employees.update', $employee->id) }}" class="p-6">
                @csrf
                @method('put')
                
                <!-- STEP 1: Data Pribadi -->
                <div x-show="step === 1" class="space-y-6">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Identitas Pribadi</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input name="full_name" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required value="{{ old('full_name', $employee->full_name) }}">
                            @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Panggilan (Nickname)</label>
                            <input name="nickname" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('nickname', $employee->nickname) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">NIK (KTP)</label>
                            <input name="national_id_number" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('national_id_number', $employee->national_id_number) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">No. Kartu Keluarga</label>
                            <input name="family_card_number" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('family_card_number', $employee->family_card_number) }}">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tempat Lahir</label>
                                <input name="birth_place" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('birth_place', $employee->birth_place) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                                <input name="birth_date" type="date" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('birth_date', $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('Y-m-d') : '') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin</label>
                                <select name="gender" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih</option>
                                    <option value="male" @selected(old('gender', $employee->gender) == 'male')>Laki-laki</option>
                                    <option value="female" @selected(old('gender', $employee->gender) == 'female')>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Status Pernikahan</label>
                                <select name="marital_status" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Pilih</option>
                                    <option value="single" @selected(old('marital_status', $employee->marital_status) == 'single')>Lajang</option>
                                    <option value="married" @selected(old('marital_status', $employee->marital_status) == 'married')>Menikah</option>
                                    <option value="divorced" @selected(old('marital_status', $employee->marital_status) == 'divorced')>Cerai Hidup</option>
                                    <option value="widowed" @selected(old('marital_status', $employee->marital_status) == 'widowed')>Cerai Mati</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Kontak & Alamat -->
                <div x-show="step === 2" class="space-y-6" style="display: none;">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Kontak & Alamat</h3>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input name="email" type="email" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('email', $employee->email) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">No. HP / WhatsApp</label>
                            <input name="phone" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('phone', $employee->phone) }}">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Lengkap (KTP)</label>
                            <textarea name="address" rows="3" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('address', $employee->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Kepegawaian -->
                <div x-show="step === 3" class="space-y-6" style="display: none;">
                    <h3 class="text-lg font-semibold text-slate-800 border-b pb-2">Data Kepegawaian</h3>
                    
                    <div class="bg-amber-50 p-4 rounded-lg border border-amber-100 mb-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            <p class="text-sm text-amber-800 font-medium">Perhatian</p>
                            <p class="text-xs text-amber-700 mt-1">Untuk perubahan Jabatan, Cabang, atau Gaji, disarankan menggunakan menu <strong>Mutasi / SK Baru</strong> di halaman Detail Karyawan agar tercatat dalam riwayat karir.</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Perusahaan <span class="text-red-500">*</span></label>
                                <select name="company_id" id="company_id" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Pilih Perusahaan</option>
                                    @foreach($companies as $id => $name)
                                        <option value="{{ $id }}" @selected(old('company_id', $employee->company_id) == $id)>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">NIP / Kode Karyawan <span class="text-red-500">*</span></label>
                                <input name="employee_code" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required value="{{ old('employee_code', $employee->employee_code) }}">
                                @error('employee_code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Cabang</label>
                            <select name="branch_id" id="branch_id" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">- Pilih Perusahaan Dulu -</option>
                                @foreach($branches as $id => $name)
                                    <option value="{{ $id }}" @selected($employee->branch_id == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Departemen</label>
                            <select name="department_id" id="department_id" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">- Pilih Perusahaan Dulu -</option>
                                @foreach($departments as $id => $name)
                                    <option value="{{ $id }}" @selected($employee->department_id == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan</label>
                            <select name="position_id" id="position_id" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                <option value="">- Pilih Perusahaan Dulu -</option>
                                @foreach($positions as $id => $name)
                                    <option value="{{ $id }}" @selected($employee->position_id == $id)>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status Karyawan <span class="text-red-500">*</span></label>
                            <select name="status" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                @foreach(['active'=>'Aktif','inactive'=>'Nonaktif','suspended'=>'Suspended','terminated'=>'Terminated'] as $val=>$label)
                                    <option value="{{ $val }}" @selected(old('status', $employee->status)===$val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Kontrak <span class="text-red-500">*</span></label>
                            <select name="employment_type" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                @foreach(['permanent'=>'Permanent','contract'=>'Contract','intern'=>'Intern','outsourcing'=>'Outsourcing'] as $val=>$label)
                                    <option value="{{ $val }}" @selected(old('employment_type', $employee->employment_type)===$val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Bergabung</label>
                            <input name="join_date" type="date" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('join_date', $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-6 pt-4 border-t">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Gaji Pokok (Basic Salary)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-500">Rp</span>
                                <input name="basic_salary" type="number" step="0.01" class="w-full pl-10 border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('basic_salary', $employee->basic_salary) }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Hourly Rate (Relawan)</label>
                            <input name="hourly_rate" type="number" step="0.01" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('hourly_rate', $employee->hourly_rate) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Komisi %</label>
                            <input name="commission_rate" type="number" step="0.01" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" value="{{ old('commission_rate', $employee->commission_rate) }}">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-4">
                        <input type="checkbox" name="is_volunteer" value="1" id="is_volunteer" @checked(old('is_volunteer', $employee->is_volunteer)) class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <label for="is_volunteer" class="text-sm font-medium text-slate-700">Tandai sebagai Relawan / Fundraiser</label>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center justify-between mt-8 pt-6 border-t border-slate-100">
                    <button type="button" x-show="step > 1" @click="step--" class="px-6 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-medium transition-colors">
                        Kembali
                    </button>
                    <div class="flex-1"></div> <!-- Spacer -->
                    
                    <button type="button" x-show="step < 3" @click="step++" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors shadow-lg shadow-blue-600/20">
                        Lanjut
                    </button>
                    
                    <button type="submit" x-show="step === 3" class="px-8 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold transition-colors shadow-lg shadow-emerald-600/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        const companySelect = document.getElementById('company_id');
        const branchSelect = document.getElementById('branch_id');
        const deptSelect = document.getElementById('department_id');
        const positionSelect = document.getElementById('position_id');

        async function loadOptions(endpoint, selectEl, selectedId) {
            selectEl.innerHTML = '<option value=\"\">Memuat...</option>';
            try {
                const res = await fetch(endpoint);
                const data = await res.json();
                selectEl.innerHTML = '<option value=\"\">- Pilih -</option>';
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.name;
                    if (selectedId && Number(selectedId) === Number(item.id)) opt.selected = true;
                    selectEl.appendChild(opt);
                });
            } catch (e) {
                selectEl.innerHTML = '<option value=\"\">Gagal memuat</option>';
            }
        }

        async function onCompanyChange() {
            const companyId = companySelect.value;
            if (!companyId) {
                branchSelect.innerHTML = '<option value=\"\">- Pilih Perusahaan Dulu -</option>';
                deptSelect.innerHTML = '<option value=\"\">- Pilih Perusahaan Dulu -</option>';
                positionSelect.innerHTML = '<option value=\"\">- Pilih Perusahaan Dulu -</option>';
                return;
            }
            // Only load if not already populated by server-side render (which we did manually in the loop, but for dynamic changes we need this)
            // Actually, for edit, we might want to reload only if changed. But the logic here is simple.
            // To avoid clearing existing selections on page load, we check if options are empty or just one.
            // But since we pre-populated in Blade, we might not need to fetch immediately unless company changes.
            // However, the original script called onCompanyChange() immediately.
            // Let's modify it to only fetch if the select is empty (which it won't be).
            // Better approach: The Blade loop populates the initial state. This script handles *changes*.
        }

        companySelect?.addEventListener('change', async () => {
             const companyId = companySelect.value;
             if (!companyId) return;
             await loadOptions(`/api/companies/${companyId}/branches`, branchSelect);
             await loadOptions(`/api/companies/${companyId}/departments`, deptSelect);
             await loadOptions(`/api/companies/${companyId}/positions`, positionSelect);
        });
    </script>
    @endpush
</x-app-layout>
