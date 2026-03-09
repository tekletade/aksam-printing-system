<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Filter Financial Report
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

    @if($reportData && $filters['report_type'] == 'income_statement')
        <x-filament::section>
            <x-slot name="heading">
                Income Statement (Profit & Loss)
            </x-slot>

            <div class="space-y-6">
                <!-- Revenue Section -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300 mb-2">Revenue</h3>
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        ETB {{ number_format($reportData['revenue'], 2) }}
                    </div>
                </div>

                <!-- Cost of Goods Sold -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-300 mb-2">Cost of Goods Sold</h3>
                    <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                        ETB {{ number_format($reportData['cogs'], 2) }}
                    </div>
                </div>

                <!-- Gross Profit -->
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-green-800 dark:text-green-300 mb-2">Gross Profit</h3>
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400">
                        ETB {{ number_format($reportData['gross_profit'], 2) }}
                    </div>
                </div>

                <!-- Operating Expenses -->
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-red-800 dark:text-red-300 mb-2">Operating Expenses</h3>
                    <div class="text-3xl font-bold text-red-600 dark:text-red-400">
                        ETB {{ number_format($reportData['operating_expenses'], 2) }}
                    </div>
                </div>

                <!-- Net Income -->
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-purple-800 dark:text-purple-300 mb-2">Net Income</h3>
                    <div class="text-4xl font-bold text-purple-600 dark:text-purple-400">
                        ETB {{ number_format($reportData['net_income'], 2) }}
                    </div>
                    <div class="text-sm text-purple-600 dark:text-purple-400 mt-1">
                        Profit Margin: {{ number_format($reportData['profit_margin'], 1) }}%
                    </div>
                </div>

                @if($summaryStats)
                    <div class="mt-6">
                        <h3 class="text-lg font-medium mb-4">Revenue by Source</h3>
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-800">
                                    <th class="px-4 py-2 text-left">Source</th>
                                    <th class="px-4 py-2 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summaryStats['revenue_by_type'] as $item)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2">{{ ucfirst($item->source_channel) }}</td>
                                    <td class="px-4 py-2 text-right">ETB {{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </x-filament::section>
    @endif

    @if($reportData && $filters['report_type'] == 'balance_sheet')
        <x-filament::section>
            <x-slot name="heading">
                Balance Sheet
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Assets -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <h3 class="text-xl font-bold text-blue-800 dark:text-blue-300 mb-4">Assets</h3>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span>Cash</span>
                            <span class="font-medium">ETB {{ number_format($reportData['assets']['cash'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Accounts Receivable</span>
                            <span class="font-medium">ETB {{ number_format($reportData['assets']['accounts_receivable'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Inventory</span>
                            <span class="font-medium">ETB {{ number_format($reportData['assets']['inventory'], 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Fixed Assets</span>
                            <span class="font-medium">ETB {{ number_format($reportData['assets']['fixed_assets'], 2) }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-blue-200 dark:border-blue-800">
                            <span class="font-bold">Total Assets</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">ETB {{ number_format($reportData['assets']['total'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Liabilities & Equity -->
                <div class="space-y-4">
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                        <h3 class="text-xl font-bold text-yellow-800 dark:text-yellow-300 mb-4">Liabilities</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span>Accounts Payable</span>
                                <span class="font-medium">ETB {{ number_format($reportData['liabilities']['accounts_payable'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tax Payable</span>
                                <span class="font-medium">ETB {{ number_format($reportData['liabilities']['tax_payable'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Accrued Expenses</span>
                                <span class="font-medium">ETB {{ number_format($reportData['liabilities']['accrued_expenses'], 2) }}</span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-yellow-200 dark:border-yellow-800">
                                <span class="font-bold">Total Liabilities</span>
                                <span class="font-bold text-yellow-600 dark:text-yellow-400">ETB {{ number_format($reportData['liabilities']['total'], 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <h3 class="text-xl font-bold text-green-800 dark:text-green-300 mb-4">Equity</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span>Share Capital</span>
                                <span class="font-medium">ETB {{ number_format($reportData['equity']['share_capital'], 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Retained Earnings</span>
                                <span class="font-medium">ETB {{ number_format($reportData['equity']['retained_earnings'], 2) }}</span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-green-200 dark:border-green-800">
                                <span class="font-bold">Total Equity</span>
                                <span class="font-bold text-green-600 dark:text-green-400">ETB {{ number_format($reportData['equity']['total'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
                <div class="flex justify-between text-lg">
                    <span class="font-bold">Total Liabilities & Equity</span>
                    <span class="font-bold text-primary-600">
                        ETB {{ number_format($reportData['liabilities']['total'] + $reportData['equity']['total'], 2) }}
                    </span>
                </div>
            </div>
        </x-filament::section>
    @endif

    @if($reportData && $filters['report_type'] == 'revenue_summary')
        <x-filament::section>
            <x-slot name="heading">
                Revenue Summary
            </x-slot>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-sm text-blue-600 dark:text-blue-400">Total Revenue</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">
                        ETB {{ number_format($summaryStats['total_revenue'], 2) }}
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="text-sm text-green-600 dark:text-green-400">Total Orders</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">
                        {{ $summaryStats['total_orders'] }}
                    </div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="text-sm text-purple-600 dark:text-purple-400">Average Order</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">
                        ETB {{ number_format($summaryStats['average_order'], 2) }}
                    </div>
                </div>

                @if($summaryStats['top_customer'])
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 dark:text-yellow-400">Top Customer</div>
                    <div class="text-lg font-bold text-yellow-700 dark:text-yellow-300 truncate">
                        {{ $summaryStats['top_customer']->customer->name ?? 'N/A' }}
                    </div>
                    <div class="text-sm text-yellow-600 dark:text-yellow-400">
                        ETB {{ number_format($summaryStats['top_customer']->total_revenue ?? 0, 2) }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Detailed Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-2 text-left">Customer</th>
                            <th class="px-4 py-2 text-right">Orders</th>
                            <th class="px-4 py-2 text-right">Total Revenue</th>
                            <th class="px-4 py-2 text-right">Average Order</th>
                            <th class="px-4 py-2 text-right">% of Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $item)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2">{{ $item->customer->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-right">{{ $item->order_count }}</td>
                            <td class="px-4 py-2 text-right font-medium text-green-600">
                                ETB {{ number_format($item->total_revenue, 2) }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                ETB {{ number_format($item->average_order_value, 2) }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                {{ number_format(($item->total_revenue / $summaryStats['total_revenue']) * 100, 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-800 font-bold">
                        <tr>
                            <td class="px-4 py-2">Total</td>
                            <td class="px-4 py-2 text-right">{{ $summaryStats['total_orders'] }}</td>
                            <td class="px-4 py-2 text-right text-green-600">ETB {{ number_format($summaryStats['total_revenue'], 2) }}</td>
                            <td class="px-4 py-2 text-right">ETB {{ number_format($summaryStats['average_order'], 2) }}</td>
                            <td class="px-4 py-2 text-right">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
