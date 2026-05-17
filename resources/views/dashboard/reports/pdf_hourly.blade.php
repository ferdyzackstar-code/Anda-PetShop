<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Per-Jam — Anda Petshop</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #2d3436;
            background: #fff;
            padding: 0 30px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1565C0;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .header img {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            object-fit: cover;
            margin-bottom: 6px;
        }

        .header-logo-placeholder {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            background: #1565C0;
            color: #fff;
            font-size: 20px;
            line-height: 52px;
            margin: 0 auto 6px auto;
        }

        .store-name {
            font-size: 15px;
            font-weight: bold;
            color: #1565C0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .store-sub {
            font-size: 9px;
            color: #636e72;
            margin-top: 2px;
        }

        .doc-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 6px;
            color: #2d3436;
        }

        .meta {
            margin-bottom: 14px;
            font-size: 9.5px;
            color: #2d3436;
        }

        .meta strong {
            color: #1565C0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 9.5px;
        }

        thead tr.row-main th {
            background-color: #1565C0;
            color: #fff;
            border: 1px solid #fff;
            padding: 8px 3px;
            font-size: 9px;
        }

        thead tr.row-sub th.th-completed {
            background-color: #1cc88a;
            color: #fff;
            border: 1px solid #fff;
            padding: 5px 3px;
        }

        thead tr.row-sub th.th-pending {
            background-color: #f6c23e;
            color: #fff;
            border: 1px solid #fff;
            padding: 5px 3px;
        }

        thead tr.row-sub th.th-cancelled {
            background-color: #e74a3b;
            color: #fff;
            border: 1px solid #fff;
            padding: 5px 3px;
        }

        thead tr.row-sub th.th-cash {
            background-color: #1cc88a;
            color: #fff;
            border: 1px solid #fff;
            padding: 5px 3px;
        }

        thead tr.row-sub th.th-transfer {
            background-color: #36b9cc;
            color: #fff;
            border: 1px solid #fff;
            padding: 5px 3px;
        }

        tbody tr.even {
            background: #fff;
        }

        tbody tr.odd {
            background: #f8f9fc;
        }

        tbody td {
            border: 1px solid #dee2e6;
            padding: 7px 3px;
        }

        tbody td.td-revenue {
            text-align: right;
            padding-right: 6px;
            font-weight: bold;
            color: #1cc88a;
        }

        tbody td.td-total {
            font-weight: bold;
            color: #1565C0;
        }

        tfoot tr td {
            background: #f6c23e;
            color: #fff;
            font-weight: bold;
            border: 1px solid #fff;
            padding: 8px 3px;
        }

        tfoot tr td.tf-revenue {
            text-align: right;
            padding-right: 6px;
        }

        .signature {
            margin-top: 30px;
            text-align: right;
            font-size: 9.5px;
        }

        .signature .sign-place {
            margin-bottom: 40px;
        }

        .signature .sign-line {
            border-top: 1px solid #2d3436;
            display: inline-block;
            width: 150px;
            text-align: center;
            padding-top: 4px;
            font-weight: bold;
            font-size: 9px;
        }

        .doc-footer {
            margin-top: 20px;
            font-size: 8px;
            color: #b2bec3;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 8px;
        }
    </style>
</head>

<body>

    @php
        $logo = \App\Models\SettingApp::get('app_image');
        $logoPath = 'storage/' . $logo;
        $hasLogo = $logo && file_exists(public_path($logoPath));
        $storeName = \App\Models\SettingApp::get('app_name', 'Anda Petshop');
        $storeAddr = \App\Models\SettingApp::get('store_address', '');
        $storePhone = \App\Models\SettingApp::get('store_phone', '');
        $storeCity = $storeAddr ? explode(',', $storeAddr)[0] : 'Bekasi';
    @endphp

    <div class="header">
        @if ($hasLogo)
            <img src="{{ public_path($logoPath) }}" alt="{{ $storeName }}">
        @else
            <div class="header-logo-placeholder">🐾</div>
        @endif
        <div class="store-name">{{ $storeName }}</div>
        @if ($storeAddr)
            <div class="store-sub">{{ $storeAddr }}</div>
        @endif
        @if ($storePhone)
            <div class="store-sub">Telp: {{ $storePhone }}</div>
        @endif
        <div class="doc-title">Laporan Transaksi Per-Jam</div>
    </div>

    <div class="meta">
        Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('j F Y') }}</strong>
        s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('j F Y') }}</strong><br>
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
    </div>

    <table>
        <thead>
            <tr class="row-main">
                <th rowspan="2" style="width:4%;">NO</th>
                <th rowspan="2" style="width:16%;">WAKTU</th>
                <th colspan="3">STATUS</th>
                <th colspan="2">METODE PEMBAYARAN</th>
                <th rowspan="2" style="width:12%;">TOTAL TRANSAKSI</th>
                <th rowspan="2" style="width:18%;">ESTIMASI KEUNTUNGAN</th>
            </tr>
            <tr class="row-sub">
                <th class="th-completed">Completed</th>
                <th class="th-pending">Pending</th>
                <th class="th-cancelled">Cancelled</th>
                <th class="th-cash">Cash</th>
                <th class="th-transfer">Transfer</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tableData as $index => $row)
                <tr class="{{ $index % 2 == 0 ? 'even' : 'odd' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['hour_formatted'] }}</td>
                    <td>{{ $row['completed'] }}</td>
                    <td>{{ $row['pending'] }}</td>
                    <td>{{ $row['cancelled'] }}</td>
                    <td>{{ $row['cash'] }}</td>
                    <td>{{ $row['transfer'] }}</td>
                    <td class="td-total">{{ $row['total_trx'] }}</td>
                    <td class="td-revenue">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="padding:16px; color:#b2bec3;">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">TOTAL</td>
                <td>{{ $totals['completed'] }}</td>
                <td>{{ $totals['pending'] }}</td>
                <td>{{ $totals['cancelled'] }}</td>
                <td>{{ $totals['cash'] }}</td>
                <td>{{ $totals['transfer'] }}</td>
                <td>{{ $totals['total_trx'] }}</td>
                <td class="tf-revenue">Rp {{ number_format($totals['revenue'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div class="sign-place">
            {{ $storeCity }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
        </div>
        <div class="sign-line">Manager Operasional</div>
    </div>

    <div class="doc-footer">
        {{ $storeName }} &copy; {{ now()->format('Y') }} &mdash; Dokumen ini digenerate secara otomatis oleh
        sistem.
    </div>

</body>

</html>
