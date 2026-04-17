<!DOCTYPE html>
<html>

<head>
    <title>Laporan Transaksi PetShop</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header p {
            margin: 5px 0;
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
        <h2 style="margin: 0;">ANDA PETSHOP</h2>
        <p><strong>Laporan Log Transaksi Kasir</strong></p>
        <p>Periode: {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Waktu</th>
                <th width="20%">Nama Kasir</th>
                <th width="15%">Status</th>
                <th width="20%">Metode</th>
                <th width="25%">Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @php $totalKeseluruhan = 0; @endphp
            @forelse($orders as $index => $order)
                @php $totalKeseluruhan += $order->total_amount; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-left">{{ $order->user->name ?? 'Unknown' }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>{{ ucfirst(optional($order->payment)->payment_method) ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada transaksi ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="5" class="text-right">TOTAL PENDAPATAN TERFILTER:</td>
                <td class="text-right">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
