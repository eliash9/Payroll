<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Permohonan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showDocModal: false, docUrl: '', docType: '' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Header Section -->
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h1 class="text-2xl font-bold text-slate-900">{{ $application->code }}</h1>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide
                                    @if($application->status === 'approved' || $application->status === 'completed') bg-emerald-100 text-emerald-700
                                    @elseif($application->status === 'rejected') bg-red-100 text-red-700
                                    @elseif($application->status === 'submitted') bg-blue-100 text-blue-700
                                    @else bg-amber-100 text-amber-700
                                    @endif">
                                    {{ str_replace('_', ' ', $application->status) }}
                                </span>
                            </div>
                            <p class="text-slate-600 text-sm">
                                {{ $application->program->name }} &bull; {{ $application->period->name }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Tanggal Pengajuan</p>
                            <p class="text-slate-900 font-medium">{{ $application->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Stepper -->
                <div class="p-6 border-b border-slate-100">
                    <div class="relative">
                        <div class="absolute top-1/2 left-0 w-full h-1 bg-slate-100 -translate-y-1/2 z-0"></div>
                        <div class="relative z-10 flex justify-between">
                            <!-- Step 1: Submitted -->
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ in_array($application->status, ['submitted', 'screening', 'survey_assigned', 'surveying', 'waiting_approval', 'approved', 'completed']) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                    1
                                </div>
                                <span class="text-xs font-medium text-slate-600">Verifikasi</span>
                            </div>
                            <!-- Step 2: Survey -->
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ in_array($application->status, ['survey_assigned', 'surveying', 'waiting_approval', 'approved', 'completed']) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                    2
                                </div>
                                <span class="text-xs font-medium text-slate-600">Survey</span>
                            </div>
                            <!-- Step 3: Approval -->
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ in_array($application->status, ['waiting_approval', 'approved', 'completed']) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                    3
                                </div>
                                <span class="text-xs font-medium text-slate-600">Keputusan</span>
                            </div>
                            <!-- Step 4: Disbursement -->
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ in_array($application->status, ['approved', 'disbursement_in_progress', 'completed']) ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                                    4
                                </div>
                                <span class="text-xs font-medium text-slate-600">Penyaluran</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid lg:grid-cols-3 gap-8 p-6">
                    <!-- Left Column: Information -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Applicant Info -->
                        <section>
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Data Pemohon
                            </h3>
                            <div class="bg-slate-50 rounded-xl p-5 border border-slate-100 grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Nama Lengkap</label>
                                    <p class="text-slate-900 font-medium">{{ $application->applicant_name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Tipe Pemohon</label>
                                    <p class="text-slate-900 font-medium">{{ $application->applicant_type === 'individual' ? 'Perorangan' : 'Lembaga' }}</p>
                                </div>
                                @if ($application->applicant)
                                    <div>
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">NIK</label>
                                        <p class="text-slate-900 font-medium">{{ $application->applicant->national_id }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Kontak</label>
                                        <p class="text-slate-900 font-medium">{{ $application->applicant->phone }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Alamat</label>
                                        <p class="text-slate-900 font-medium">{{ $application->applicant->address }}</p>
                                    </div>
                                @endif
                            </div>

                        </section>

                        <!-- Beneficiary Details (if not self) -->
                        @if (!$application->is_applicant_beneficiary && $application->beneficiaries->isNotEmpty())
                        <section>
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Data Penerima Manfaat
                            </h3>
                            <div class="space-y-4">
                                @foreach($application->beneficiaries as $beneficiary)
                                <div class="bg-orange-50 rounded-xl p-5 border border-orange-100 grid md:grid-cols-2 gap-6 relative">
                                    <div class="absolute top-4 right-4 bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded font-bold">
                                        #{{ $loop->iteration }}
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Nama Penerima</label>
                                        <p class="text-slate-900 font-medium">{{ $beneficiary->name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">NIK (Opsional)</label>
                                        <p class="text-slate-900 font-medium">{{ $beneficiary->national_id ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Kontak</label>
                                        <p class="text-slate-900 font-medium">{{ $beneficiary->phone ?? '-' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Alamat</label>
                                        <p class="text-slate-900 font-medium">{{ $beneficiary->address ?? '-' }}</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Keterangan / Kondisi</label>
                                        <p class="text-slate-700 leading-relaxed">{{ $beneficiary->description ?? '-' }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </section>
                        @endif


                        <!-- Request Details -->
                        <section>
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Detail Permohonan
                            </h3>
                            <div class="bg-slate-50 rounded-xl p-5 border border-slate-100 space-y-4">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Nominal Diajukan</label>
                                        <p class="text-emerald-700 font-bold text-lg">Rp {{ number_format($application->requested_amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Bentuk Bantuan</label>
                                        <p class="text-slate-900 font-medium capitalize">{{ $application->requested_aid_type }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Deskripsi Kebutuhan</label>
                                    <p class="text-slate-700 mt-1 leading-relaxed">{{ $application->need_description }}</p>
                                </div>
                                <div>
                                    <label class="text-xs text-slate-500 uppercase tracking-wide font-semibold">Lokasi</label>
                                    <p class="text-slate-900 font-medium">
                                        {{ $application->location_village ? $application->location_village . ', ' : '' }}
                                        {{ $application->location_district ? $application->location_district . ', ' : '' }}
                                        {{ $application->location_regency }}, {{ $application->location_province }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <!-- Documents -->
                        <section>
                            <h3 class="text-lg font-semibold text-slate-900 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Dokumen Pendukung
                            </h3>
                            <div class="grid sm:grid-cols-2 gap-4">
                                @forelse ($application->documents as $doc)
                                    <div class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-xl hover:border-emerald-500 hover:shadow-md transition-all group">
                                        <div class="flex items-center flex-1 cursor-pointer" @click="showDocModal = true; docUrl = '{{ Storage::url($doc->file_path) }}'; docType = '{{ $doc->document_type }}'">
                                            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg mr-3 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-900">{{ $doc->document_type }}</p>
                                                <p class="text-xs text-slate-500">Klik untuk melihat</p>
                                            </div>
                                        </div>
                                        <a href="{{ Storage::url($doc->file_path) }}" download target="_blank" class="p-2 text-slate-400 hover:text-emerald-600 transition-colors" title="Unduh">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-span-2 text-center py-8 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-slate-500">
                                        Belum ada dokumen yang diunggah
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </div>

                    <!-- Right Column: Actions & History -->
                    <div class="space-y-6">
                        
                        <!-- ACTION CARD: Based on Status -->
                        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="bg-slate-900 px-4 py-3 border-b border-slate-800">
                                <h3 class="font-bold text-white flex items-center gap-2">
                                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Tindakan Diperlukan
                                </h3>
                            </div>
                            <div class="p-4">
                                @php $user = auth()->user(); @endphp

                                <!-- 1. Submitted -> Screening/Assign Surveyor -->
                                @if ($application->status === 'submitted' || $application->status === 'screening')
                                    @if ($user->hasRole(['super_admin','admin_pusat','admin_cabang']))
                                        <div class="space-y-4">
                                            <p class="text-sm text-slate-600">Permohonan baru masuk. Silakan verifikasi berkas dan tugaskan surveyor jika diperlukan.</p>
                                            
                                            <form method="POST" action="{{ route('laz.applications.assign-surveyor', $application) }}" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="text-xs font-semibold text-slate-700 uppercase">Pilih Surveyor</label>
                                                    <select name="surveyor_id" class="mt-1 w-full border-slate-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                                        @foreach ($surveyors as $surveyor)
                                                            <option value="{{ $surveyor->id }}">{{ $surveyor->name }} ({{ $surveyor->branch->name ?? '-' }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <button class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors">
                                                    Tugaskan Surveyor
                                                </button>
                                            </form>

                                            <div class="border-t pt-3">
                                                <form method="POST" action="{{ route('laz.applications.status', $application) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="w-full py-2 border border-red-200 text-red-600 hover:bg-red-50 rounded-lg font-medium transition-colors text-sm">
                                                        Tolak Permohonan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-slate-500 italic">Menunggu verifikasi admin.</p>
                                    @endif

                                    @elseif ($application->status === 'survey_assigned')
                                    @if ($user->hasRole(['super_admin','admin_pusat','admin_cabang','surveyor']))
                                        <div class="space-y-4">
                                            <p class="text-sm text-slate-600">Surveyor telah ditugaskan. Silakan input hasil survey lapangan.</p>
                                            
                                            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3 w-full">
                                                        <h3 class="text-sm font-medium text-indigo-800">Mulai Survey Lapangan</h3>
                                                        <div class="mt-2 text-sm text-indigo-700">
                                                            <p>Proses survey menggunakan formulir dinamis sesuai program <strong>{{ $application->program->name }}</strong>.</p>
                                                        </div>
                                                        <div class="mt-4">
                                                            <a href="{{ route('laz.surveys.create', $application) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full justify-center text-center">
                                                                Isi Formulir Survey
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm text-slate-500 italic">Menunggu hasil survey.</p>
                                    @endif

                                <!-- 3. Waiting Approval -> Approve/Reject -->
                                @elseif ($application->status === 'waiting_approval' || $application->status === 'surveying')
                                    @if ($user->hasRole(['super_admin','approver']))
                                        <div class="space-y-4">
                                            <p class="text-sm text-slate-600">Hasil survey telah masuk. Silakan berikan keputusan.</p>
                                            
                                            <form method="POST" action="{{ route('laz.approvals.store', $application) }}" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="text-xs font-semibold text-slate-700 uppercase">Keputusan</label>
                                                    <select name="decision" class="mt-1 w-full border-slate-300 rounded-lg text-sm font-medium">
                                                        <option value="approved">Setujui Permohonan</option>
                                                        <option value="rejected">Tolak</option>
                                                        <option value="revision">Minta Revisi</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-xs font-semibold text-slate-700 uppercase">Nominal Disetujui</label>
                                                    <input type="number" name="approved_amount" value="{{ $application->requested_amount }}" class="mt-1 w-full border-slate-300 rounded-lg text-sm">
                                                </div>
                                                <div>
                                                    <label class="text-xs font-semibold text-slate-700 uppercase">Catatan</label>
                                                    <textarea name="notes" rows="2" class="mt-1 w-full border-slate-300 rounded-lg text-sm"></textarea>
                                                </div>
                                                <button class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors">
                                                    Simpan Keputusan
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <p class="text-sm text-slate-500 italic">Menunggu persetujuan approver.</p>
                                    @endif

                                <!-- 4. Approved -> Disburse -->
                                @elseif ($application->status === 'approved' || $application->status === 'disbursement_in_progress')
                                    @if ($user->hasRole(['super_admin','keuangan']))
                                        <div class="space-y-4">
                                            <p class="text-sm text-slate-600">Permohonan disetujui. Silakan proses penyaluran dana/bantuan.</p>
                                            
                                            <form method="POST" action="{{ route('laz.disbursements.store', $application) }}" enctype="multipart/form-data" class="space-y-3">
                                                @csrf
                                                <div>
                                                    <label class="text-xs font-semibold text-slate-700 uppercase">Tanggal Penyaluran</label>
                                                    <input type="datetime-local" name="disbursed_at" class="mt-1 w-full border-slate-300 rounded-lg text-sm" required>
                                                </div>
                                                <div>
                                                    <label class="text-xs font-semibold text-slate-700 uppercase">Metode</label>
                                                    <select name="method" class="mt-1 w-full border-slate-300 rounded-lg text-sm">
                                                        <option value="transfer">Transfer Bank</option>
                                                        <option value="cash">Tunai</option>
                                                        <option value="goods">Barang</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-xs font-semibold text-slate-700 uppercase">Total Nilai</label>
                                                    <input type="number" name="total_amount" class="mt-1 w-full border-slate-300 rounded-lg text-sm" required>
                                                </div>
                                                <button class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-colors">
                                                    Catat Penyaluran
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <p class="text-sm text-slate-500 italic">Menunggu proses pencairan oleh keuangan.</p>
                                    @endif

                                <!-- 5. Completed/Rejected -->
                                @else
                                    <div class="text-center py-4">
                                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <p class="text-sm font-medium text-slate-900">Proses Selesai</p>
                                        <p class="text-xs text-slate-500">Tidak ada tindakan lebih lanjut.</p>
                                    </div>
                                @endif

                            </div>
                        </div>

                        <!-- History Logs -->
                        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
                            <h3 class="font-bold text-slate-900 mb-4 text-sm uppercase tracking-wide">Riwayat Proses</h3>
                            <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-300 before:to-transparent">
                                
                                <!-- Survey History -->
                                @foreach ($application->surveys as $survey)
                                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-slate-50 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                            <svg class="fill-slate-500" xmlns="http://www.w3.org/2000/svg" width="12" height="10"><path fill-rule="nonzero" d="M10.422 1.257 4.655 7.025 2.553 4.923A.916.916 0 0 0 1.257 6.22l2.75 2.75a.916.916 0 0 0 1.296 0l6.415-6.416a.916.916 0 0 0-1.296-1.296Z"/></svg>
                                        </div>
                                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-4 rounded border border-slate-200 shadow">
                                            <div class="flex items-center justify-between space-x-2 mb-1">
                                                <div class="font-bold text-slate-900 text-sm">Survey Lapangan</div>
                                                <time class="font-caveat font-medium text-indigo-500 text-xs">{{ $survey->created_at->format('d M Y') }}</time>
                                            </div>
                                            <div class="text-slate-500 text-xs">Surveyor: {{ $survey->surveyor->name ?? 'Unknown' }}<br>Rekomendasi: {{ $survey->recommendation }}</div>
                                            <div class="mt-2">
                                                <a href="{{ route('laz.surveys.show', $survey) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium underline">Lihat Detail Laporan</a>
                                            </div>
                                            @if($survey->photos->count() > 0)
                                                <div class="mt-2 grid grid-cols-3 gap-2">
                                                    @foreach($survey->photos as $photo)
                                                        <a href="{{ Storage::url($photo->file_path) }}" target="_blank" class="block aspect-square rounded overflow-hidden border border-slate-200 hover:opacity-75">
                                                            <img src="{{ Storage::url($photo->file_path) }}" alt="{{ $photo->caption }}" class="w-full h-full object-cover">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Approval History -->
                                @foreach ($application->approvals as $approval)
                                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-emerald-50 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                            <svg class="fill-emerald-600" xmlns="http://www.w3.org/2000/svg" width="12" height="10"><path fill-rule="nonzero" d="M10.422 1.257 4.655 7.025 2.553 4.923A.916.916 0 0 0 1.257 6.22l2.75 2.75a.916.916 0 0 0 1.296 0l6.415-6.416a.916.916 0 0 0-1.296-1.296Z"/></svg>
                                        </div>
                                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-4 rounded border border-slate-200 shadow">
                                            <div class="flex items-center justify-between space-x-2 mb-1">
                                                <div class="font-bold text-slate-900 text-sm">Keputusan: {{ ucfirst($approval->decision) }}</div>
                                                <time class="font-caveat font-medium text-indigo-500 text-xs">{{ $approval->decided_at->format('d M Y') }}</time>
                                            </div>
                                            <div class="text-slate-500 text-xs">Oleh: {{ $approval->approver->name }}<br>Nominal: Rp {{ number_format($approval->approved_amount,0,',','.') }}</div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Disbursement History -->
                                @foreach ($application->disbursements as $disbursement)
                                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-blue-50 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                            <svg class="fill-blue-600" xmlns="http://www.w3.org/2000/svg" width="12" height="10"><path fill-rule="nonzero" d="M10.422 1.257 4.655 7.025 2.553 4.923A.916.916 0 0 0 1.257 6.22l2.75 2.75a.916.916 0 0 0 1.296 0l6.415-6.416a.916.916 0 0 0-1.296-1.296Z"/></svg>
                                        </div>
                                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-4 rounded border border-slate-200 shadow">
                                            <div class="flex items-center justify-between space-x-2 mb-1">
                                                <div class="font-bold text-slate-900 text-sm">Penyaluran Dana</div>
                                                <time class="font-caveat font-medium text-indigo-500 text-xs">{{ $disbursement->disbursed_at->format('d M Y') }}</time>
                                            </div>
                                            <div class="text-slate-500 text-xs">Metode: {{ $disbursement->method }}<br>Total: Rp {{ number_format($disbursement->total_amount,0,',','.') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Document Modal -->
    <div x-show="showDocModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDocModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showDocModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="showDocModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title" x-text="docType"></h3>
                        <button @click="showDocModal = false" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 h-[70vh] bg-slate-100 rounded-lg overflow-hidden">
                        <iframe :src="docUrl" class="w-full h-full border-0"></iframe>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                     <a :href="docUrl" download target="_blank" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Unduh
                    </a>
                    <button type="button" @click="showDocModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
