<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Laporan Penjualan</p>
        <p>Periode: {{ request('dateFrom', now()->startOfMonth()->format('Y-m-d')) }} - {{ request('dateTo', now()->format('Y-m-d')) }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kasir</th>
                <th class="text-right">Total</th>
                <th class="text-right">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $s)
            <tr>
                <td>{{ $s->invoice_number }}</td>
                <td>{{ $s->transaction_date->format('d/m/Y') }}</td>
                <td>{{ $s->customer?->name ?? 'Umum' }}</td>
                <td>{{ $s->user?->name }}</td>
                <td class="text-right">{{ number_format($s->total_amount, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($s->grand_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">Total</th>
                <th class="text-right">{{ number_format($data->sum('total_amount'), 0, ',', '.') }}</th>
                <th class="text-right">{{ number_format($data->sum('grand_total'), 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
