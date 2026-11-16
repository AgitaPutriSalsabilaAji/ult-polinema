@php
    $plotLeft = 48;
    $plotTop = 18;
    $plotWidth = 740;
    $plotHeight = 190;
    $barMaxValue = max(1, (int) $unitApplicationStatistics->max(
        fn (array $unit): int => $unit['total_pengajuan']
    ));
    $barUnitCount = max(1, $unitApplicationStatistics->count());
    $barSlotWidth = $plotWidth / $barUnitCount;
    $barGroupWidth = min(58, $barSlotWidth * 0.75);
    $barWidth = $barGroupWidth / 4;
    $barStatuses = [
        ['key' => 'pengajuan_baru', 'color' => '#f59e0b'],
        ['key' => 'pengajuan_diproses', 'color' => '#7c3aed'],
        ['key' => 'pengajuan_selesai', 'color' => '#059669'],
        ['key' => 'pengajuan_ditolak', 'color' => '#ef4444'],
    ];
@endphp
<svg xmlns="http://www.w3.org/2000/svg" width="820" height="270" viewBox="0 0 820 270">
    @for ($line = 0; $line <= 4; $line++)
        @php
            $gridY = $plotTop + ($plotHeight / 4) * $line;
            $gridValue = round($barMaxValue * (4 - $line) / 4);
        @endphp
        <line x1="{{ $plotLeft }}" y1="{{ $gridY }}" x2="{{ $plotLeft + $plotWidth }}"
            y2="{{ $gridY }}" stroke="#e2e8f0" stroke-width="1"/>
        <text x="{{ $plotLeft - 7 }}" y="{{ $gridY + 3 }}" text-anchor="end"
            fill="#64748b" font-size="9">{{ $gridValue }}</text>
    @endfor

    @foreach ($unitApplicationStatistics as $unitIndex => $unit)
        @php
            $barStartX = $plotLeft + ($unitIndex * $barSlotWidth)
                + (($barSlotWidth - $barGroupWidth) / 2);
        @endphp
        @foreach ($barStatuses as $statusIndex => $status)
            @php
                $barValue = $unit[$status['key']];
                $barHeight = ($barValue / $barMaxValue) * $plotHeight;
                $barX = $barStartX + ($statusIndex * $barWidth);
                $barY = $plotTop + $plotHeight - $barHeight;
            @endphp
            <rect x="{{ $barX }}" y="{{ $barY }}" width="{{ max(1, $barWidth - 1) }}"
                height="{{ max(0, $barHeight) }}" fill="{{ $status['color'] }}"/>
        @endforeach
        @php
            $unitLabelX = $plotLeft + ($unitIndex * $barSlotWidth) + ($barSlotWidth / 2);
            $unitLabelY = $plotTop + $plotHeight + 20;
        @endphp
        <text x="{{ $unitLabelX }}" y="{{ $unitLabelY }}" text-anchor="end"
            transform="rotate(-30 {{ $unitLabelX }} {{ $unitLabelY }})"
            fill="#475569" font-size="9">{{ $unit['nama_unit'] }}</text>
    @endforeach
</svg>
