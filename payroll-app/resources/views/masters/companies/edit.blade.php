<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Perusahaan</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('companies.update', $company->id) }}" class="space-y-4">
                @csrf
                @method('put')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Nama</label>
                        <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name', $company->name) }}">
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="code" class="w-full border rounded px-3 py-2" value="{{ old('code', $company->code) }}">
                    </div>
                </div>
                <div>
                    <label class="text-sm">Alamat</label>
                    <textarea name="address" class="w-full border rounded px-3 py-2">{{ old('address', $company->address) }}</textarea>
                </div>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm">Telepon</label>
                        <input name="phone" class="w-full border rounded px-3 py-2" value="{{ old('phone', $company->phone) }}">
                    </div>
                    <div>
                        <label class="text-sm">Email</label>
                        <input name="email" type="email" class="w-full border rounded px-3 py-2" value="{{ old('email', $company->email) }}">
                    </div>
                    <div>
                        <label class="text-sm">NPWP</label>
                        <input name="npwp" class="w-full border rounded px-3 py-2" value="{{ old('npwp', $company->npwp) }}">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
                    <a href="{{ route('companies.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
