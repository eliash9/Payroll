<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pinjaman Karyawan</h2>
            <a href="{{ route('employee-loans.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-4 text-sm text-green-700 bg-green-100 border border-green-200 rounded px-3 py-2">
                {{ session('success') }}
            </div>
        @endif
        <div class="bg-white shadow-sm rounded p-4 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                <tr class="bg-slate-100 text-left">
                    <th class="px-3 py-2">Loan #</th>
                    <th class="px-3 py-2">Karyawan</th>
                    <th class="px-3 py-2">Company</th>
                    <th class="px-3 py-2 text-right">Principal</th>
                    <th class="px-3 py-2 text-right">Remaining</th>
                    <th class="px-3 py-2 text-right">Instalment</th>
                    <th class="px-3 py-2 text-right">Cicilan belum bayar</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($loans as $loan)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $loan->loan_number }}</td>
                        <td class="px-3 py-2">
                            <div class="font-semibold">{{ $loan->full_name }}</div>
                            <div class="text-xs text-slate-500">{{ $loan->employee_code }}</div>
                        </td>
                        <td class="px-3 py-2">{{ $loan->company_name }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($loan->principal_amount,0,',','.') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($loan->remaining_amount,0,',','.') }}</td>
                        <td class="px-3 py-2 text-right">Rp {{ number_format($loan->installment_amount,0,',','.') }}</td>
                        <td class="px-3 py-2 text-right">{{ $loan->unpaid_installments }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a href="{{ route('employee-loans.edit', $loan->id) }}" class="text-blue-600 underline text-xs">Edit</a>
                            <form method="post" action="{{ route('employee-loans.destroy', $loan->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline text-xs" onclick="return confirm('Hapus pinjaman ini? Jadwal cicilan ikut terhapus.')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-4 text-center text-slate-500">Belum ada pinjaman</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
