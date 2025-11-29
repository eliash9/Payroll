<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tarif Pajak (PPh21)</h2>
            <a href="{{ route('tax-rates.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
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
                    <th class="px-3 py-2">Tahun</th>
                    <th class="px-3 py-2">Range Min</th>
                    <th class="px-3 py-2">Range Max</th>
                    <th class="px-3 py-2">Tarif %</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($taxRates as $rate)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $rate->year }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($rate->range_min, 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right">{{ $rate->range_max ? number_format($rate->range_max, 0, ',', '.') : 'Tak Terbatas' }}</td>
                        <td class="px-3 py-2">{{ $rate->rate_percent }}%</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('tax-rates.edit', $rate->id) }}">Edit</a>
                            <form method="post" action="{{ route('tax-rates.destroy', $rate->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus tarif pajak?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $taxRates->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
