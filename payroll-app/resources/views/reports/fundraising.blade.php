<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Fundraising') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('reports.fundraising') }}" class="mb-6 flex gap-4 items-end">
                        <div>
                            <x-input-label for="month" :value="__('Bulan')" />
                            <input type="month" id="month" name="month" value="{{ $month }}" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        </div>
                        <x-primary-button>
                            {{ __('Tampilkan') }}
                        </x-primary-button>
                    </form>

                    <div class="mb-4 flex gap-2">
                        <a href="{{ route('reports.fundraising', ['month' => request('month', $month), 'export' => 'excel']) }}" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">Export Excel</a>
                        <a href="{{ route('reports.fundraising', ['month' => request('month', $month), 'export' => 'pdf']) }}" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700">Export PDF</a>
                    </div>

                    <div class="overflow-x-auto">
                        @include('reports.fundraising_table')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
