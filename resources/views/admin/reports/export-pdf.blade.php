<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penjualan ShoeCycle</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            color: #2563eb;
            text-transform: uppercase;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #666;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .summary-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .summary-box table {
            width: 100%;
        }

        .summary-title {
            font-weight: bold;
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
        }

        table.main-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        table.main-table th {
            background-color: #2563eb;
            color: white;
            padding: 10px;
            text-align: left;
            text-transform: uppercase;
        }

        table.main-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        table.main-table tr:nth-child(even) {
            background-color: #f1f5f9;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>ShoeCycle</h1>
        <p>Laporan Penjualan Resmi Platform E-Commerce Sepatu</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Periode:</strong> {{ $monthName }} {{ $year }}</td>
            <td class="text-right"><strong>Dicetak pada:</strong> {{ $dateGenerated }}</td>
        </tr>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td width="50%">
                    <div class="summary-title">Total Akumulasi Pendapatan</div>
                    <div class="summary-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                </td>
                <td width="50%">
                    <div class="summary-title">Jumlah Transaksi Berhasil</div>
                    <div class="summary-value">{{ $reports->count() }} Transaksi</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No. Invoice</th>
                <th>Nama Pelanggan</th>
                <th class="text-right">Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reports as $index => $trx)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                    <td class="font-bold">#{{ $trx->invoice }}</td>
                    <td>{{ $trx->customer->name }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right font-bold" style="padding: 15px;">TOTAL AKUMULASI:</td>
                <td class="text-right font-bold" style="font-size: 14px; color: #2563eb;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem administrasi ShoeCycle &copy; {{ date('Y') }}
    </div>
</body>

</html>
