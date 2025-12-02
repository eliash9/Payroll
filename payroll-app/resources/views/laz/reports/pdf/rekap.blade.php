<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekap LAZ</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        h2 { margin-top: 20px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        h1 { text-align: center; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <h1>{{ $companyName }}</h1>
    <h1>Laporan Rekapitulasi LAZ</h1>

    <h2>Ringkas per Program</h2>
    <table>
        <thead>
            <tr>
                <th>Program</th>
                <th>Diajukan</th>
                <th>Disetujui</th>
                <th>Ditolak</th>
                <th>Dana Diminta</th>
                <th>Dana Disetujui</th>
                <th>Dana Disalurkan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($perProgram as $row)
                <tr>
                    <td>{{ $row->program->name ?? '-' }}</td>
                    <td class="text-center">{{ $row->total_submitted }}</td>
                    <td class="text-center">{{ $row->total_approved }}</td>
                    <td class="text-center">{{ $row->total_rejected }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_requested,0,',','.') }}</td>
                    <td class="text-right">Rp {{ number_format($perProgramApproved[$row->program_id] ?? 0,0,',','.') }}</td>
                    <td class="text-right">Rp {{ number_format($perProgramDisbursed[$row->program_id] ?? 0,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Rekap per Bulan</h2>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Jumlah</th>
                <th>Dana Diminta</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($perMonth as $row)
                <tr>
                    <td>{{ $row->month }}</td>
                    <td class="text-center">{{ $row->total }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_requested,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>Segmentasi Pemohon</h2>
    <table>
        <thead>
            <tr>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Dana Diminta</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($segmentApplicant as $row)
                <tr>
                    <td>{{ ucfirst($row->applicant_type) }}</td>
                    <td class="text-center">{{ $row->total }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_requested,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Wilayah (Provinsi)</h2>
    <table>
        <thead>
            <tr>
                <th>Provinsi</th>
                <th>Jumlah</th>
                <th>Dana Diminta</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($segmentProvince as $row)
                <tr>
                    <td>{{ $row->location_province }}</td>
                    <td class="text-center">{{ $row->total }}</td>
                    <td class="text-right">Rp {{ number_format($row->total_requested,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
