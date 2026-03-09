<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Filter Inventory Report
        </x-slot>

        <form wire:submit="generateReport" class="space-y-4">
            {{ $this->form }}

            <div class="flex justify-end">
                <x-filament::button type="submit">
                    Generate Report
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    @if($summaryStats)
        <!-- Summary Cards -->
        <x-filament::section>
            <x-slot name="heading">
                Summary Statistics
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-sm text-blue-600 dark:text-blue-400">Total Items</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $summaryStats['total_items'] }}</div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="text-sm text-green-600 dark:text-green-400">Total Inventory Value</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                        ETB {{ number_format($summaryStats['total_value'], 2) }}
                    </div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 dark:text-yellow-400">Low Stock Items</div>
                    <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $summaryStats['low_stock_items'] }}</div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <div class="text-sm text-red-600 dark:text-red-400">Out of Stock</div>
                    <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $summaryStats['out_of_stock'] }}</div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="text-sm text-purple-600 dark:text-purple-400">Total Transactions</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $summaryStats['total_transactions'] }}</div>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400">Transaction Value</div>
                    <div class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">
                        ETB {{ number_format($summaryStats['total_transaction_value'], 2) }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Categories Distribution -->
        <x-filament::section>
            <x-slot name="heading">
                Categories Distribution
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($summaryStats['categories'] as $category => $count)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <div class="text-lg font-medium">{{ ucfirst($category) }}</div>
                        <div class="text-2xl font-bold text-primary-600">{{ $count }} items</div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Transaction Summary -->
        <x-filament::section>
            <x-slot name="heading">
                Transaction Summary ({{ Carbon\Carbon::parse($filters['from_date'])->format('M d, Y') }} - {{ Carbon\Carbon::parse($filters['to_date'])->format('M d, Y') }})
            </x-slot>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left">Transaction Type</th>
                        <th class="px-4 py-2 text-right">Count</th>
                        <th class="px-4 py-2 text-right">Total Quantity</th>
                        <th class="px-4 py-2 text-right">Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($summaryStats['transactions'] as $transaction)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2 capitalize">{{ str_replace('_', ' ', $transaction->transaction_type) }}</td>
                            <td class="px-4 py-2 text-right">{{ $transaction->count }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($transaction->total_quantity) }}</td>
                            <td class="px-4 py-2 text-right font-medium text-green-600">
                                ETB {{ number_format($transaction->total_value ?? 0, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>

        <!-- Usage by Printer -->
        @if($summaryStats['usage_by_printer']->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">
                    Usage by Printer
                </x-slot>

                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Printer</th>
                            <th class="px-4 py-2 text-right">Quantity Used</th>
                            <th class="px-4 py-2 text-right">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaryStats['usage_by_printer'] as $usage)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2">{{ $usage->printer->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($usage->total_used) }}</td>
                                <td class="px-4 py-2 text-right font-medium text-orange-600">
                                    ETB {{ number_format($usage->total_cost ?? 0, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-filament::section>
        @endif

        <!-- Detailed Inventory List -->
        <x-filament::section>
            <x-slot name="heading">
                Detailed Inventory List
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Item Code</th>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Category</th>
                            <th class="px-4 py-2 text-right">Current Stock</th>
                            <th class="px-4 py-2 text-right">Min Stock</th>
                            <th class="px-4 py-2 text-right">Unit Cost</th>
                            <th class="px-4 py-2 text-right">Total Value</th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $item)
                            @php
                                $totalValue = $item->current_stock * $item->unit_cost;
                                $status = $item->current_stock <= 0 ? 'out_of_stock' :
                                        ($item->current_stock <= $item->minimum_stock ? 'low_stock' : 'in_stock');
                                $statusColor = [
                                    'in_stock' => 'text-green-600 bg-green-100 dark:bg-green-900/20',
                                    'low_stock' => 'text-yellow-600 bg-yellow-100 dark:bg-yellow-900/20',
                                    'out_of_stock' => 'text-red-600 bg-red-100 dark:bg-red-900/20',
                                ][$status];
                                $statusLabel = [
                                    'in_stock' => 'In Stock',
                                    'low_stock' => 'Low Stock',
                                    'out_of_stock' => 'Out of Stock',
                                ][$status];
                            @endphp
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-2 font-mono">{{ $item->item_code }}</td>
                                <td class="px-4 py-2">{{ $item->name }}</td>
                                <td class="px-4 py-2 capitalize">{{ $item->category }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($item->current_stock) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($item->minimum_stock) }}</td>
                                <td class="px-4 py-2 text-right">ETB {{ number_format($item->unit_cost, 2) }}</td>
                                <td class="px-4 py-2 text-right font-medium text-blue-600">
                                    ETB {{ number_format($totalValue, 2) }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded text-xs {{ $statusColor }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
