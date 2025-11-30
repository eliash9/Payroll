<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan & Analitik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-xl font-semibold mb-4">Laporan & Analitik</h1>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="font-semibold mb-2">Ringkas per Program</h2>
                        <table class="w-full text-sm border">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="p-2 border">Program</th>
                                    <th class="p-2 border">Diajukan</th>
                                    <th class="p-2 border">Disetujui</th>
                                    <th class="p-2 border">Ditolak</th>
                                    <th class="p-2 border">Dana Diminta</th>
                                    <th class="p-2 border">Dana Disetujui</th>
                                    <th class="p-2 border">Dana Disalurkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($perProgram as $row)
                                    <tr>
                                        <td class="p-2 border">{{ $row->program->name ?? '-' }}</td>
                                        <td class="p-2 border text-center">{{ $row->total_submitted }}</td>
                                        <td class="p-2 border text-center">{{ $row->total_approved }}</td>
                                        <td class="p-2 border text-center">{{ $row->total_rejected }}</td>
                                        <td class="p-2 border text-right">Rp {{ number_format($row->total_requested,0,',','.') }}</td>
                                        <td class="p-2 border text-right">Rp {{ number_format($perProgramApproved[$row->program_id] ?? 0,0,',','.') }}</td>
                                        <td class="p-2 border text-right">Rp {{ number_format($perProgramDisbursed[$row->program_id] ?? 0,0,',','.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h2 class="font-semibold mb-2">Rekap per Bulan</h2>
                        <table class="w-full text-sm border">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="p-2 border">Bulan</th>
                                    <th class="p-2 border">Jumlah</th>
                                    <th class="p-2 border">Dana Diminta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($perMonth as $row)
                                    <tr>
                                        <td class="p-2 border">{{ $row->month }}</td>
                                        <td class="p-2 border text-center">{{ $row->total }}</td>
                                        <td class="p-2 border text-right">Rp {{ number_format($row->total_requested,0,',','.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <h2 class="font-semibold mb-2">Segmentasi Pemohon</h2>
                        <table class="w-full text-sm border">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="p-2 border">Tipe</th>
                                    <th class="p-2 border">Jumlah</th>
                                    <th class="p-2 border">Dana Diminta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($segmentApplicant as $row)
                                    <tr>
                                        <td class="p-2 border">{{ $row->applicant_type }}</td>
                                        <td class="p-2 border text-center">{{ $row->total }}</td>
                                        <td class="p-2 border text-right">Rp {{ number_format($row->total_requested,0,',','.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h2 class="font-semibold mb-2">Wilayah (Provinsi)</h2>
                        <table class="w-full text-sm border">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="p-2 border">Provinsi</th>
                                    <th class="p-2 border">Jumlah</th>
                                    <th class="p-2 border">Dana Diminta</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($segmentProvince as $row)
                                    <tr>
                                        <td class="p-2 border">{{ $row->location_province }}</td>
                                        <td class="p-2 border text-center">{{ $row->total }}</td>
                                        <td class="p-2 border text-right">Rp {{ number_format($row->total_requested,0,',','.') }}</td>
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
