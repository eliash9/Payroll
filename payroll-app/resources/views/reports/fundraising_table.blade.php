<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Fundraiser</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Transaksi</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Donasi</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($reportData as $row)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $row->full_name }} <br>
                    <span class="text-xs text-gray-500">{{ $row->employee_code }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    {{ number_format($row->total_transactions, 0, ',', '.') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                    Rp {{ number_format($row->total_amount, 0, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    Tidak ada data fundraising untuk bulan ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
