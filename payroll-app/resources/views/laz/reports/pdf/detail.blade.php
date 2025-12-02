<!DOCTYPE html>
<html>
<head>
    <title>Laporan Detail LAZ</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2 class="text-center">{{ $companyName }}</h2>
    <h2>Laporan Detail Permohonan LAZ</h2>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Program</th>
                <th>Periode</th>
                <th>Pemohon</th>
                <th>Cabang</th>
                <th>Jumlah Diminta</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $app)
            <tr>
                <td>{{ $app->code }}</td>
                <td>{{ $app->program->name ?? '-' }}</td>
                <td>{{ $app->period->name ?? '-' }}</td>
                <td>
                    {{ $app->applicant_name }}
                    <br><small>{{ ucfirst($app->applicant_type) }}</small>
                </td>
                <td>{{ $app->branch->name ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($app->requested_amount, 0, ',', '.') }}</td>
                <td>{{ ucfirst($app->status) }}</td>
                <td>{{ $app->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
