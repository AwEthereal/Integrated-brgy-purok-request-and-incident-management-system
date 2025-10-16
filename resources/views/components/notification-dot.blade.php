@props(['count' => 0, 'color' => 'yellow', 'pulse' => true])

@if($count > 0)
    <span class="relative inline-flex items-center">
        @if($pulse)
            <span class="absolute inline-flex h-full w-full rounded-full bg-{{ $color }}-400 opacity-75 animate-ping"></span>
        @endif
        <span class="relative inline-flex items-center justify-center h-2 w-2 rounded-full bg-{{ $color }}-500">
            <span class="sr-only">{{ $count }} new notifications</span>
        </span>
    </span>
@endif
