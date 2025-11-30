<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Mutasi / SK Baru</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded-xl p-6">
            <div class="flex items-center gap-4 mb-6 border-b pb-4">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-500">
                    {{ substr($employee->full_name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $employee->full_name }}</h3>
                    <p class="text-sm text-slate-500">{{ $employee->employee_code }} &bull; {{ $employee->position->name ?? '-' }}</p>
                </div>
            </div>

            <form method="post" action="{{ route('employees.mutations.store', $employee->id) }}" class="space-y-6">
                @csrf
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Perubahan <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Pilih Jenis</option>
                            <option value="promotion">Promosi (Kenaikan Jabatan)</option>
                            <option value="demotion">Demosi (Penurunan Jabatan)</option>
                            <option value="rotation">Rotasi / Mutasi (Pindah Cabang/Divisi)</option>
                            <option value="contract_renewal">Perpanjangan Kontrak</option>
                            <option value="permanent_appointment">Pengangkatan Karyawan Tetap</option>
                            <option value="salary_adjustment">Penyesuaian Gaji</option>
                            <option value="resignation">Resign (Mengundurkan Diri)</option>
                            <option value="termination">Terminasi (PHK)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nomor SK / Referensi</label>
                        <input name="reference_number" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Contoh: SK/HR/001/XI/2025">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Efektif <span class="text-red-500">*</span></label>
                        <input name="effective_date" type="date" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                    <h4 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Perubahan Data (Isi yang berubah saja)
                    </h4>
                    
                    <div class="grid md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase mb-1">Cabang Baru</label>
                            <select name="new_branch_id" class="w-full border-slate-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tetap ({{ $employee->branch->name ?? '-' }})</option>
                                @foreach($branches as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase mb-1">Departemen Baru</label>
                            <select name="new_department_id" class="w-full border-slate-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tetap ({{ $employee->department->name ?? '-' }})</option>
                                @foreach($departments as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase mb-1">Jabatan Baru</label>
                            <select name="new_position_id" class="w-full border-slate-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tetap ({{ $employee->position->name ?? '-' }})</option>
                                @foreach($positions as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase mb-1">Status Kepegawaian</label>
                            <select name="new_employment_type" class="w-full border-slate-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tetap ({{ ucfirst($employee->employment_type) }})</option>
                                <option value="permanent">Permanent</option>
                                <option value="contract">Contract</option>
                                <option value="intern">Intern</option>
                                <option value="outsourcing">Outsourcing</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase mb-1">Status Aktif</label>
                            <select name="new_status" class="w-full border-slate-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Tetap ({{ ucfirst($employee->status) }})</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                                <option value="suspended">Suspended</option>
                                <option value="terminated">Terminated</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 uppercase mb-1">Gaji Pokok Baru</label>
                            <input name="new_basic_salary" type="number" step="0.01" class="w-full border-slate-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="{{ number_format($employee->basic_salary, 0, ',', '.') }}">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Catatan / Keterangan</label>
                    <textarea name="notes" rows="3" class="w-full border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Alasan mutasi/promosi..."></textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('employees.show', $employee->id) }}" class="px-6 py-2 border border-slate-300 text-slate-600 rounded-lg font-medium hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
