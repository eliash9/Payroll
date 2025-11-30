<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Pengguna</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <form method="post" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="text-sm">Nama</label>
                    <input name="name" class="w-full border rounded px-3 py-2" required value="{{ old('name') }}">
                </div>
                <div>
                    <label class="text-sm">Email</label>
                    <input name="email" type="email" class="w-full border rounded px-3 py-2" required value="{{ old('email') }}">
                </div>
                <div>
                    <label class="text-sm">Company</label>
                    <select name="company_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">--Pilih company--</option>
                        @foreach($companies as $id => $name)
                            <option value="{{ $id }}" @selected(old('company_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm">Branch (Cabang)</label>
                    <select name="branch_id" class="w-full border rounded px-3 py-2">
                        <option value="">--Pilih cabang--</option>
                        @foreach($branches as $id => $name)
                            <option value="{{ $id }}" @selected(old('branch_id') == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm">LAZ Roles</label>
                    <div class="grid grid-cols-2 gap-2 mt-1 border rounded p-3 bg-gray-50">
                        @foreach($lazRoles as $id => $name)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="laz_roles[]" value="{{ $id }}" @checked(in_array($id, old('laz_roles', [])))>
                                <span class="text-sm">{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="text-sm">Role</label>
                    <select name="role" class="w-full border rounded px-3 py-2">
                        @foreach(['admin','hr','manager','volunteer'] as $role)
                            <option value="{{ $role }}" @selected(old('role','admin') === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm">Password</label>
                    <input name="password" type="password" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('users.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
