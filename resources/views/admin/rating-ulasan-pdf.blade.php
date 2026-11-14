<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rating &amp; Ulasan Layanan ULT Polinema</title>
    <style>
        @page {
            margin: 28px 30px 35px;
        }

        body {
            color: #202633;
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9px;
        }

        h1, h2, p {
            margin: 0;
        }

        .report-header {
            border-bottom: 2px solid #25205f;
            margin-bottom: 16px;
            padding-bottom: 12px;
            text-align: center;
        }

        .report-logo {
            height: 62px;
            margin-bottom: 6px;
            width: 62px;
        }

        .report-title {
            color: #25205f;
            font-size: 15px;
            font-weight: bold;
            line-height: 1.5;
        }

        .report-meta {
            color: #505968;
            line-height: 1.6;
            margin-top: 5px;
        }

        .section-title {
            color: #25205f;
            font-size: 11px;
            margin: 15px 0 7px;
        }

        .summary-table, .report-table {
            border-collapse: collapse;
            width: 100%;
        }

        .summary-table td {
            background: #f3f4f8;
            border: 1px solid #d6d9e2;
            padding: 8px;
            text-align: center;
            width: 25%;
        }

        .summary-value {
            color: #25205f;
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .summary-label {
            color: #505968;
            font-size: 8px;
        }

        .report-table {
            table-layout: fixed;
        }

        .report-table th, .report-table td {
            border: 1px solid #cbd0da;
            padding: 5px;
            vertical-align: top;
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

        .rating-summary td {
            text-align: center;
        }

        .rating-summary td.unit-name {
            text-align: left;
        }

        .rating-detail td {
            line-height: 1.45;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .rating-detail td.comment {
            white-space: normal;
        }

        .muted {
            color: #677080;
        }

        .text-center {
            text-align: center;
        }

        thead {
            display: table-header-group;
        }

        .rating-summary tbody tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <header class="report-header">
        @if ($logoDataUri)
            <div><img class="report-logo" src="{{ $logoDataUri }}" alt="Logo Politeknik Negeri Malang"></div>
        @endif
        <h1 class="report-title">LAPORAN RATING &amp; ULASAN<br>LAYANAN ULT POLINEMA</h1>
        <p class="report-meta">
            Periode: {{ $period }}<br>
            Tanggal pembuatan: {{ $createdAt }}
        </p>
    </header>

    <section>
        <h2 class="section-title">Ringkasan Umum</h2>
        <table class="summary-table">
            <tbody>
                <tr>
                    <td>
                        <span class="summary-value">{{ number_format($summary->total_rating) }}</span>
                        <span class="summary-label">Total Rating</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ number_format((float) ($summary->average_rating ?? 0), 2) }} / 5</span>
                        <span class="summary-label">Rata-rata Rating</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ number_format($unitSummaries->count()) }}</span>
                        <span class="summary-label">Unit dengan Rating</span>
                    </td>
                    <td>
                        <span class="summary-value">{{ number_format($summary->total_comments) }}</span>
                        <span class="summary-label">Ulasan dengan Komentar</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

    <section>
        <h2 class="section-title">Rangkuman Rating per Unit</h2>
        <table class="report-table rating-summary">
            <thead>
                <tr>
                    <th style="width: 4%;">No.</th>
                    <th style="width: 28%;">Nama Unit</th>
                    <th style="width: 10%;">Total Rating</th>
                    <th style="width: 10%;">Rata-rata</th>
                    <th style="width: 9%;">Bintang 5</th>
                    <th style="width: 9%;">Bintang 4</th>
                    <th style="width: 9%;">Bintang 3</th>
                    <th style="width: 9%;">Bintang 2</th>
                    <th style="width: 9%;">Bintang 1</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($unitSummaries as $index => $unit)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="unit-name">{{ $unit->nama_unit }}</td>
                        <td>{{ number_format($unit->total_rating) }}</td>
                        <td>{{ number_format((float) ($unit->average_rating ?? 0), 2) }}</td>
                        <td>{{ number_format($unit->rating_5) }}</td>
                        <td>{{ number_format($unit->rating_4) }}</td>
                        <td>{{ number_format($unit->rating_3) }}</td>
                        <td>{{ number_format($unit->rating_2) }}</td>
                        <td>{{ number_format($unit->rating_1) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section>
        <h2 class="section-title">Detail Ulasan</h2>
        <table class="report-table rating-detail">
            <thead>
                <tr>
                    <th style="width: 21%;">Nama Unit</th>
                    <th style="width: 21%;">Nama Layanan</th>
                    <th style="width: 8%;">Rating</th>
                    <th style="width: 38%;">Komentar</th>
                    <th style="width: 12%;">Tanggal Rating</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ratings as $rating)
                    <tr>
                        <td>{{ $rating->layanan?->unit?->nama_unit ?? '-' }}</td>
                        <td>{{ $rating->layanan?->nama_layanan ?? '-' }}</td>
                        <td class="text-center">{{ $rating->rating ?? 0 }} / 5</td>
                        <td class="comment">{{ $rating->komentar ?: 'Tidak ada komentar.' }}</td>
                        <td>
                            {{ $rating->dibuat_pada?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <p class="muted" style="margin-top: 12px;">
        Laporan ini dibuat secara otomatis dari data Rating &amp; Ulasan Layanan ULT Polinema.
    </p>
</body>
</html>
