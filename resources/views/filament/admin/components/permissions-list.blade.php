<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 mt-2">
    @foreach($permissions as $permission)
        <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <x-heroicon-m-check-circle class="w-4 h-4 text-success-500" />
            <span class="text-sm">{{ $permission }}</span>
        </div>
    @endforeach
</div>
