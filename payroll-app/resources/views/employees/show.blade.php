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

            <!-- Tab Content: Payroll (Placeholder) -->
            <div x-show="tab === 'payroll'" class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Informasi Gaji</h3>
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
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
    @endpush
</x-app-layout>
