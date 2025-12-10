<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Template Survey') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Header Info -->
                <div class="border-b pb-4 mb-6 flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $surveyTemplate->title }}</h1>
                        <p class="text-sm text-gray-500 mt-1">Kode: {{ $surveyTemplate->code }} | Program: {{ $surveyTemplate->program->name ?? '-' }}</p>
                        <p class="text-sm text-gray-500 mt-1">Status: 
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $surveyTemplate->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $surveyTemplate->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('laz.survey-templates.edit', $surveyTemplate) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">Edit Template</a>
                    </div>
                </div>

                @if($surveyTemplate->description)
                    <div class="mb-6 bg-gray-50 p-4 rounded-md">
                        <h3 class="font-medium text-gray-900 mb-2">Deskripsi</h3>
                        <p class="text-gray-600">{{ $surveyTemplate->description }}</p>
                    </div>
                @endif

                <!-- Question List Preview -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Preview Formulir Survey</h3>
                    
                    <div class="border rounded-lg p-6 bg-gray-50 space-y-6">
                        @forelse ($surveyTemplate->questions as $index => $question)
                            <div class="bg-white p-4 rounded border shadow-sm">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $index + 1 }}. {{ $question->question }}
                                    @if($question->is_required) <span class="text-red-500">*</span> @endif
                                </label>
                                
                                @if ($question->type === 'text')
                                    <input type="text" disabled class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm bg-gray-100 p-2" placeholder="Jawaban singkat text...">
                                
                                @elseif ($question->type === 'textarea')
                                    <textarea disabled rows="3" class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm bg-gray-100 p-2" placeholder="Jawaban panjang..."></textarea>
                                
                                @elseif ($question->type === 'number')
                                    <input type="number" disabled class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm bg-gray-100 p-2" placeholder="0">
                                
                                @elseif ($question->type === 'date')
                                    <input type="date" disabled class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm bg-gray-100 p-2">
                                
                                @elseif ($question->type === 'select')
                                    <select disabled class="block w-full rounded-md border-gray-300 shadow-sm sm:text-sm bg-gray-100 p-2">
                                        <option>-- Pilih Opsi --</option>
                                        @if($question->options)
                                            @foreach($question->options as $opt)
                                                <option>{{ $opt }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                
                                @elseif ($question->type === 'radio')
                                    <div class="space-y-2">
                                        @if($question->options)
                                            @foreach($question->options as $opt)
                                                <div class="flex items-center">
                                                    <input type="radio" disabled class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                    <label class="ml-2 block text-sm text-gray-700">{{ $opt }}</label>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-400 italic">Tidak ada opsi</p>
                                        @endif
                                    </div>

                                @elseif ($question->type === 'checkbox')
                                    <div class="space-y-2">
                                        @if($question->options)
                                            @foreach($question->options as $opt)
                                                <div class="flex items-center">
                                                    <input type="checkbox" disabled class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                    <label class="ml-2 block text-sm text-gray-700">{{ $opt }}</label>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-sm text-gray-400 italic">Tidak ada opsi</p>
                                        @endif
                                    </div>
                                
                                @elseif ($question->type === 'photo')
                                    <div class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-1 text-sm text-gray-600">Upload Foto</p>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-400 mt-2 text-right">Tipe: {{ ucfirst($question->type) }}</p>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-4">Belum ada pertanyaan.</p>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('laz.survey-templates.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">&larr; Kembali ke Daftar Template</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
