<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard LAZ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-semibold mb-4">Dashboard</h1>

                <div class="grid md:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 bg-emerald-50 border rounded">
                        <p class="text-sm text-slate-600">Total Permohonan</p>
                        <p class="text-2xl font-bold">{{ $totalApplications }}</p>
                    </div>
                    <div class="p-4 bg-white border rounded">
                        <p class="text-sm text-slate-600">Permohonan Bulan Ini</p>
                        <p class="text-2xl font-bold">{{ $monthApplications }}</p>
                    </div>
                    <div class="p-4 bg-white border rounded">
                        <p class="text-sm text-slate-600">Disetujui</p>
                        <p class="text-2xl font-bold text-emerald-700">{{ $approved }}</p>
                    </div>
                    <div class="p-4 bg-white border rounded">
                        <p class="text-sm text-slate-600">Ditolak</p>
                        <p class="text-2xl font-bold text-red-600">{{ $rejected }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-4 mb-6">
                    <div class="p-4 bg-white border rounded">
                        <p class="text-sm text-slate-600">Total Dana Diminta</p>
                        <p class="text-xl font-semibold">Rp {{ number_format($sumRequested,0,',','.') }}</p>
                    </div>
                    <div class="p-4 bg-white border rounded">
                        <p class="text-sm text-slate-600">Total Dana Disetujui</p>
                        <p class="text-xl font-semibold">Rp {{ number_format($sumApproved,0,',','.') }}</p>
                    </div>
                    <div class="p-4 bg-white border rounded">
                        <p class="text-sm text-slate-600">Total Dana Disalurkan</p>
                        <p class="text-xl font-semibold">Rp {{ number_format($sumDisbursed,0,',','.') }}</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="font-semibold mb-2">Permohonan per Program</h2>
                        <table class="w-full text-sm border">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="p-2 border">Program</th>
                                    <th class="p-2 border">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($perProgram as $row)
                                    <tr>
                                        <td class="p-2 border">{{ $row->program->name ?? '-' }}</td>
                                        <td class="p-2 border text-center">{{ $row->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h2 class="font-semibold mb-2">Permohonan per Status</h2>
                        <table class="w-full text-sm border">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="p-2 border">Status</th>
                                    <th class="p-2 border">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($perStatus as $row)
                                    <tr>
                                        <td class="p-2 border">{{ $row->status }}</td>
                                        <td class="p-2 border text-center">{{ $row->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
