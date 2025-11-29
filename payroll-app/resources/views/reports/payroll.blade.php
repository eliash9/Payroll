<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Gaji') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('reports.payroll') }}" class="mb-6 flex gap-4 items-end">
                        <div>
                            <x-input-label for="period_id" :value="__('Periode Payroll')" />
                            <select id="period_id" name="period_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Periode --</option>
                                @foreach($periods as $id => $name)
                                    <option value="{{ $id }}" {{ request('period_id') == $id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-primary-button>
                            {{ __('Tampilkan') }}
                        </x-primary-button>
                    </form>

                    @if($reportData)
                        <div class="mb-4 flex gap-2">
                            <a href="{{ route('reports.payroll', ['period_id' => request('period_id'), 'export' => 'excel']) }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">Export Excel</a>
                            <a href="{{ route('reports.payroll', ['period_id' => request('period_id'), 'export' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700">Export PDF</a>
                        </div>
                        <div class="overflow-x-auto">
                            @include('reports.payroll_table')
                        </div>
                    @elseif(request('period_id'))
                         <p class="text-gray-500 text-center py-4">Data tidak ditemukan.</p>
                    @else
                        <p class="text-gray-500 text-center py-4">Silakan pilih periode untuk melihat laporan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
