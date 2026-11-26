<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Statistik Unit Layanan Terpadu Polinema</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18px 26px 24px;
        }

        body {
            color: #202633;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
            margin: 0;
        }

        h1, h2, p {
            margin: 0;
        }

        .report-header {
            border-bottom: 2px solid #25205f;
            margin-bottom: 8px;
            padding-bottom: 7px;
            text-align: center;
        }

        .report-logo {
            height: 48px;
            margin-bottom: 3px;
            width: 48px;
        }

        .report-title {
            color: #25205f;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.35;
        }

        .report-meta {
            color: #505968;
            font-size: 8px;
            line-height: 1.45;
            margin-top: 4px;
        }

        .section-title {
            color: #25205f;
            font-size: 11px;
            margin: 8px 0 5px;
            page-break-after: avoid;
        }

        .summary-table, .report-table {
            border-collapse: collapse;
            width: 100%;
        }

        .summary-table {
            border-collapse: separate;
            border-spacing: 6px 5px;
        }

        .summary-table td {
            background: #f7f8fa;
            border: 1px solid #e4e7ec;
            height: 43px;
            padding: 5px 3px;
            text-align: center;
            width: 25%;
        }

        .summary-value {
            color: #25205f;
            display: block;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .summary-label {
            color: #505968;
            font-size: 7.5px;
            line-height: 1.25;
        }

        .report-table {
            table-layout: fixed;
            font-size: 7.5px;
        }

        .report-table th, .report-table td {
            border: 1px solid #cbd0da;
            padding: 5px 3px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .report-table th {
            background: #25205f;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .report-table tbody tr:nth-child(even) {
            background: #f5f6f9;
        }

        .text-center {
            text-align: center;
        }

        .chart-table {
            border-collapse: collapse;
            width: 100%;
        }

        .chart-table td {
            vertical-align: top;
        }

        .chart-title {
            color: #25205f;
            font-size: 10px;
            margin: 0 0 7px;
            text-align: center;
        }

        .bar-chart-image {
            display: block;
            height: auto;
            width: 100%;
        }

        .pie-chart-image {
            display: block;
            width: 100%;
            max-width: 300px;
            height: auto;
            margin: 0 auto;
        }

        .chart-legend {
            font-size: 8px;
            line-height: 1.6;
            margin: 0 0 8px;
        }

        .legend-dot {
            display: inline-block;
            height: 8px;
            margin-right: 4px;
            width: 8px;
        }

        .muted {
            color: #677080;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        section {
            page-break-inside: avoid;
        }

        .chart-section {
            margin: 0 0 12px;
            padding: 0 0 4px;
            page-break-inside: avoid;
        }

        .bar-legend {
            text-align: center;
            font-size: 7.5px;
            line-height: 1.5;
            margin: 10px 0 0;
        }

        .bar-legend span {
            display: inline-block;
            margin: 0 5px;
        }

        .pie-layout {
            table-layout: fixed;
            width: 100%;
        }

        .pie-layout td {
            vertical-align: middle;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <header class="report-header">
        @if ($logoDataUri)
            <div><img class="report-logo" src="{{ $logoDataUri }}" alt="Logo Politeknik Negeri Malang"></div>
        @endif
        <h1 class="report-title">LAPORAN STATISTIK<br>UNIT LAYANAN TERPADU POLINEMA</h1>
        <p class="report-meta">
            Periode: {{ $period }} &nbsp; | &nbsp; Unit Layanan: {{ $unitName }}<br>
            Tanggal pembuatan: {{ $createdAt }}
        </p>
    </header>

    <section>
        <h2 class="section-title">Ringkasan Statistik</h2>
        <table class="summary-table">
            <tbody>
                <tr>
                    <td>
                        <span class="summary-value">{{ number_format($statistikPengajuan['total_pengajuan']) }}</span>
                        <span class="summary-label">Total Pengajuan</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ number_format($statistikPengajuan['pengajuan_baru']) }}</span>
                        <span class="summary-label">Pengajuan Baru</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ number_format($statistikPengajuan['pengajuan_diproses']) }}</span>
                        <span class="summary-label">Sedang Diproses</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ number_format($statistikPengajuan['pengajuan_selesai']) }}</span>
                        <span class="summary-label">Pengajuan Selesai</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="summary-value">{{ number_format($statistikPengajuan['pengajuan_ditolak']) }}</span>
                        <span class="summary-label">Pengajuan Ditolak</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ number_format($informasiLayananAktif) }}</span>
                        <span class="summary-label">Informasi Layanan Aktif</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ number_format($statistikRating['rata_rata_rating'], 2) }} / 5</span>
                        <span class="summary-label">Rata-rata Rating</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ number_format($statistikRating['jumlah_ulasan']) }}</span>
                        <span class="summary-label">Total Ulasan</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

    <section>
        <h2 class="section-title">Grafik Statistik</h2>
        <div class="chart-section">
            <h3 class="chart-title">Statistik Pengajuan per Unit</h3>
            <img class="bar-chart-image" src="{{ $barChartImage }}" alt="Grafik statistik pengajuan per unit">
            <div class="bar-legend">
                @foreach ($statusGroups as $status)
                    <span>
                        <span class="legend-dot" style="background: {{ $status['color'] }};"></span>
                        {{ $status['label'] }}
                    </span>
                @endforeach
            </div>
        </div>
        <div class="chart-section">
            <h3 class="chart-title">Distribusi Status Pengajuan</h3>
            <table class="pie-layout">
                <tbody>
                    <tr>
                        <td class="text-center" style="width: 60%; padding-right: 10px;">
                            <img class="pie-chart-image" src="{{ $pieChartImage }}">
                        </td>

                        <td style="width: 40%; padding-left: 10px;">
                            @foreach ($pieChartSegments as $segment)
                                <div class="chart-legend" style="margin-left: 0;">
                                    <span class="legend-dot" style="background: {{ $segment['color'] }};"></span>
                                    <strong>{{ $segment['label'] }}</strong><br>
                                    {{ number_format($segment['count']) }} pengajuan
                                    ({{ number_format($segment['percentage'], 1) }}%)
                                </div>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="page-break">
        <h2 class="section-title">Rekapitulasi Pengajuan per Unit</h2>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 30%;">Nama Unit</th>
                    <th style="width: 13%;">Total<br>Pengajuan</th>
                    <th style="width: 13%;">Pengajuan<br>Baru</th>
                    <th style="width: 13%;">Sedang<br>Diproses</th>
                    <th style="width: 13%;">Pengajuan<br>Selesai</th>
                    <th style="width: 13%;">Pengajuan<br>Ditolak</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($unitApplicationStatistics as $index => $unit)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $unit['nama_unit'] }}</td>
                        <td class="text-center">{{ number_format($unit['total_pengajuan']) }}</td>
                        <td class="text-center">{{ number_format($unit['pengajuan_baru']) }}</td>
                        <td class="text-center">{{ number_format($unit['pengajuan_diproses']) }}</td>
                        <td class="text-center">{{ number_format($unit['pengajuan_selesai']) }}</td>
                        <td class="text-center">{{ number_format($unit['pengajuan_ditolak']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section>
        <h2 class="section-title">Rekapitulasi Rating per Unit</h2>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 7%;">No.</th>
                    <th style="width: 53%;">Nama Unit</th>
                    <th style="width: 20%;">Total Rating</th>
                    <th style="width: 20%;">Rata-rata Rating</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($unitRatingStatistics as $index => $unit)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $unit['nama_unit'] }}</td>
                        <td class="text-center">{{ number_format($unit['total_rating']) }}</td>
                        <td class="text-center">{{ number_format($unit['rata_rata_rating'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <p class="muted" style="margin-top: 12px;">
        Laporan ini dibuat secara otomatis dari data sistem Unit Layanan Terpadu Polinema.
    </p>
</body>
</html>
