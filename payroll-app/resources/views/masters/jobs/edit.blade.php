<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Profil Pekerjaan</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto" x-data="jobForm()">
        <form method="post" action="{{ route('jobs.update', $job->id) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="bg-white shadow-sm rounded p-6">
                <h3 class="text-lg font-medium mb-4">Identitas Pekerjaan</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Nama Pekerjaan (Title)</label>
                        <input name="title" class="w-full border rounded px-3 py-2" required value="{{ old('title', $job->title) }}">
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code', $job->code) }}">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm">Deskripsi Singkat</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $job->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Tanggung Jawab Pekerjaan</h3>
                    <button type="button" @click="addResponsibility" class="text-sm text-blue-600 hover:underline">+ Tambah</button>
                </div>
                <!-- Headers -->
                <div class="grid grid-cols-12 gap-2 text-sm text-slate-500 mb-2">
                    <div class="col-span-1 text-center">Utama?</div>
                    <div class="col-span-10">Tanggung Jawab</div>
                    <div class="col-span-1"></div>
                </div>

                <template x-for="(resp, index) in responsibilities" :key="index">
                    <div class="grid grid-cols-12 gap-2 mb-2 items-start">
                        <div class="col-span-1 flex justify-center pt-2">
                            <input type="hidden" :name="'responsibilities['+index+'][is_primary]'" :value="resp.is_primary ? 1 : 0">
                            <input type="checkbox" x-model="resp.is_primary" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        </div>
                        <div class="col-span-10">
                            <textarea :name="'responsibilities['+index+'][responsibility]'" x-model="resp.text" class="w-full border rounded px-3 py-2 text-sm" rows="2"></textarea>
                        </div>
                        <div class="col-span-1 flex justify-center pt-2">
                            <button type="button" @click="removeResponsibility(index)" class="text-red-500 hover:text-red-700">&times;</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="bg-white shadow-sm rounded p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">Kualifikasi Pekerjaan</h3>
                    <button type="button" @click="addRequirement" class="text-sm text-blue-600 hover:underline">+ Tambah</button>
                </div>
                 <!-- Headers -->
                 <div class="grid grid-cols-12 gap-2 text-sm text-slate-500 mb-2">
                    <div class="col-span-3">Tipe</div>
                    <div class="col-span-8">Kualifikasi</div>
                    <div class="col-span-1"></div>
                </div>

                <template x-for="(req, index) in requirements" :key="index">
                    <div class="grid grid-cols-12 gap-2 mb-2 items-start">
                        <div class="col-span-3">
                            <select :name="'requirements['+index+'][type]'" x-model="req.type" class="w-full border rounded px-2 py-2 text-sm">
                                <option value="education">Pendidikan</option>
                                <option value="experience">Pengalaman</option>
                                <option value="skill">Keahlian (Skill)</option>
                                <option value="certification">Sertifikasi</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-span-8">
                            <textarea :name="'requirements['+index+'][requirement]'" x-model="req.text" class="w-full border rounded px-3 py-2 text-sm" rows="2"></textarea>
                        </div>
                        <div class="col-span-1 flex justify-center pt-2">
                            <button type="button" @click="removeRequirement(index)" class="text-red-500 hover:text-red-700">&times;</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="flex gap-2">
                <button class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">Update Profil Pekerjaan</button>
                <a href="{{ route('jobs.index') }}" class="bg-slate-200 text-slate-700 px-6 py-2 rounded hover:bg-slate-300">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function jobForm() {
            return {
                responsibilities: @json($job->responsibilities->map(fn($r) => ['is_primary' => (bool)$r->is_primary, 'text' => $r->responsibility])),
                requirements: @json($job->requirements->map(fn($r) => ['type' => $r->type, 'text' => $r->requirement])),
                addResponsibility() {
                    this.responsibilities.push({ is_primary: true, text: '' });
                },
                removeResponsibility(index) {
                    this.responsibilities.splice(index, 1);
                },
                addRequirement() {
                    this.requirements.push({ type: 'education', text: '' });
                },
                removeRequirement(index) {
                    this.requirements.splice(index, 1);
                },
                init() {
                    if (this.responsibilities.length === 0) {
                        this.addResponsibility();
                    }
                    if (this.requirements.length === 0) {
                        this.addRequirement();
                    }
                }
            }
        }
    </script>
</x-app-layout>
