<!DOCTYPE html>
<html>

<head>
    <title>Laporan Tahunan PetShop</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 13px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>ANDA PETSHOP</h2>
        <p>Ringkasan Pendapatan Bulanan (Tahunan)</p>
        <p>Tahun: {{ $year }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th>Total Transaksi</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @php $totalYear = 0; @endphp
            @foreach ($reportData as $data)
                @php $totalYear += $data['revenue']; @endphp
                <tr>
                    <td class="text-left">{{ $data['month_name'] }}</td>
                    <td>{{ $data['total_transaksi'] }}</td>
                    <td class="text-right">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #eee; font-weight: bold;">
                <td colspan="2" class="text-right">TOTAL PENDAPATAN SETAHUN:</td>
                <td class="text-right">Rp {{ number_format($totalYear, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
