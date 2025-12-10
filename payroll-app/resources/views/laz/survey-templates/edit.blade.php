<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Template Survey') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-600 p-4 rounded-md">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('laz.survey-templates.update', $surveyTemplate) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Program</label>
                            <select name="program_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Pilih Program</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}" {{ old('program_id', $surveyTemplate->program_id) == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Template</label>
                            <input type="text" name="code" value="{{ old('code', $surveyTemplate->code) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Judul Template</label>
                            <input type="text" name="title" value="{{ old('title', $surveyTemplate->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $surveyTemplate->description) }}</textarea>
                        </div>
                    </div>

                    <div class="border-t pt-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Pertanyaan</h3>
                            <button type="button" onclick="addQuestion()" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1 rounded hover:bg-indigo-100">+ Tambah Pertanyaan</button>
                        </div>

                        <div id="questions-container" class="space-y-4">
                            @foreach ($surveyTemplate->questions as $index => $question)
                                <div class="question-item bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
                                    <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">
                                    <!-- Field deletion flag -->
                                    <input type="hidden" name="questions[{{ $index }}][delete_flag]" class="delete-flag" value="0">

                                    <button type="button" onclick="markAsDeleted(this)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                                        <div class="md:col-span-8">
                                            <label class="block text-xs font-medium text-gray-500">Pertanyaan</label>
                                            <input type="text" name="questions[{{ $index }}][question]" value="{{ old('questions.'.$index.'.question', $question->question) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                        <div class="md:col-span-3">
                                            <label class="block text-xs font-medium text-gray-500">Tipe Jawaban</label>
                                            <select name="questions[{{ $index }}][type]" onchange="toggleOptions(this)" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="text" {{ $question->type == 'text' ? 'selected' : '' }}>Teks Singkat</option>
                                                <option value="textarea" {{ $question->type == 'textarea' ? 'selected' : '' }}>Teks Panjang</option>
                                                <option value="number" {{ $question->type == 'number' ? 'selected' : '' }}>Angka</option>
                                                <option value="date" {{ $question->type == 'date' ? 'selected' : '' }}>Tanggal</option>
                                                <option value="select" {{ $question->type == 'select' ? 'selected' : '' }}>Pilihan (Dropdown)</option>
                                                <option value="radio" {{ $question->type == 'radio' ? 'selected' : '' }}>Pilihan (Radio)</option>
                                                <option value="checkbox" {{ $question->type == 'checkbox' ? 'selected' : '' }}>Pilihan (Checkbox)</option>
                                                <option value="photo" {{ $question->type == 'photo' ? 'selected' : '' }}>Foto</option>
                                            </select>
                                        </div>
                                        <div class="md:col-span-1 pt-6 text-center">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" name="questions[{{ $index }}][is_required]" value="1" {{ $question->is_required ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                <span class="ml-2 text-xs text-gray-600">Wajib</span>
                                            </label>
                                        </div>
                                        <div class="md:col-span-12 options-field {{ in_array($question->type, ['select', 'radio', 'checkbox']) ? '' : 'hidden' }}">
                                            <label class="block text-xs font-medium text-gray-500">Opsi Pilihan (Pisahkan dengan koma)</label>
                                            <input type="text" name="questions[{{ $index }}][options]" value="{{ is_array($question->options) ? implode(', ', $question->options) : '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white" placeholder="Ya, Tidak, Mungkin">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('laz.survey-templates.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Batal</a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Question Template for JS -->
    <template id="question-template">
        <div class="question-item bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
            <button type="button" onclick="removeQuestion(this)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-8">
                    <label class="block text-xs font-medium text-gray-500">Pertanyaan</label>
                    <input type="text" name="questions[INDEX][question]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-medium text-gray-500">Tipe Jawaban</label>
                    <select name="questions[INDEX][type]" onchange="toggleOptions(this)" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="text">Teks Singkat</option>
                        <option value="textarea">Teks Panjang</option>
                        <option value="number">Angka</option>
                        <option value="date">Tanggal</option>
                        <option value="select">Pilihan (Dropdown)</option>
                        <option value="radio">Pilihan (Radio)</option>
                        <option value="checkbox">Pilihan (Checkbox)</option>
                        <option value="photo">Foto</option>
                    </select>
                </div>
                <div class="md:col-span-1 pt-6 text-center">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="questions[INDEX][is_required]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-xs text-gray-600">Wajib</span>
                    </label>
                </div>
                <div class="md:col-span-12 hidden options-field">
                    <label class="block text-xs font-medium text-gray-500">Opsi Pilihan (Pisahkan dengan koma)</label>
                    <input type="text" name="questions[INDEX][options]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-white" placeholder="Ya, Tidak, Mungkin">
                </div>
            </div>
        </div>
    </template>

    <script>
        // Start index from existing count + 100 to avoid collision
        let questionIndex = {{ $surveyTemplate->questions->count() + 100 }};

        function addQuestion() {
            const container = document.getElementById('questions-container');
            const template = document.getElementById('question-template');
            const clone = template.content.cloneNode(true);
            
            // Replace INDEX placeholder
            const elements = clone.querySelectorAll('[name*="INDEX"]');
            elements.forEach(el => {
                el.name = el.name.replace('INDEX', questionIndex);
            });

            container.appendChild(clone);
            questionIndex++;
        }

        // For newly added items
        function removeQuestion(btn) {
            btn.closest('.question-item').remove();
        }

        // For existing items
        function markAsDeleted(btn) {
            const item = btn.closest('.question-item');
            if(confirm("Hapus pertanyaan ini?")) {
                item.querySelector('.delete-flag').value = 1;
                item.style.display = 'none';
            }
        }

        function toggleOptions(select) {
            const optionsField = select.closest('.question-item').querySelector('.options-field');
            if (['select', 'radio', 'checkbox'].includes(select.value)) {
                optionsField.classList.remove('hidden');
                optionsField.querySelector('input').required = true;
            } else {
                optionsField.classList.add('hidden');
                optionsField.querySelector('input').required = false;
                optionsField.querySelector('input').value = ''; // Optional: clear value
            }
        }
    </script>
</x-app-layout>
