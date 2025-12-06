<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Email LAZ') }}
        </h2>
    </x-slot>
    

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('laz.settings.update') }}">
                    @csrf
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Konfigurasi Pengirim</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Pengirim</label>
                                <input type="email" name="email_sender_address" value="{{ old('email_sender_address', $settings['email_sender_address'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Pengirim</label>
                                <input type="text" name="email_sender_name" value="{{ old('email_sender_name', $settings['email_sender_name'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Template Email: Permohonan Masuk</h3>
                        <p class="text-sm text-gray-500 mb-2">Variabel yang tersedia: {code}, {applicant_name}, {program_name}, {date}</p>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Subjek</label>
                            <input type="text" name="email_new_request_subject" value="{{ old('email_new_request_subject', $settings['email_new_request_subject'] ?? 'Permohonan Bantuan Baru - {code}') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Isi Email</label>
                            <textarea name="email_new_request_body" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('email_new_request_body', $settings['email_new_request_body'] ?? "Halo {applicant_name},\n\nTerima kasih telah mengajukan permohonan bantuan.\nKode Tiket Anda: {code}\nProgram: {program_name}\nTanggal: {date}\n\nMohon simpan kode tiket ini untuk pengecekan status.\n\nSalam,\nTim LAZ") }}</textarea>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Template Email: Perubahan Status</h3>
                        <p class="text-sm text-gray-500 mb-2">Variabel yang tersedia: {code}, {applicant_name}, {status}, {program_name}</p>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Subjek</label>
                            <input type="text" name="email_status_update_subject" value="{{ old('email_status_update_subject', $settings['email_status_update_subject'] ?? 'Update Status Permohonan - {code}') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Isi Email</label>
                            <textarea name="email_status_update_body" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('email_status_update_body', $settings['email_status_update_body'] ?? "Halo {applicant_name},\n\nStatus permohonan bantuan Anda ({code}) telah diperbarui menjadi: {status}.\n\nSilakan cek detailnya di portal kami.\n\nSalam,\nTim LAZ") }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
