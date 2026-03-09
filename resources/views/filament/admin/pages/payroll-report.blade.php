<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Filter Payroll Report
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
        <x-filament::section>
            <x-slot name="heading">
                Summary Statistics
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Employees</div>
                    <div class="text-2xl font-bold">{{ $summaryStats['total_employees'] }}</div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-sm text-blue-600 dark:text-blue-400">Total Gross Pay</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">ETB {{ number_format($summaryStats['total_gross'], 2) }}</div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <div class="text-sm text-red-600 dark:text-red-400">Total Deductions</div>
                    <div class="text-2xl font-bold text-red-700 dark:text-red-300">ETB {{ number_format($summaryStats['total_deductions'], 2) }}</div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="text-sm text-green-600 dark:text-green-400">Total Net Pay</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">ETB {{ number_format($summaryStats['total_net'], 2) }}</div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 dark:text-yellow-400">Income Tax</div>
                    <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">ETB {{ number_format($summaryStats['total_tax'], 2) }}</div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="text-sm text-purple-600 dark:text-purple-400">Pension (Employee)</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">ETB {{ number_format($summaryStats['total_pension_employee'], 2) }}</div>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400">Pension (Employer)</div>
                    <div class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">ETB {{ number_format($summaryStats['total_pension_employer'], 2) }}</div>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                    <div class="text-sm text-orange-600 dark:text-orange-400">Average Net Pay</div>
                    <div class="text-2xl font-bold text-orange-700 dark:text-orange-300">ETB {{ number_format($summaryStats['average_net'], 2) }}</div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Detailed Payroll Report
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Employee</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Basic Salary</th>
                            <th class="px-6 py-3">Allowances</th>
                            <th class="px-6 py-3">Overtime</th>
                            <th class="px-6 py-3">Gross Pay</th>
                            <th class="px-6 py-3">Tax</th>
                            <th class="px-6 py-3">Pension</th>
                            <th class="px-6 py-3">Deductions</th>
                            <th class="px-6 py-3">Net Pay</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $record)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $record->employee?->full_name ?? 'N/A' }}
                                    <div class="text-xs text-gray-500">{{ $record->employee?->employee_id ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4">{{ $record->employee?->department ?? 'N/A' }}</td>
                                <td class="px-6 py-4">ETB {{ number_format($record->basic_salary, 2) }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $allowances = ($record->housing_allowance ?? 0) +
                                                      ($record->transport_allowance ?? 0) +
                                                      ($record->position_allowance ?? 0) +
                                                      ($record->other_allowances ?? 0);
                                    @endphp
                                    ETB {{ number_format($allowances, 2) }}
                                </td>
                                <td class="px-6 py-4">ETB {{ number_format($record->overtime_pay ?? 0, 2) }}</td>
                                <td class="px-6 py-4 font-medium text-blue-600">ETB {{ number_format($record->gross_pay, 2) }}</td>
                                <td class="px-6 py-4 text-yellow-600">ETB {{ number_format($record->income_tax ?? 0, 2) }}</td>
                                <td class="px-6 py-4 text-purple-600">ETB {{ number_format($record->pension_employee ?? 0, 2) }}</td>
                                <td class="px-6 py-4 text-red-600">ETB {{ number_format($record->total_deductions, 2) }}</td>
                                <td class="px-6 py-4 font-bold text-green-600">ETB {{ number_format($record->net_pay, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                    No payroll records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
