<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold">Daftar Slip</h1>
                @if($period)
                    <p class="text-sm text-slate-500">{{ $period->name }} ({{ $period->code }})</p>
                @else
                    <p class="text-sm text-slate-500">Belum ada periode</p>
                @endif
            </div>
            <form method="get" class="flex flex-wrap items-end gap-2">
                <div>
                    <label class="text-xs text-slate-600">Perusahaan</label>
                    <select name="company_id" class="border rounded px-2 py-1 text-sm">
                        @foreach($companies as $id => $name)
                            <option value="{{ $id }}" @selected($companyId == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-slate-600">Periode</label>
                    <select name="period" class="border rounded px-2 py-1 text-sm">
                        <option value="">Terbaru</option>
                        @foreach($periodsList as $p)
                            <option value="{{ $p->code }}" @selected(request('period') == $p->code)>{{ $p->code }} ({{ $p->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-slate-600">Karyawan</label>
                    <select name="employee_id" class="border rounded px-2 py-1 text-sm">
                        <option value="">Semua</option>
                        @foreach($employees as $id => $name)
                            <option value="{{ $id }}" @selected(request('employee_id')==$id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs text-slate-600">Cari nama/kode</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="border rounded px-2 py-1 text-sm" placeholder="Nama / Kode">
                </div>
                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Filter</button>
            </form>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto">
        <div class="bg-white shadow-sm rounded p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="bg-slate-100 text-left">
                        <th class="px-3 py-2">Nama</th>
                        <th class="px-3 py-2">Code</th>
                        <th class="px-3 py-2">Net</th>
                        <th class="px-3 py-2">Gross</th>
                        <th class="px-3 py-2">Relawan?</th>
                        <th class="px-3 py-2">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($headers as $h)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $h->full_name }}</td>
                            <td class="px-3 py-2">{{ $h->employee_code }}</td>
                            <td class="px-3 py-2">Rp {{ number_format($h->net_income,0,',','.') }}</td>
                            <td class="px-3 py-2">Rp {{ number_format($h->gross_income,0,',','.') }}</td>
                            <td class="px-3 py-2">{{ $h->is_volunteer ? 'Ya' : 'Tidak' }}</td>
                            <td class="px-3 py-2">
                                <a class="text-blue-600 underline" href="/payslip/{{ $h->payroll_period_id }}/{{ $h->employee_id }}">Lihat Slip</a>
                                <a class="text-emerald-600 underline ms-2" href="/payslip/{{ $h->payroll_period_id }}/{{ $h->employee_id }}?format=pdf">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-4 text-center text-slate-500">Belum ada data slip</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $headers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
