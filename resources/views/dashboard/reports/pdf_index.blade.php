<!DOCTYPE html>
<html>

<head>
    <title>Laporan Penjualan Resmi</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        .kop-surat h1 {
            margin: 0;
            font-size: 24px;
            color: #4e73df;
        }

        .kop-surat p {
            margin: 2px;
            font-size: 12px;
        }

        .info-laporan {
            margin-bottom: 20px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #4e73df;
            color: white;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .footer-table {
            margin-top: 10px;
        }

        .grand-total {
            background-color: #f8f9fc;
            font-weight: bold;
            font-size: 14px;
        }

        .ttd-container {
            margin-top: 50px;
            float: right;
            width: 200px;
            text-align: center;
        }

        .ttd-space {
            height: 80px;
        }
    </style>
</head>

<body>
    <div class="kop-surat">
        <h1>ANDA PETSHOP</h1>
        <p>Solusi Terbaik untuk Kebutuhan Hewan Peliharaan Anda</p>
        <p>Griya Kota Bekasi 2, Tambun Utara, Bekasi - Jawa Barat</p>
    </div>

    <div class="info-laporan">
        <strong>LAPORAN PENJUALAN UNIT</strong><br>
        Dicetak pada: {{ $date }}<br>
        Periode: {{ request('start_date') ?? 'Semua' }} s/d {{ request('end_date') ?? 'Hari Ini' }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Invoice</th>
                <th width="25%">Outlet</th>
                <th width="25%">Kasir</th>
                <th width="25%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $key => $t)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td><strong>{{ $t->invoice_number }}</strong></td>
                    <td>{{ $t->outlet->name }}</td>
                    <td>{{ $t->user->name }}</td>
                    <td class="text-right">Rp {{ number_format($t->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="grand-total">
                <td colspan="4" class="text-right">TOTAL PENDAPATAN</td>
                <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="ttd-container">
        <p>Bekasi, {{ date('d M Y') }}</p>
        <p>Manager Operasional,</p>
        <div class="ttd-space"></div>
        <p><strong>( ________________ )</strong></p>
    </div>
</body>

</html>
