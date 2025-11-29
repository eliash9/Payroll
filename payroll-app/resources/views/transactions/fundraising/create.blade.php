<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Transaksi Fundraising
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="post" action="{{ route('fundraising.transactions.store') }}" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="fundraiser_id" value="Relawan" />
                                <select id="fundraiser_id" name="fundraiser_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="">Pilih Relawan</option>
                                    @foreach($fundraisers as $f)
                                        <option value="{{ $f->id }}" @selected(old('fundraiser_id') == $f->id)>{{ $f->full_name }} ({{ $f->employee_code }})</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('fundraiser_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="date_received" value="Tanggal Terima" />
                                <x-text-input id="date_received" name="date_received" type="datetime-local" class="mt-1 block w-full" :value="old('date_received', now()->format('Y-m-d\TH:i'))" required />
                                <x-input-error :messages="$errors->get('date_received')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="amount" value="Jumlah (IDR)" />
                                <x-text-input id="amount" name="amount" type="number" class="mt-1 block w-full text-right" :value="old('amount')" required min="0" />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="source" value="Sumber" />
                                <select id="source" name="source" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    @foreach(['offline','online','event','qr','transfer','other'] as $s)
                                        <option value="{{ $s }}" @selected(old('source') == $s)>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('source')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="category" value="Kategori" />
                                <select id="category" name="category" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="">Pilih Kategori</option>
                                    @foreach(['zakat','infaq','shodaqoh','wakaf','donation','other'] as $c)
                                        <option value="{{ $c }}" @selected(old('category') == $c)>{{ ucfirst($c) }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('category')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="campaign_name" value="Nama Campaign" />
                                <x-text-input id="campaign_name" name="campaign_name" type="text" class="mt-1 block w-full" :value="old('campaign_name')" />
                                <x-input-error :messages="$errors->get('campaign_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="donor_name" value="Nama Donatur" />
                                <x-text-input id="donor_name" name="donor_name" type="text" class="mt-1 block w-full" :value="old('donor_name')" />
                                <x-input-error :messages="$errors->get('donor_name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="donor_phone" value="No HP Donatur" />
                                <x-text-input id="donor_phone" name="donor_phone" type="text" class="mt-1 block w-full" :value="old('donor_phone')" />
                                <x-input-error :messages="$errors->get('donor_phone')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" value="Status Verifikasi" />
                                <select id="status" name="status" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                    <option value="pending" @selected(old('status') == 'pending')>Pending</option>
                                    <option value="verified" @selected(old('status') == 'verified')>Verified</option>
                                    <option value="rejected" @selected(old('status') == 'rejected')>Rejected</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Hanya status Verified yang dihitung komisi.</p>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="notes" value="Catatan" />
                            <textarea id="notes" name="notes" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                            <a href="{{ route('fundraising.transactions.index') }}" class="text-gray-600 hover:text-gray-900">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
