<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 4px 0; }
        h2 { font-size: 14px; margin: 12px 0 6px 0; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 10px; background: #d1fae5; color: #065f46; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 6px; border: 1px solid #e5e7eb; }
        th { background: #f3f4f6; text-align: left; font-size: 11px; }
        .text-right { text-align: right; }
        .muted { color: #6b7280; font-size: 11px; }
        .summary { display: flex; gap: 12px; margin-top: 12px; }
        .card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px; flex: 1; }
        .card-title { font-size: 11px; color: #6b7280; margin-bottom: 4px; }
        .card-value { font-size: 14px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Slip Gaji / Insentif</h1>
            <div class="muted">{{ $period->name }} ({{ $period->code }})</div>
            <div class="muted">Periode: {{ $period->start_date }} s/d {{ $period->end_date }}</div>
        </div>
        <div style="text-align: right;">
            <div style="font-weight: 700;">{{ $employee->full_name }}</div>
            <div class="muted">{{ $employee->employee_code }}</div>
            @if($employee->is_volunteer)
                <span class="badge">Relawan</span>
            @endif
        </div>
    </div>

    <h2>Rincian Komponen</h2>
    <table>
        <thead>
        <tr>
            <th>Komponen</th>
            <th>Kode</th>
            <th>Jenis</th>
            <th class="text-right">Jumlah</th>
            <th class="text-right">Kuantitas</th>
        </tr>
        </thead>
        <tbody>
        @php $loanTotal = 0; @endphp
        @foreach($details as $d)
            @php if ($d->code === 'LOAN_INSTALLMENT') { $loanTotal += $d->amount; } @endphp
            <tr>
                <td>{{ $d->name }}</td>
                <td class="muted">{{ $d->code }}</td>
                <td>{{ ucfirst($d->type) }}</td>
                <td class="text-right">Rp {{ number_format($d->amount,0,',','.') }}</td>
                <td class="text-right">{{ $d->quantity ?? '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="card">
            <div class="card-title">Pendapatan Bruto</div>
            <div class="card-value">Rp {{ number_format($header->gross_income,0,',','.') }}</div>
        </div>
        <div class="card">
            <div class="card-title">Potongan</div>
            <div class="card-value">Rp {{ number_format($header->total_deduction,0,',','.') }}</div>
            @if($loanTotal !== 0)
                <div class="muted">Termasuk pinjaman: Rp {{ number_format($loanTotal,0,',','.') }}</div>
            @endif
        </div>
        <div class="card">
            <div class="card-title">Take Home Pay</div>
            <div class="card-value">Rp {{ number_format($header->net_income,0,',','.') }}</div>
        </div>
    </div>
</body>
</html>
