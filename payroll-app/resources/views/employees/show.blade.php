<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">File Karyawan</h2>
            <div class="flex gap-2">
                <a href="{{ route('employees.edit', $employee->id) }}" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 text-sm font-medium hover:bg-slate-50">
                    Edit Biodata
                </a>
                <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-medium hover:bg-slate-700">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Profile Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6 flex flex-col md:flex-row items-start md:items-center gap-6">
            <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 text-3xl font-bold border-4 border-white shadow-md">
                {{ substr($employee->full_name, 0, 1) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-2xl font-bold text-slate-900">{{ $employee->full_name }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wide
                        {{ $employee->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $employee->status }}
                    </span>
                </div>
                <div class="text-slate-500 text-sm space-y-1">
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        {{ $employee->position->name ?? '-' }} &bull; {{ $employee->department->name ?? '-' }}
                    </p>
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $employee->branch->name ?? 'Pusat' }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">NIP / ID</p>
                <p class="text-lg font-mono font-bold text-slate-700">{{ $employee->employee_code }}</p>
                <p class="text-xs text-slate-500 mt-2">Bergabung: {{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}</p>
            </div>
        </div>

        <div x-data="{ tab: 'profile' }">
            <!-- Tabs Navigation -->
            <div class="border-b border-slate-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button @click="tab = 'profile'" :class="tab === 'profile' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Biodata Lengkap
                    </button>
                    <button @click="tab = 'career'" :class="tab === 'career' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Karir & Mutasi
                    </button>
                    <button @click="tab = 'payroll'" :class="tab === 'payroll' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Payroll Info
                    </button>
                    <button @click="tab = 'education'" :class="tab === 'education' ? 'border-blue-500 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Pendidikan & Sertifikasi
                    </button>
                </nav>
            </div>

            <!-- Tab Content: Profile -->
            <div x-show="tab === 'profile'" class="grid lg:grid-cols-3 gap-8">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Informasi Pribadi</h3>
                        <dl class="grid sm:grid-cols-2 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Nama Lengkap</dt>
                                <dd class="mt-1 text-sm text-slate-900 font-medium">{{ $employee->full_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Nama Panggilan</dt>
                                <dd class="mt-1 text-sm text-slate-900">{{ $employee->nickname ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500">NIK (KTP)</dt>
                                <dd class="mt-1 text-sm text-slate-900">{{ $employee->national_id_number ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500">No. KK</dt>
                                <dd class="mt-1 text-sm text-slate-900">{{ $employee->family_card_number ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Tempat, Tgl Lahir</dt>
                                <dd class="mt-1 text-sm text-slate-900">
                                    {{ $employee->birth_place ?? '-' }}, 
                                    {{ $employee->birth_date ? \Carbon\Carbon::parse($employee->birth_date)->format('d M Y') : '-' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Jenis Kelamin</dt>
                                <dd class="mt-1 text-sm text-slate-900 capitalize">{{ $employee->gender ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Status Pernikahan</dt>
                                <dd class="mt-1 text-sm text-slate-900 capitalize">{{ $employee->marital_status ?? '-' }}</dd>
                            </div>
                        </dl>
                    </section>

                    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Kontak & Alamat</h3>
                        <dl class="grid sm:grid-cols-2 gap-x-4 gap-y-6">
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Email</dt>
                                <dd class="mt-1 text-sm text-slate-900">{{ $employee->email ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500">No. HP</dt>
                                <dd class="mt-1 text-sm text-slate-900">{{ $employee->phone ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-slate-500">Alamat Lengkap</dt>
                                <dd class="mt-1 text-sm text-slate-900">{{ $employee->address ?? '-' }}</dd>
                            </div>
                        </dl>
                    </section>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <section class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Status Kepegawaian</h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Tipe Kontrak</dt>
                                <dd class="mt-1 text-sm text-slate-900 capitalize font-semibold">{{ $employee->employment_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Tanggal Bergabung</dt>
                                <dd class="mt-1 text-sm text-slate-900">{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M Y') : '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-slate-500">Masa Kerja</dt>
                                <dd class="mt-1 text-sm text-slate-900">
                                    {{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->diffForHumans(null, true) : '-' }}
                                </dd>
                            </div>
                        </dl>
                    </section>
                </div>
            </div>

            <!-- Tab Content: Career History -->
            <div x-show="tab === 'career'" class="space-y-6">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-900">Riwayat Karir & Mutasi</h3>
                    <a href="{{ route('employees.mutations.create', $employee->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Buat Mutasi / SK Baru
                    </a>
                </div>

                <div class="relative border-l-2 border-slate-200 ml-3 space-y-8">
                    @forelse($careerHistories as $history)
                        <div class="relative pl-8">
                            <!-- Dot -->
                            <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-2 border-white 
                                {{ $history->type == 'promotion' ? 'bg-emerald-500' : ($history->type == 'demotion' ? 'bg-red-500' : 'bg-blue-500') }}">
                            </div>
                            
                            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2">
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-lg capitalize">{{ str_replace('_', ' ', $history->type) }}</h4>
                                        <p class="text-sm text-slate-500">No. SK: {{ $history->reference_number ?? '-' }}</p>
                                    </div>
                                    <span class="text-sm font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded">
                                        Efektif: {{ $history->effective_date->format('d M Y') }}
                                    </span>
                                </div>
                                
                                <div class="grid md:grid-cols-2 gap-4 mt-4 text-sm">
                                    <div class="bg-slate-50 p-3 rounded border border-slate-100">
                                        <p class="text-xs font-bold text-slate-400 uppercase mb-2">Sebelumnya</p>
                                        <p><span class="text-slate-500 w-20 inline-block">Jabatan:</span> <span class="font-medium">{{ $history->oldPosition->name ?? '-' }}</span></p>
                                        <p><span class="text-slate-500 w-20 inline-block">Cabang:</span> <span class="font-medium">{{ $history->oldBranch->name ?? '-' }}</span></p>
                                        <p><span class="text-slate-500 w-20 inline-block">Dept:</span> <span class="font-medium">{{ $history->oldDepartment->name ?? '-' }}</span></p>
                                    </div>
                                    <div class="bg-blue-50 p-3 rounded border border-blue-100">
                                        <p class="text-xs font-bold text-blue-400 uppercase mb-2">Menjadi</p>
                                        <p><span class="text-blue-500 w-20 inline-block">Jabatan:</span> <span class="font-medium text-slate-900">{{ $history->newPosition->name ?? '-' }}</span></p>
                                        <p><span class="text-blue-500 w-20 inline-block">Cabang:</span> <span class="font-medium text-slate-900">{{ $history->newBranch->name ?? '-' }}</span></p>
                                        <p><span class="text-blue-500 w-20 inline-block">Dept:</span> <span class="font-medium text-slate-900">{{ $history->newDepartment->name ?? '-' }}</span></p>
                                    </div>
                                </div>
                                
                                @if($history->notes)
                                    <div class="mt-3 pt-3 border-t border-slate-100">
                                        <p class="text-sm text-slate-600 italic">"{{ $history->notes }}"</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="pl-8">
                            <p class="text-slate-500 italic">Belum ada riwayat karir/mutasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tab Content: Payroll -->
            <div x-show="tab === 'payroll'" class="space-y-6">
                <!-- Basic Salary -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Informasi Dasar</h3>
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-500">Gaji Pokok</label>
                            <p class="text-xl font-bold text-slate-900">Rp {{ number_format($employee->basic_salary, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-500">Bank</label>
                            <p class="text-base font-medium text-slate-900">{{ $employee->bank_name ?? '-' }} - {{ $employee->bank_account_number ?? '-' }}</p>
                            <p class="text-xs text-slate-500">a.n {{ $employee->bank_account_holder ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Components -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-slate-900">Komponen Gaji Tambahan (Bisyarah)</h3>
                        <button onclick="document.getElementById('addComponentModal').showModal()" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                            + Tambah Komponen
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left">
                            <thead class="bg-slate-50 text-slate-500 uppercase font-medium">
                                <tr>
                                    <th class="px-4 py-3">Komponen</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3 text-right">Nominal / Rate</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($employee->payrollComponents as $comp)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-slate-900">
                                            {{ $comp->name }}
                                            <div class="text-xs text-slate-400">{{ $comp->category }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded text-xs font-bold {{ $comp->type == 'earning' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                                {{ ucfirst($comp->type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right font-mono">
                                            Rp {{ number_format($comp->pivot->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <form action="{{ route('employees.components.destroy', [$employee->id, $comp->id]) }}" method="POST" onsubmit="return confirm('Hapus komponen ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-slate-500 italic">Belum ada komponen tambahan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Add Component -->
            <dialog id="addComponentModal" class="modal rounded-lg shadow-xl p-0 w-full max-w-md backdrop:bg-gray-900/50">
                <div class="bg-white p-6">
                    <h3 class="font-bold text-lg mb-4">Tambah Komponen Gaji</h3>
                    <form action="{{ route('employees.components.store', $employee->id) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Komponen</label>
                                <select name="payroll_component_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="">Pilih Komponen...</option>
                                    @foreach(\App\Models\PayrollComponent::where('company_id', $employee->company_id)->orderBy('name')->get() as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ ucfirst($c->type) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal / Rate</label>
                                <input type="number" name="amount" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: 500000" required>
                                <p class="text-xs text-slate-500 mt-1">Masukkan nominal tetap bulanan atau rate per satuan (jika komponen variabel).</p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('addComponentModal').close()" class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </dialog>
             <!-- No extra closing divs here -->

            <!-- Tab Content: Education & Certification -->
            <div x-show="tab === 'education'" class="space-y-8">
                
                <!-- Education Section -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-slate-900">Riwayat Pendidikan</h3>
                        <button onclick="document.getElementById('addEducationModal').showModal()" class="px-3 py-1.5 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Pendidikan
                        </button>
                    </div>

                    <div class="relative border-l-2 border-slate-200 ml-3 space-y-8">
                         @forelse($employee->educations->sortByDesc('end_year') as $edu)
                            <div class="relative pl-8">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-2 border-white bg-blue-500"></div>
                                <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm group">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-lg">{{ $edu->institution_name }}</h4>
                                            <p class="text-slate-600">{{ $edu->degree }} {{ $edu->major ? '- ' . $edu->major : '' }}</p>
                                            <p class="text-sm text-slate-500 mt-1">
                                                {{ $edu->start_year }} - {{ $edu->end_year ?? 'Sekarang' }}
                                                @if($edu->gpa) 
                                                    &bull; IPK: {{ $edu->gpa }}
                                                @endif
                                            </p>
                                            @if($edu->notes)
                                                <p class="text-sm text-slate-500 italic mt-2">"{{ $edu->notes }}"</p>
                                            @endif
                                        </div>
                                        <form action="{{ route('employees.educations.destroy', [$employee->id, $edu->id]) }}" method="POST" onsubmit="return confirm('Hapus data pendidikan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="pl-8">
                                <p class="text-slate-500 italic">Belum ada data pendidikan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Certification Section -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-slate-900">Sertifikasi & Pelatihan</h3>
                        <button onclick="document.getElementById('addCertificationModal').showModal()" class="px-3 py-1.5 bg-emerald-600 text-white rounded text-sm hover:bg-emerald-700 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Sertifikasi
                        </button>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        @forelse($employee->certifications->sortByDesc('issue_date') as $cert)
                            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm flex flex-col justify-between group h-full">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-1 rounded uppercase tracking-wide">
                                            Sertifikasi
                                        </div>
                                        <form action="{{ route('employees.certifications.destroy', [$employee->id, $cert->id]) }}" method="POST" onsubmit="return confirm('Hapus sertifikasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                    <h4 class="font-bold text-slate-900">{{ $cert->name }}</h4>
                                    <p class="text-sm text-slate-600">{{ $cert->issuer }}</p>
                                    <div class="mt-3 text-sm text-slate-500 space-y-1">
                                        <p>Diterbitkan: {{ $cert->issue_date ? $cert->issue_date->format('M Y') : '-' }}</p>
                                        <p>Berlaku Sampai: {{ $cert->expiry_date ? $cert->expiry_date->format('M Y') : 'Seumur Hidup' }}</p>
                                        @if($cert->credential_number)
                                            <p class="font-mono text-xs text-slate-400 mt-2">ID: {{ $cert->credential_number }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if($cert->url)
                                    <div class="mt-4 pt-3 border-t border-slate-100">
                                        <a href="{{ $cert->url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                                            Lihat Sertifikat <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-2 text-center py-8 text-slate-500 italic border border-dashed border-slate-300 rounded-lg">
                                Belum ada data sertifikasi.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Modal Add Education -->
            <dialog id="addEducationModal" class="modal rounded-lg shadow-xl p-0 w-full max-w-lg backdrop:bg-gray-900/50">
                <div class="bg-white p-6">
                    <h3 class="font-bold text-lg mb-4">Tambah Riwayat Pendidikan</h3>
                    <form action="{{ route('employees.educations.store', $employee->id) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Institusi / Sekolah</label>
                                <input type="text" name="institution_name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang</label>
                                    <select name="degree" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        <option value="">Pilih...</option>
                                        <option value="SMA/SMK">SMA/SMK</option>
                                        <option value="D3">D3</option>
                                        <option value="D4">D4</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan (Opsional)</label>
                                    <input type="text" name="major" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Thn Masuk</label>
                                    <input type="number" name="start_year" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="YYYY">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Thn Lulus</label>
                                    <input type="number" name="end_year" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="YYYY">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">IPK / Nilai</label>
                                    <input type="number" step="0.01" name="gpa" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="0.00">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                                <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('addEducationModal').close()" class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </dialog>

            <!-- Modal Add Certification -->
            <dialog id="addCertificationModal" class="modal rounded-lg shadow-xl p-0 w-full max-w-lg backdrop:bg-gray-900/50">
                <div class="bg-white p-6">
                    <h3 class="font-bold text-lg mb-4">Tambah Sertifikasi</h3>
                    <form action="{{ route('employees.certifications.store', $employee->id) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sertifikasi / Pelatihan</label>
                                <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit (Institusi)</label>
                                <input type="text" name="issuer" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                             <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tgl Terbit</label>
                                    <input type="date" name="issue_date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tgl Kadaluarsa</label>
                                    <input type="date" name="expiry_date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <p class="text-xs text-slate-400 mt-0.5">Kosongkan jika seumur hidup</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kredensial (ID)</label>
                                <input type="text" name="credential_number" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">URL Sertifikat (Opsional)</label>
                                <input type="url" name="url" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="https://...">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="document.getElementById('addCertificationModal').close()" class="px-4 py-2 border border-slate-300 rounded-md text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </dialog>

        </div>
    </div>

    @push('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
    @endpush
</x-app-layout>
