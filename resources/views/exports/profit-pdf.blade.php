<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .profit { color: green; font-weight: bold; }
        .loss { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Laporan Laba Rugi</p>
        <p>Periode: {{ request('dateFrom', now()->startOfMonth()->format('Y-m-d')) }} - {{ request('dateTo', now()->format('Y-m-d')) }}</p>
    </div>
    <table>
        <tr><td>Total Penjualan</td><td class="text-right">{{ number_format($data['total_sales'] ?? 0, 0, ',', '.') }}</td></tr>
        <tr><td>Harga Pokok Penjualan (HPP)</td><td class="text-right">{{ number_format($data['hpp'] ?? 0, 0, ',', '.') }}</td></tr>
        <tr style="font-weight: bold;"><td>Laba Kotor</td><td class="text-right">{{ number_format($data['gross_profit'] ?? 0, 0, ',', '.') }}</td></tr>
        <tr><td>Total Pengeluaran</td><td class="text-right">{{ number_format($data['total_expenses'] ?? 0, 0, ',', '.') }}</td></tr>
        <tr style="font-weight: bold;" class="{{ ($data['net_profit'] ?? 0) >= 0 ? 'profit' : 'loss' }}">
            <td>Laba Bersih</td>
            <td class="text-right">{{ number_format($data['net_profit'] ?? 0, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
