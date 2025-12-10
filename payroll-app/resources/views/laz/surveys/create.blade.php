<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Hasil Survey') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg max-w-4xl mx-auto">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Permohonan: {{ $application->code }}</h3>
                    <p class="text-sm text-gray-600">Pemohon: {{ $application->applicant_name }}</p>
                    <p class="text-sm text-gray-600">Program: {{ $application->program->name }}</p>
                </div>

                <div class="p-6">
                    <!-- Error Display -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('laz.surveys.store', $application) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="surveyor_id" value="{{ auth()->id() }}">
                        @if($template)
                            <input type="hidden" name="survey_template_id" value="{{ $template->id }}">
                        @endif

                        <!-- General Survey Info -->
                        <div class="mb-8">
                            <h4 class="font-semibold text-gray-800 mb-4 pb-2 border-b">Informasi Umum Survey</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tanggal Survey <span class="text-red-500">*</span></label>
                                    <input type="date" name="survey_date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Metode Survey <span class="text-red-500">*</span></label>
                                    <select name="method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        <option value="onsite">Kunjungan Langsung (Onsite)</option>
                                        <option value="online">Online / Video Call</option>
                                        <option value="phone">Wawancara Telepon</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Questions (if template exists) -->
                        @if($template && $template->questions->count() > 0)
                            <div class="mb-8">
                                <h4 class="font-semibold text-gray-800 mb-4 pb-2 border-b">Pertanyaan Survey ({{ $template->title }})</h4>
                                <div class="space-y-6">
                                    @foreach($template->questions as $question)
                                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                                {{ $question->order }}. {{ $question->question }}
                                                @if($question->is_required) <span class="text-red-500">*</span> @endif
                                            </label>

                                            @if($question->type === 'text')
                                                <input type="text" name="responses[{{ $question->id }}]" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" {{ $question->is_required ? 'required' : '' }}>
                                            
                                            @elseif($question->type === 'textarea')
                                                <textarea name="responses[{{ $question->id }}]" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" {{ $question->is_required ? 'required' : '' }}></textarea>
                                            
                                            @elseif($question->type === 'number')
                                                <input type="number" name="responses[{{ $question->id }}]" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" {{ $question->is_required ? 'required' : '' }}>
                                            
                                            @elseif($question->type === 'date')
                                                <input type="date" name="responses[{{ $question->id }}]" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" {{ $question->is_required ? 'required' : '' }}>
                                            
                                            @elseif($question->type === 'select')
                                                <select name="responses[{{ $question->id }}]" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm" {{ $question->is_required ? 'required' : '' }}>
                                                    <option value="">-- Pilih --</option>
                                                    @if($question->options)
                                                        @foreach($question->options as $opt)
                                                            @php $label = is_array($opt) ? $opt['text'] : $opt; @endphp
                                                            <option value="{{ $label }}">{{ $label }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            
                                            @elseif($question->type === 'radio')
                                                <div class="space-y-2">
                                                    @if($question->options)
                                                        @foreach($question->options as $opt)
                                                            @php $label = is_array($opt) ? $opt['text'] : $opt; @endphp
                                                            <div class="flex items-center">
                                                                <input type="radio" name="responses[{{ $question->id }}]" value="{{ $label }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" {{ $question->is_required ? 'required' : '' }}>
                                                                <label class="ml-2 block text-sm text-gray-700">{{ $label }}</label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                            @elseif($question->type === 'checkbox')
                                                <div class="space-y-2">
                                                    @if($question->options)
                                                        @foreach($question->options as $opt)
                                                            @php $label = is_array($opt) ? $opt['text'] : $opt; @endphp
                                                            <div class="flex items-center">
                                                                <input type="checkbox" name="responses[{{ $question->id }}][]" value="{{ $label }}" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                                                <label class="ml-2 block text-sm text-gray-700">{{ $label }}</label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                            @elseif($question->type === 'photo')
                                                <!-- For now treating specific photo questions as standard file inputs, handled separately if we want to store them in SurveyResponse or SurveyPhoto. 
                                                     Given simplicity, maybe just use standard photo upload section for evidence.
                                                     But let's allow file input here, though current controller saves answers as text/json.
                                                     We might need to adjust controller to handle file uploads for 'answer' if it's a file path.
                                                     For this iteration, I'll instruct user to upload photos in the main Evidence section.
                                                -->
                                                <p class="text-sm text-gray-500 italic pb-2">Silakan unggah foto terkait pertanyaan ini di bagian "Bukti Foto Lapangan" di bawah.</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-blue-50 text-blue-700 rounded-md">
                                <p>Tidak ada template survey khusus untuk program ini. Silakan isi ringkasan manual di bawah.</p>
                            </div>
                        @endif

                        <!-- Conclusion -->
                        <div class="mb-8">
                            <h4 class="font-semibold text-gray-800 mb-4 pb-2 border-b">Kesimpulan & Rekomendasi</h4>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Skor Kondisi Ekonomi (1-5)</label>
                                <div class="flex gap-4">
                                    @foreach(range(1, 5) as $score)
                                        <label class="flex items-center gap-1 cursor-pointer p-3 border rounded-md hover:bg-gray-50 has-[:checked]:bg-emerald-50 has-[:checked]:border-emerald-500">
                                            <input type="radio" name="economic_condition_score" value="{{ $score }}" class="text-emerald-600 focus:ring-emerald-500" {{ $score === 3 ? 'checked' : '' }}>
                                            <div class="text-sm">
                                                <span class="font-bold block text-center">{{ $score }}</span>
                                                <span class="text-xs text-gray-500 block">
                                                    @if($score==1) Sangat Buruk @elseif($score==5) Sangat Baik @endif
                                                </span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ringkasan Temuan Survey <span class="text-red-500">*</span></label>
                                <textarea name="summary" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Jelaskan kondisi factual di lapangan..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rekomendasi Surveyor <span class="text-red-500">*</span></label>
                                <select name="recommendation" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">-- Pilih Rekomendasi --</option>
                                    <option value="eligible">LAYAK (Eligible) - Disarankan untuk dibantu</option>
                                    <option value="not_eligible">TIDAK LAYAK - Tidak memenuhi kriteria</option>
                                    <option value="need_revision">PERLU REVISI - Data kurang lengkap</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Tambahan</label>
                                <textarea name="notes" rows="2" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            </div>
                        </div>

                        <!-- Photos -->
                        <div class="mb-8">
                            <h4 class="font-semibold text-gray-800 mb-4 pb-2 border-b">Bukti Foto Lapangan</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-data="{ photos: [1,2,3] }">
                                <template x-for="i in photos" :key="i">
                                    <div class="border rounded-md p-3 bg-gray-50">
                                        <input type="file" name="photos[]" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 mb-2">
                                        <input type="text" name="photo_captions[]" placeholder="Keterangan foto (opsional)..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs">
                                    </div>
                                </template>
                            </div>
                            <button type="button" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800" onclick="alert('Maksimal 3 foto untuk saat ini via web.')">+ Tambah Slot Foto</button>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t">
                            <a href="{{ route('laz.applications.show', $application) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 font-medium">Batal</a>
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 font-medium shadow-sm">Simpan Hasil Survey</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
