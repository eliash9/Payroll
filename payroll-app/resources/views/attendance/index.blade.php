<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Absensi Manual</h2>
                <p class="text-sm text-slate-500">Periode {{ $period }}</p>
            </div>
            <form method="get" class="flex items-center gap-2">
                <input type="month" name="period" value="{{ $period }}" class="border rounded px-2 py-1 text-sm">
                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Filter</button>
            </form>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto space-y-6">
        @if(session('success'))
            <div class="text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-sm rounded p-4">
            <h3 class="font-semibold mb-3">Input Check-in / Check-out</h3>
            <form method="post" action="{{ route('attendance.store') }}" class="grid md:grid-cols-4 gap-3 items-end">
                @csrf
                <div>
                    <label class="text-sm">Karyawan</label>
                    <select name="employee_id" class="w-full border rounded px-2 py-2">
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm">Tanggal</label>
                    <input type="date" name="work_date" value="{{ now()->toDateString() }}" class="w-full border rounded px-2 py-2">
                </div>
                <div>
                    <label class="text-sm">Jam</label>
                    <input type="time" name="time" value="{{ now()->format('H:i') }}" class="w-full border rounded px-2 py-2">
                </div>
                <div>
                    <label class="text-sm">Type</label>
                    <select name="type" class="w-full border rounded px-2 py-2">
                        <option value="in">Check-in</option>
                        <option value="out">Check-out</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex items-center gap-2">
                    <label class="text-sm">Worked minutes (opsional)</label>
                    <input type="number" name="worked_minutes" class="border rounded px-2 py-2">
                </div>
                <div class="md:col-span-4">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
            <h3 class="font-semibold mb-3">Rekap</h3>
            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Tanggal</th>
                    <th class="px-3 py-2">Karyawan</th>
                    <th class="px-3 py-2">Cabang</th>
                    <th class="px-3 py-2">Check-in</th>
                    <th class="px-3 py-2">Check-out</th>
                    <th class="px-3 py-2">Worked (menit)</th>
                    <th class="px-3 py-2">Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($summaries as $row)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $row->work_date }}</td>
                        <td class="px-3 py-2">{{ $row->full_name }} ({{ $row->employee_code }})</td>
                        <td class="px-3 py-2">{{ $row->branch_name }}</td>
                        <td class="px-3 py-2">{{ $row->check_in }}</td>
                        <td class="px-3 py-2">{{ $row->check_out }}</td>
                        <td class="px-3 py-2">{{ $row->worked_minutes }}</td>
                        <td class="px-3 py-2">{{ $row->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $summaries->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
