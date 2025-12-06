<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tugas Survey') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showModal: false, selectedSurvey: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-xl font-semibold mb-4">Survey Permohonan</h1>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-2 border">Permohonan</th>
                                <th class="p-2 border">Pemohon</th>
                                <th class="p-2 border">Program</th>
                                <th class="p-2 border">Surveyor</th>
                                <th class="p-2 border">Tanggal</th>
                                <th class="p-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($surveys as $survey)
                                <tr>
                                    <td class="p-2 border"><a class="text-emerald-700" href="{{ route('laz.applications.show', $survey->application) }}">{{ $survey->application->code }}</a></td>
                                    <td class="p-2 border">{{ $survey->application->applicant_name }}</td>
                                    <td class="p-2 border">{{ $survey->application->program->name }}</td>
                                    <td class="p-2 border">{{ $survey->surveyor->name }}</td>
                                    <td class="p-2 border">{{ $survey->survey_date?->format('d M Y') ?? '-' }}</td>
                                    <td class="p-2 border text-center">
                                        <button @click="showModal = true; selectedSurvey = {{ json_encode($survey) }}" class="px-2 py-1 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 text-xs">
                                            Detail & Foto
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $surveys->links() }}</div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="showModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <template x-if="selectedSurvey">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Detail Survey</h3>
                                <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Metode</p>
                                        <p class="font-medium" x-text="selectedSurvey.method"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Skor Ekonomi</p>
                                        <p class="font-medium" x-text="selectedSurvey.economic_condition_score"></p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-gray-500">Ringkasan</p>
                                        <p class="font-medium" x-text="selectedSurvey.summary"></p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-gray-500">Rekomendasi</p>
                                        <p class="font-medium" x-text="selectedSurvey.recommendation"></p>
                                    </div>
                                </div>
                                
                                <h4 class="font-medium text-gray-900 mb-2">Foto Dokumentasi</h4>
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="photo in selectedSurvey.photos" :key="photo.id">
                                        <a :href="'/storage/' + photo.file_path" target="_blank" class="block aspect-square rounded overflow-hidden border border-gray-200">
                                            <img :src="'/storage/' + photo.file_path" class="w-full h-full object-cover">
                                        </a>
                                    </template>
                                    <template x-if="!selectedSurvey.photos || selectedSurvey.photos.length === 0">
                                        <p class="col-span-3 text-sm text-gray-500 italic">Tidak ada foto.</p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
