<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Klaim Pengeluaran</h2>
            <a href="{{ route('expense-claims.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded text-sm">Ajukan Klaim</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-sm rounded p-4 space-y-4">
            <form method="get" class="flex flex-wrap items-end gap-3">
                <div>
                    <label class="text-sm">Status</label>
                    <select name="status" class="border rounded px-3 py-2">
                        <option value="">Semua</option>
                        @foreach(['pending','approved','rejected'] as $s)
                            <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="bg-slate-100 text-left">
                        <th class="px-3 py-2">Tanggal</th>
                        <th class="px-3 py-2">Karyawan</th>
                        <th class="px-3 py-2">Keterangan</th>
                        <th class="px-3 py-2">Jumlah</th>
                        <th class="px-3 py-2">Bukti</th>
                        <th class="px-3 py-2">Status</th>
                        <th class="px-3 py-2">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($claims as $claim)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $claim->date->format('d/m/Y') }}</td>
                            <td class="px-3 py-2">
                                <div class="font-semibold">{{ $claim->employee->full_name }}</div>
                                <div class="text-xs text-slate-500">{{ $claim->employee->employee_code }}</div>
                            </td>
                            <td class="px-3 py-2">{{ $claim->description }}</td>
                            <td class="px-3 py-2 text-right">Rp {{ number_format($claim->amount,0,',','.') }}</td>
                            <td class="px-3 py-2">
                                @if($claim->receipt_url)
                                    <a href="{{ $claim->receipt_url }}" target="_blank" class="text-blue-600 underline">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 rounded text-xs {{ $claim->status === 'approved' ? 'bg-green-100 text-green-800' : ($claim->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($claim->status) }}
                                </span>
                                @if($claim->status === 'rejected' && $claim->rejection_reason)
                                    <div class="text-xs text-red-600 mt-1">{{ $claim->rejection_reason }}</div>
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if(auth()->user()->role === 'admin' && $claim->status === 'pending')
                                    <div class="flex gap-2">
                                        <form method="post" action="{{ route('expense-claims.update-status', $claim->id) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button class="text-green-600 hover:underline" onclick="return confirm('Setujui klaim ini?')">Approve</button>
                                        </form>
                                        <button type="button" class="text-red-600 hover:underline" onclick="document.getElementById('reject-modal-{{ $claim->id }}').showModal()">Reject</button>
                                        
                                        <dialog id="reject-modal-{{ $claim->id }}" class="p-4 rounded shadow-lg border w-96">
                                            <form method="post" action="{{ route('expense-claims.update-status', $claim->id) }}">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <h3 class="font-bold text-lg mb-2">Tolak Klaim</h3>
                                                <textarea name="rejection_reason" class="w-full border rounded p-2 mb-2" placeholder="Alasan penolakan..." required></textarea>
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" class="px-3 py-1 border rounded" onclick="document.getElementById('reject-modal-{{ $claim->id }}').close()">Batal</button>
                                                    <button class="px-3 py-1 bg-red-600 text-white rounded">Tolak</button>
                                                </div>
                                            </form>
                                        </dialog>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                {{ $claims->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
