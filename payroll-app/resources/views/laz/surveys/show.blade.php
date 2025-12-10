<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Survey Lapangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Toolbar -->
                <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center print:hidden">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('laz.applications.show', $survey->application) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                            &larr; Kembali ke Permohonan
                        </a>
                    </div>
                    <div>
                        <button onclick="window.print()" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak
                        </button>
                    </div>
                </div>

                <div class="p-8 print:p-0" id="survey-content">
                    <!-- Header Report -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h1 class="text-2xl font-bold text-center text-gray-900 mb-2">LAPORAN SURVEY LAPANGAN</h1>
                        <p class="text-center text-gray-600">Program: {{ $survey->application->program->name }}</p>
                    </div>

                    <!-- Info Table -->
                    <div class="grid grid-cols-2 gap-x-12 gap-y-4 mb-8 text-sm">
                        <div>
                            <table class="w-full">
                                <tr>
                                    <td class="py-1 text-gray-500 w-1/3">No. Permohonan</td>
                                    <td class="py-1 font-medium">{{ $survey->application->code }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-500">Nama Pemohon</td>
                                    <td class="py-1 font-medium">{{ $survey->application->applicant_name }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-500">Tanggal Survey</td>
                                    <td class="py-1 font-medium">{{ $survey->survey_date ? \Carbon\Carbon::parse($survey->survey_date)->format('d F Y') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table class="w-full">
                                <tr>
                                    <td class="py-1 text-gray-500 w-1/3">Surveyor</td>
                                    <td class="py-1 font-medium">{{ $survey->surveyor->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-500">Metode</td>
                                    <td class="py-1 font-medium capitalize">{{ $survey->method }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-500">Total Skor</td>
                                    <td class="py-1 font-bold text-indigo-600">{{ $survey->total_score }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Questions & Answers -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2 mb-4">Detail Pertanyaan</h3>
                        <div class="space-y-6">
                            @foreach($survey->responses as $response)
                                <div class="break-inside-avoid">
                                    <p class="text-sm font-medium text-gray-900 mb-1">
                                        {{ $response->question->order ?? $loop->iteration }}. {{ $response->question->question ?? 'Pertanyaan dihapus' }}
                                        <span class="text-xs text-gray-400 font-normal ml-2">(Bobot: {{ $response->question->weight ?? 0 }})</span>
                                    </p>
                                    <div class="bg-gray-50 p-3 rounded text-sm text-gray-800 border border-gray-100 flex justify-between">
                                        <div>
                                            @php
                                                $answer = $response->answer;
                                                // Try to decode if json array
                                                $decoded = json_decode($answer, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                    echo implode(', ', $decoded);
                                                } else {
                                                    echo $answer;
                                                }
                                            @endphp
                                        </div>
                                        <div class="font-bold text-indigo-600 ml-4">
                                            Skor: {{ $response->score }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Conclusion -->
                    <div class="mb-8 break-inside-avoid">
                        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2 mb-4">Kesimpulan Surveyor</h3>
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Kondisi Ekonomi</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-2xl font-bold {{ $survey->economic_condition_score >= 3 ? 'text-emerald-600' : 'text-orange-600' }}">
                                            {{ $survey->economic_condition_score }}/5
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            @if($survey->economic_condition_score == 1) (Sangat Buruk)
                                            @elseif($survey->economic_condition_score == 5) (Sangat Baik)
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Rekomendasi</p>
                                    @php
                                        $colors = [
                                            'eligible' => 'text-emerald-700 bg-emerald-100',
                                            'not_eligible' => 'text-red-700 bg-red-100',
                                            'need_revision' => 'text-amber-700 bg-amber-100',
                                        ];
                                        $labels = [
                                            'eligible' => 'LAYAK (ELIGIBLE)',
                                            'not_eligible' => 'TIDAK LAYAK',
                                            'need_revision' => 'PERLU REVISI',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $colors[$survey->recommendation] ?? 'text-gray-700 bg-gray-100' }}">
                                        {{ $labels[$survey->recommendation] ?? $survey->recommendation }}
                                    </span>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Ringkasan Temuan</p>
                                    <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $survey->summary }}</p>
                                </div>
                                @if($survey->notes)
                                <div class="md:col-span-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Catatan Tambahan</p>
                                    <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $survey->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Photos -->
                    @if($survey->photos->count() > 0)
                    <div class="break-inside-avoid">
                        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-200 pb-2 mb-4">Dokumentasi Lapangan</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($survey->photos as $photo)
                                <div class="border rounded-lg overflow-hidden break-inside-avoid">
                                    <img src="{{ Storage::url($photo->file_path) }}" class="w-full h-48 object-cover">
                                    @if($photo->caption)
                                        <div class="p-2 bg-gray-50 text-xs text-gray-600 border-t">
                                            {{ $photo->caption }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Signatures -->
                    <div class="mt-12 pt-8 border-t border-gray-200 grid grid-cols-3 gap-8 text-center text-sm break-inside-avoid">
                        <div>
                            <p class="mb-16">Surveyor</p>
                            <p class="font-bold underline">{{ $survey->surveyor->name ?? '....................' }}</p>
                        </div>
                        <div>
                            <p class="mb-16">Mengetahui</p>
                            <p class="font-bold underline">Kepala Program</p>
                        </div>
                        <div>
                            <p class="mb-16">Menyetujui</p>
                            <p class="font-bold underline">Kepala Cabang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #survey-content, #survey-content * {
                visibility: visible;
            }
            #survey-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
            }
            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
