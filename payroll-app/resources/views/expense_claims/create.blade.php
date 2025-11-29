<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ajukan Klaim Pengeluaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="post" action="{{ route('expense-claims.store') }}" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="employee_id" value="Karyawan" />
                                <select id="employee_id" name="employee_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="">Pilih Karyawan</option>
                                    @foreach($employees as $e)
                                        <option value="{{ $e->id }}" @selected(old('employee_id') == $e->id)>{{ $e->full_name }} ({{ $e->employee_code }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('employee_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="date" value="Tanggal Pengeluaran" />
                                <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', now()->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('date')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="amount" value="Jumlah (IDR)" />
                                <x-text-input id="amount" name="amount" type="number" class="mt-1 block w-full text-right" :value="old('amount')" required min="0" />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="receipt_url" value="URL Bukti / Struk (Opsional)" />
                                <x-text-input id="receipt_url" name="receipt_url" type="url" class="mt-1 block w-full" :value="old('receipt_url')" placeholder="https://..." />
                                <x-input-error :messages="$errors->get('receipt_url')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="description" value="Keterangan" />
                            <textarea id="description" name="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Ajukan') }}</x-primary-button>
                            <a href="{{ route('expense-claims.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
