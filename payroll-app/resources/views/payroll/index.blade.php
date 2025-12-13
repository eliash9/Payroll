<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Periode Payroll</h2>
            @if($periods->count())
                <form method="post" action="{{ route('payroll.periods.generate.volunteer', $periods->first()->id) }}">
                    @csrf
                        <button class="bg-emerald-600 text-white px-3 py-1 rounded text-sm"
                            onclick="return confirm('Generate payroll relawan untuk periode terbaru?')">
                            Generate Payroll Relawan (periode terbaru)
                        </button>
                    </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto space-y-4">
        @if(session('success'))
            <div class="text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif
        <div class="bg-white shadow-sm rounded p-4 space-y-4 overflow-x-auto">
            <form method="get" class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="text-sm">Cari</label>
                    <input name="q" value="{{ request('q') }}" class="border rounded px-3 py-2" placeholder="Kode / Nama">
                </div>
                <div>
                    <label class="text-sm">Perusahaan</label>
                    <select name="company_id" class="border rounded px-3 py-2">
                        <option value="">Semua</option>
                        @foreach($companies as $id => $name)
                            <option value="{{ $id }}" @selected($companyId==$id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm">Status</label>
                    <select name="status" class="border rounded px-3 py-2">
                        <option value="">Semua</option>
                        @php
                            $statusMap = [
                                'draft' => 'Draft',
                                'calculated' => 'Dihitung',
                                'approved' => 'Disetujui',
                                'closed' => 'Ditutup'
                            ];
                        @endphp
                        @foreach(['draft','calculated','approved','closed'] as $st)
                            <option value="{{ $st }}" @selected(request('status')===$st)>{{ $statusMap[$st] }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
                <a href="{{ route('payroll.periods.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded">Tambah Periode</a>
            </form>

            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Kode</th>
                    <th class="px-3 py-2">Periode</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2 text-right">Slip</th>
                    <th class="px-3 py-2 text-right">Total Bersih</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($periods as $p)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $p->code }}</td>
                        <td class="px-3 py-2">
                            <div>{{ $p->start_date }} s/d {{ $p->end_date }}</div>
                            <div class="text-xs text-slate-500">{{ $p->name }}</div>
                        </td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-1 rounded text-xs {{ $p->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($p->status === 'calculated' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                {{ $statusMap[$p->status] ?? ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-right">{{ $p->slip_count ?? 0 }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($p->net_total ?? 0,0,',','.') }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a href="{{ route('payroll.periods.show', $p->id) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition">
                                Kelola Periode
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-3 py-4 text-center text-slate-500">Belum ada periode</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $periods->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
