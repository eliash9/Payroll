<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penerapan Masal Komponen Bisyarah') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('payroll-components.bulk-assign.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Component Selection -->
                    <div>
                        <x-input-label for="payroll_component_id" :value="__('Pilih Komponen')" />
                        <select name="payroll_component_id" id="payroll_component_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">-- Pilih Komponen --</option>
                            @foreach($components as $comp)
                                <option value="{{ $comp->id }}">{{ $comp->code }} - {{ $comp->name }} ({{ ucfirst($comp->type) }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('payroll_component_id')" class="mt-2" />
                    </div>

                    <!-- Amount -->
                    <div>
                        <x-input-label for="amount" :value="__('Nominal / Rate')" />
                        <x-text-input id="amount" class="block mt-1 w-full" type="number" name="amount" required placeholder="Contoh: 1500000" />
                        <p class="text-sm text-gray-500 mt-1">Masukkan angka tanpa titik/koma.</p>
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <!-- Target Selection -->
                    <div x-data="{ type: 'all' }">
                        <x-input-label for="target_type" :value="__('Target Penerima')" />
                        <select name="target_type" id="target_type" x-model="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="all">Semua Karyawan (Aktif)</option>
                            <option value="regular">Semua Karyawan Reguler</option>
                            <option value="volunteer">Semua Relawan</option>
                            <option value="branch">Berdasarkan Cabang</option>
                            <option value="department">Berdasarkan Departemen</option>
                            <option value="position">Berdasarkan Jabatan</option>
                        </select>

                        <!-- Dynamic Select based on Type -->
                        <div class="mt-4" x-show="type === 'branch'" style="display: none;">
                            <x-input-label for="branch_id" :value="__('Pilih Cabang')" />
                            <select name="target_value" :disabled="type !== 'branch'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($branches as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4" x-show="type === 'department'" style="display: none;">
                            <x-input-label for="department_id" :value="__('Pilih Departemen')" />
                            <select name="target_value" :disabled="type !== 'department'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departments as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-4" x-show="type === 'position'" style="display: none;">
                            <x-input-label for="position_id" :value="__('Pilih Jabatan')" />
                            <select name="target_value" :disabled="type !== 'position'" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($positions as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-4">
                        <a href="{{ route('payroll-components.index') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">Batal</a>
                        <x-primary-button onclick="return confirm('Apakah Anda yakin? Komponen ini akan ditambahkan/diupdate untuk seluruh karyawan yang sesuai kriteria.');">
                            {{ __('Terapkan Masal') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
