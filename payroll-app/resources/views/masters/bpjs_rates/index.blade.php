<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tarif BPJS</h2>
            <a href="{{ route('bpjs-rates.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Tambah</a>
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
                    <th class="px-3 py-2">Program</th>
                    <th class="px-3 py-2">Perusahaan %</th>
                    <th class="px-3 py-2">Karyawan %</th>
                    <th class="px-3 py-2">Cap Min</th>
                    <th class="px-3 py-2">Cap Max</th>
                    <th class="px-3 py-2">Periode</th>
                    <th class="px-3 py-2">Perusahaan</th>
                    <th class="px-3 py-2">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rates as $rate)
                    <tr class="border-t">
                        <td class="px-3 py-2 uppercase">{{ $rate->program }}</td>
                        <td class="px-3 py-2">{{ $rate->employer_rate }}%</td>
                        <td class="px-3 py-2">{{ $rate->employee_rate }}%</td>
                        <td class="px-3 py-2">{{ $rate->salary_cap_min }}</td>
                        <td class="px-3 py-2">{{ $rate->salary_cap_max }}</td>
                        <td class="px-3 py-2">{{ $rate->effective_from }} - {{ $rate->effective_to ?? '...' }}</td>
                        <td class="px-3 py-2">{{ $rate->company_name }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <a class="text-blue-600 underline" href="{{ route('bpjs-rates.edit', $rate->id) }}">Edit</a>
                            <form method="post" action="{{ route('bpjs-rates.destroy', $rate->id) }}" class="inline">
                                @csrf
                                @method('delete')
                                <button class="text-red-600 underline" onclick="return confirm('Hapus tarif BPJS?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-3 py-4 text-center text-slate-500">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $rates->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
