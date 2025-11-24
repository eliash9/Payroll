<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cuti & Lembur</h2>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto space-y-6">
        @if(session('success'))
            <div class="text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white shadow-sm rounded p-4 space-y-3">
                <h3 class="font-semibold">Ajukan Cuti</h3>
                <form method="post" action="{{ route('leaveovertime.leave.store') }}" class="space-y-3">
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
                        <label class="text-sm">Tipe Cuti</label>
                        <select name="leave_type_id" class="w-full border rounded px-2 py-2">
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}">{{ $lt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm">Start</label>
                            <input type="date" name="start_date" class="w-full border rounded px-2 py-2" value="{{ now()->toDateString() }}">
                        </div>
                        <div>
                            <label class="text-sm">End</label>
                            <input type="date" name="end_date" class="w-full border rounded px-2 py-2" value="{{ now()->toDateString() }}">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm">Total days</label>
                        <input type="number" step="0.5" name="total_days" class="w-full border rounded px-2 py-2" value="1">
                    </div>
                    <div>
                        <label class="text-sm">Status</label>
                        <select name="status" class="w-full border rounded px-2 py-2">
                            <option value="pending">pending</option>
                            <option value="approved">approved</option>
                            <option value="rejected">rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Reason</label>
                        <textarea name="reason" class="w-full border rounded px-2 py-2" rows="2"></textarea>
                    </div>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded p-4 space-y-3">
                <h3 class="font-semibold">Ajukan Lembur</h3>
                <form method="post" action="{{ route('leaveovertime.overtime.store') }}" class="space-y-3">
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
                        <input type="date" name="work_date" class="w-full border rounded px-2 py-2" value="{{ now()->toDateString() }}">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm">Start</label>
                            <input type="time" name="start_time" class="w-full border rounded px-2 py-2" value="18:00">
                        </div>
                        <div>
                            <label class="text-sm">End</label>
                            <input type="time" name="end_time" class="w-full border rounded px-2 py-2" value="20:00">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm">Total minutes</label>
                        <input type="number" name="total_minutes" class="w-full border rounded px-2 py-2" value="120">
                    </div>
                    <div>
                        <label class="text-sm">Status</label>
                        <select name="status" class="w-full border rounded px-2 py-2">
                            <option value="pending">pending</option>
                            <option value="approved">approved</option>
                            <option value="rejected">rejected</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Reason</label>
                        <textarea name="reason" class="w-full border rounded px-2 py-2" rows="2"></textarea>
                    </div>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </form>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
            <h3 class="font-semibold mb-3">Daftar Cuti (50 terakhir)</h3>
            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Karyawan</th>
                    <th class="px-3 py-2">Tanggal</th>
                    <th class="px-3 py-2">Total</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($leaves as $l)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $l->full_name }} ({{ $l->employee_code }})</td>
                        <td class="px-3 py-2">{{ $l->start_date }} s/d {{ $l->end_date }}</td>
                        <td class="px-3 py-2">{{ $l->total_days }} hari</td>
                        <td class="px-3 py-2">{{ $l->status }}</td>
                        <td class="px-3 py-2">
                            <form method="post" action="{{ route('leaveovertime.leave.status', $l->id) }}" class="flex gap-1">
                                @csrf
                                <select name="status" class="border rounded px-2 py-1 text-xs">
                                    <option value="pending" @if($l->status=='pending') selected @endif>pending</option>
                                    <option value="approved" @if($l->status=='approved') selected @endif>approved</option>
                                    <option value="rejected" @if($l->status=='rejected') selected @endif>rejected</option>
                                    <option value="cancelled" @if($l->status=='cancelled') selected @endif>cancelled</option>
                                </select>
                                <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
            <h3 class="font-semibold mb-3">Daftar Lembur (50 terakhir)</h3>
            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Karyawan</th>
                    <th class="px-3 py-2">Tanggal</th>
                    <th class="px-3 py-2">Total menit</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($overtimes as $o)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $o->full_name }} ({{ $o->employee_code }})</td>
                        <td class="px-3 py-2">{{ $o->work_date }}</td>
                        <td class="px-3 py-2">{{ $o->total_minutes }}</td>
                        <td class="px-3 py-2">{{ $o->status }}</td>
                        <td class="px-3 py-2">
                            <form method="post" action="{{ route('leaveovertime.overtime.status', $o->id) }}" class="flex gap-1">
                                @csrf
                                <select name="status" class="border rounded px-2 py-1 text-xs">
                                    <option value="pending" @if($o->status=='pending') selected @endif>pending</option>
                                    <option value="approved" @if($o->status=='approved') selected @endif>approved</option>
                                    <option value="rejected" @if($o->status=='rejected') selected @endif>rejected</option>
                                    <option value="cancelled" @if($o->status=='cancelled') selected @endif>cancelled</option>
                                </select>
                                <button class="bg-blue-600 text-white px-2 py-1 rounded text-xs">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
