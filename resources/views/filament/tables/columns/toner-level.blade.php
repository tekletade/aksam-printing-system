@php
    $tonerLevels = $getRecord()->tonerLevels;
    $hasColor = $getRecord()->printerModel?->is_color ?? false;
@endphp

<div class="flex flex-col gap-1">
    @foreach($tonerLevels as $toner)
        @php
            $level = $toner->current_level;
            $color = $toner->toner_color;
            $bgColor = match($color) {
                'black' => 'bg-gray-600',
                'cyan' => 'bg-cyan-500',
                'magenta' => 'bg-pink-500',
                'yellow' => 'bg-yellow-500',
                default => 'bg-gray-400'
            };
            $width = min(100, max(0, $level));
            $textColor = $level < 15 ? 'text-danger-600' : ($level < 30 ? 'text-warning-600' : 'text-success-600');
        @endphp
        <div class="flex items-center gap-1 text-xs">
            <span class="w-12 capitalize">{{ $color }}</span>
            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="{{ $bgColor }} h-full rounded-full" style="width: {{ $width }}%"></div>
            </div>
            <span class="{{ $textColor }} w-8 text-right">{{ $level }}%</span>
        </div>
    @endforeach
</div>
