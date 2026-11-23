<svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320">
    @foreach ($pieChartSegments as $segment)
        @if ($segment['path'] !== '')
            <path d="{{ $segment['path'] }}" fill="{{ $segment['color'] }}"
                stroke="#ffffff" stroke-width="2"/>
        @endif
    @endforeach
</svg>
