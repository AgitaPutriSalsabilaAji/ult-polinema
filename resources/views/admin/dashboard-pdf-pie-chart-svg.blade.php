<svg xmlns="http://www.w3.org/2000/svg" width="250" height="250" viewBox="0 0 360 320">
    @foreach ($pieChartSegments as $segment)
        @if ($segment['path'] !== '')
            <path d="{{ $segment['path'] }}" fill="{{ $segment['color'] }}"
                stroke="#ffffff" stroke-width="2"/>
        @endif
    @endforeach
</svg>
