<!DOCTYPE html>
<html>

<head>
    <title>Laporan Harian PetShop</title>
    <style>
        /* Gaya CSS Sederhana khusus untuk PDF */
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>ANDA PETSHOP</h2>
        <p>Laporan Pendapatan Harian</p>
        <p>Periode: {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Hari, Tanggal</th>
                <th width="30%">Total Transaksi</th>
                <th width="30%">Revenue (Omset)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalKeseluruhan = 0;
                $no = 1;
            @endphp

            @forelse($dailyData as $date => $data)
                @php $totalKeseluruhan += $data['revenue']; @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-left">{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}
                    </td>
                    <td>{{ $data['total_transaksi'] }} Transaksi</td>
                    <td class="text-right">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tidak ada data transaksi pada bulan ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right fw-bold">TOTAL REVENUE KESELURUHAN</td>
                <td class="text-right fw-bold">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y H:i') }}</p>
        <p>Oleh: {{ auth()->user()->name ?? 'Administrator' }}</p>
    </div>

</body>

</html>
