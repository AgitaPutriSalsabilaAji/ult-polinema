@php
    $status = strtolower((string) $status);
    $statusMap = [
        'diajukan' => ['BARU', 'baru'],
        'baru' => ['BARU', 'baru'],
        'didisposisikan' => ['DIDISPOSISIKAN', 'didisposisikan'],
        'diproses' => ['DIPROSES', 'diproses'],
        'selesai' => ['SELESAI', 'selesai'],
        'ditolak' => ['DITOLAK', 'ditolak'],
        'dikirim' => ['DIDISPOSISIKAN', 'didisposisikan'],
    ];
    [$statusLabel, $statusClass] = $statusMap[$status] ?? [strtoupper($status), 'default'];
@endphp
<span class="ticket-status-badge ticket-status-badge--{{ $statusClass }}">
    {{ $statusLabel }}
</span>
