<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Template Survey Baru') }}
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

                <form method="POST" action="{{ route('laz.survey-templates.store') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Program</label>
                            <select name="program_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">Pilih Program</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kode Template</label>
                            <input type="text" name="code" value="{{ old('code') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: SRV-BEASISWA-2024">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Judul Template</label>
                            <input type="text" name="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Contoh: Survey Kelayakan Penerima Beasiswa">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="border-t pt-6 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Pertanyaan</h3>
                            <button type="button" onclick="addQuestion()" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1 rounded hover:bg-indigo-100">+ Tambah Pertanyaan</button>
                        </div>

                        <div id="questions-container" class="space-y-4">
                            <!-- Questions will be added here via JS -->
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('laz.survey-templates.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Batal</a>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Template</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Question Template for JS -->
    <template id="question-template">
        <div class="question-item bg-gray-50 p-4 rounded-lg border border-gray-200 relative mb-4">
            <button type="button" onclick="removeQuestion(this)" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-6">
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
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500">Bobot (Poin)</label>
                    <input type="number" name="questions[INDEX][weight]" value="0" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div class="md:col-span-1 pt-6 text-center">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="questions[INDEX][is_required]" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <span class="ml-2 text-xs text-gray-600">Wajib</span>
                    </label>
                </div>
                
                <!-- Options Container -->
                <div class="md:col-span-12 hidden options-container">
                    <div class="bg-white p-3 rounded border border-gray-200">
                        <label class="block text-xs font-medium text-gray-500 mb-2">Opsi Jawaban & Skor</label>
                        <div class="options-list space-y-2">
                            <!-- Options added here -->
                        </div>
                        <button type="button" onclick="addOption(this)" class="mt-2 text-xs text-indigo-600 font-medium hover:text-indigo-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Opsi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="option-template">
        <div class="option-row flex items-center gap-2">
            <input type="text" name="questions[INDEX][options][OPT_INDEX][text]" placeholder="Label Opsi" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-xs">
            <input type="number" name="questions[INDEX][options][OPT_INDEX][score]" placeholder="Skor" value="0" class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-xs">
            <button type="button" onclick="removeOption(this)" class="text-gray-400 hover:text-red-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </template>

    <script>
        let questionIndex = 0;

        function addQuestion() {
            const container = document.getElementById('questions-container');
            const template = document.getElementById('question-template');
            const clone = template.content.cloneNode(true);
            
            // Set unique index
            const currentIndex = questionIndex;
            
            // Replace INDEX placeholder
            const elements = clone.querySelectorAll('[name*="INDEX"]');
            elements.forEach(el => {
                el.name = el.name.replace('INDEX', currentIndex);
            });
            
            // Set data-index attribute for option adding
            clone.querySelector('.question-item').dataset.index = currentIndex;

            container.appendChild(clone);
            questionIndex++;
        }

        function removeQuestion(btn) {
            btn.closest('.question-item').remove();
        }

        function toggleOptions(select) {
            const container = select.closest('.question-item').querySelector('.options-container');
            if (['select', 'radio', 'checkbox'].includes(select.value)) {
                container.classList.remove('hidden');
                // Add initial option if empty
                const list = container.querySelector('.options-list');
                if (list.children.length === 0) {
                    addOption(container.querySelector('button'));
                }
            } else {
                container.classList.add('hidden');
            }
        }

        function addOption(btn) {
            const questionItem = btn.closest('.question-item');
            const list = questionItem.querySelector('.options-list');
            const qIndex = questionItem.dataset.index;
            const optIndex = list.children.length; // Simple index based on count

            const template = document.getElementById('option-template');
            const clone = template.content.cloneNode(true);

            // Replace placeholders
            const inputs = clone.querySelectorAll('input');
            inputs.forEach(input => {
                input.name = input.name.replace('INDEX', qIndex).replace('OPT_INDEX', optIndex);
            });

            list.appendChild(clone);
        }

        function removeOption(btn) {
            btn.closest('.option-row').remove();
        }

        // Add one question by default
        document.addEventListener('DOMContentLoaded', () => {
            addQuestion();
        });
    </script>
</x-app-layout>
