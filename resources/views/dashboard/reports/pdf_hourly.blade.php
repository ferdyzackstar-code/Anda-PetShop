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
        }

        /* ── Header ── */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #1565C0;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header-logo {
            display: table-cell;
            width: 64px;
            vertical-align: middle;
        }

        .header-logo img {
            width: 56px;
            height: 56px;
            border-radius: 10px;
            object-fit: cover;
        }

        .header-logo-placeholder {
            width: 56px;
            height: 56px;
            border-radius: 10px;
            background: #1565C0;
            text-align: center;
            line-height: 56px;
            color: #fff;
            font-size: 22px;
            font-weight: bold;
        }

        .header-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .header-info .store-name {
            font-size: 16px;
            font-weight: bold;
            color: #1565C0;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .header-info .store-sub {
            font-size: 9px;
            color: #636e72;
            margin-top: 2px;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
        }

        .header-right .doc-title {
            font-size: 11px;
            font-weight: bold;
            color: #1565C0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-right .doc-sub {
            font-size: 9px;
            color: #636e72;
            margin-top: 3px;
        }

        /* ── Meta info ── */
        .meta {
            background: #f0f4ff;
            border-left: 4px solid #1565C0;
            padding: 7px 12px;
            border-radius: 4px;
            margin-bottom: 14px;
            font-size: 9.5px;
            color: #2d3436;
        }

        .meta strong {
            color: #1565C0;
        }

        /* ── Table ── */
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

        /* ── Footer ── */
        .footer {
            margin-top: 24px;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            vertical-align: bottom;
            font-size: 8.5px;
            color: #b2bec3;
        }

        .footer-right {
            display: table-cell;
            text-align: right;
            font-size: 9px;
            color: #2d3436;
        }

        .footer-right .sign-line {
            margin-top: 48px;
            border-top: 1px solid #2d3436;
            display: inline-block;
            width: 160px;
            text-align: center;
            padding-top: 4px;
            font-weight: bold;
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
        $storeEmail = \App\Models\SettingApp::get('store_email', '');
    @endphp

    {{-- ── Header ── --}}
    <div class="header">
        <div class="header-logo">
            @if ($hasLogo)
                <img src="{{ public_path($logoPath) }}" alt="{{ $storeName }}">
            @else
                <div class="header-logo-placeholder">🐾</div>
            @endif
        </div>
        <div class="header-info">
            <div class="store-name">{{ $storeName }}</div>
            @if ($storeAddr)
                <div class="store-sub">{{ $storeAddr }}</div>
            @endif
            @if ($storePhone)
                <div class="store-sub">Telp: {{ $storePhone }}</div>
            @endif
            @if ($storeEmail)
                <div class="store-sub">{{ $storeEmail }}</div>
            @endif
        </div>
        <div class="header-right">
            <div class="doc-title">Laporan Transaksi</div>
            <div class="doc-title">Per-Jam</div>
            <div class="doc-sub">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
        </div>
    </div>

    {{-- ── Meta Info ── --}}
    <div class="meta">
        Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('j F Y') }}</strong>
        s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('j F Y') }}</strong>
    </div>

    {{-- ── Tabel ── --}}
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

    {{-- ── Footer ── --}}
    <div class="footer">
        <div class="footer-left">
            {{ $storeName }} &copy; {{ now()->format('Y') }} — Dokumen ini digenerate secara otomatis oleh sistem.
        </div>
        <div class="footer-right">
            <div>{{ $storeAddr ? $storeAddr . ', ' : '' }}{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
            <div class="sign-line">Manager Operasional</div>
        </div>
    </div>

</body>

</html>
