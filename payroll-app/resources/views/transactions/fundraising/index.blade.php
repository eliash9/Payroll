<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transaksi Fundraising</h2>
            <div></div>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4 space-y-4">
            <div class="flex justify-between items-end">
                <form method="get" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="text-sm">Relawan</label>
                        <select name="fundraiser_id" class="border rounded px-3 py-2">
                            <option value="">Semua</option>
                            @foreach($fundraisers as $id => $name)
                                <option value="{{ $id }}" @selected(request('fundraiser_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Dari</label>
                        <input type="date" name="from" value="{{ request('from') }}" class="border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="text-sm">Sampai</label>
                        <input type="date" name="to" value="{{ request('to') }}" class="border rounded px-3 py-2">
                    </div>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
                </form>
                <a href="{{ route('fundraising.transactions.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded">Tambah Transaksi</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="bg-slate-100 text-left">
                        <th class="px-3 py-2">Tanggal</th>
                        <th class="px-3 py-2">Relawan</th>
                        <th class="px-3 py-2">Donatur</th>
                        <th class="px-3 py-2">Campaign</th>
                        <th class="px-3 py-2">Kategori</th>
                        <th class="px-3 py-2">Jumlah</th>
                        <th class="px-3 py-2">Sumber</th>
                        <th class="px-3 py-2">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($transactions as $tx)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ \Illuminate\Support\Carbon::parse($tx->date_received)->format('Y-m-d H:i') }}</td>
                            <td class="px-3 py-2">
                                <div class="font-semibold">{{ $tx->fundraiser_name }}</div>
                                <div class="text-xs text-slate-500">{{ $tx->employee_code }}</div>
                            </td>
                            <td class="px-3 py-2">{{ $tx->donor_name }}</td>
                            <td class="px-3 py-2">{{ $tx->campaign_name }}</td>
                            <td class="px-3 py-2">{{ $tx->category }}</td>
                            <td class="px-3 py-2">Rp {{ number_format($tx->amount,0,',','.') }}</td>
                            <td class="px-3 py-2">{{ $tx->source }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 rounded text-xs {{ $tx->status === 'verified' ? 'bg-green-100 text-green-800' : ($tx->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($tx->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                {{ $transactions->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
