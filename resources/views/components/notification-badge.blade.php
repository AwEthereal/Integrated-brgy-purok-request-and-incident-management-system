@props(['count' => 0, 'color' => 'yellow', 'showNumber' => false, 'pulse' => true])

@if($count > 0)
    @if($showNumber)
        {{-- Badge with number --}}
        <span class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-{{ $color }}-500 rounded-full {{ $pulse ? 'animate-pulse' : '' }}">
            {{ $count }}
        </span>
    @else
        {{-- Just a dot --}}
        <span class="ml-2 relative inline-flex items-center">
            @if($pulse)
                <span class="absolute inline-flex h-full w-full rounded-full bg-{{ $color }}-400 opacity-75 animate-ping"></span>
            @endif
            <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-{{ $color }}-500"></span>
        </span>
    @endif
@endif
