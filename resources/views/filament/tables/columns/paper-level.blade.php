@php
    $paperTrays = $getRecord()->paperInventory;
@endphp

<div class="flex flex-col gap-1">
    @foreach($paperTrays as $paper)
        @php
            $level = $paper->current_sheets;
            $max = $paper->max_capacity;
            $percentage = ($level / $max) * 100;
            $width = min(100, max(0, $percentage));
            $bgColor = $percentage < 15 ? 'bg-danger-500' : ($percentage < 30 ? 'bg-warning-500' : 'bg-success-500');
        @endphp
        <div class="flex items-center gap-1 text-xs">
            <span class="w-12">{{ $paper->tray_name }}</span>
            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="{{ $bgColor }} h-full rounded-full" style="width: {{ $width }}%"></div>
            </div>
            <span class="w-12 text-right">{{ $level }}/{{ $max }}</span>
        </div>
    @endforeach
</div>
