<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Karyawan</h2>
            <a href="{{ route('employees.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto">
        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif
        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto space-y-4">
            <form method="get" class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="text-sm">Cari</label>
                    <input name="q" value="{{ request('q') }}" class="border rounded px-3 py-2" placeholder="Nama / Kode">
                </div>
                <div>
                    <label class="text-sm">Urut</label>
                    <select name="sort" class="border rounded px-3 py-2">
                        <option value="full_name" @selected(request('sort','full_name')==='full_name')>Nama</option>
                        <option value="employee_code" @selected(request('sort')==='employee_code')>Kode</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm">Arah</label>
                    <select name="dir" class="border rounded px-3 py-2">
                        <option value="asc" @selected(request('dir','asc')==='asc')>ASC</option>
                        <option value="desc" @selected(request('dir')==='desc')>DESC</option>
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            </form>

            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Kode</th>
                    <th class="px-3 py-2">Nama</th>
                    <th class="px-3 py-2">Cabang</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Relawan?</th>
                    <th class="px-3 py-2">Gaji Pokok</th>
                    <th class="px-3 py-2">Per Jam</th>
                    <th class="px-3 py-2">Komisi %</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($employees as $emp)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $emp->employee_code }}</td>
                        <td class="px-3 py-2">{{ $emp->full_name }}</td>
                        <td class="px-3 py-2">{{ $emp->branch_name }}</td>
                        <td class="px-3 py-2">{{ ucfirst($emp->status) }}</td>
                        <td class="px-3 py-2">{{ $emp->is_volunteer ? 'Ya' : 'Tidak' }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($emp->basic_salary,0,',','.') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($emp->hourly_rate,0,',','.') }}</td>
                        <td class="px-3 py-2">{{ $emp->commission_rate }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-emerald-600 font-medium hover:underline" href="{{ route('employees.show', $emp->id) }}">Detail</a>
                            <a class="text-blue-600 hover:underline" href="{{ route('employees.edit', $emp->id) }}">Edit</a>
                            <form method="post" action="{{ route('employees.destroy', $emp->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus karyawan?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
