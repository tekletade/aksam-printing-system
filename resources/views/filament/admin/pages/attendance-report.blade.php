<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Filter Attendance Report
        </x-slot>

        <form wire:submit="generateReport" class="space-y-4">
            {{ $this->form }}

            <div class="flex justify-end space-x-2">
                <x-filament::button type="submit">
                    Generate Report
                </x-filament::button>

                @if($reportData)
                    <x-filament::button color="gray" wire:click="exportPdf">
                        Export PDF
                    </x-filament::button>

                    <x-filament::button color="success" wire:click="exportExcel">
                        Export Excel
                    </x-filament::button>
                @endif
            </div>
        </form>
    </x-filament::section>

    @if($summaryStats)
        <x-filament::section>
            <x-slot name="heading">
                Summary Statistics
            </x-slot>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Records</div>
                    <div class="text-2xl font-bold">{{ $summaryStats['total_records'] }}</div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="text-sm text-blue-600 dark:text-blue-400">Unique Employees</div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $summaryStats['unique_employees'] }}</div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="text-sm text-green-600 dark:text-green-400">Present</div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $summaryStats['present'] }}</div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <div class="text-sm text-red-600 dark:text-red-400">Absent</div>
                    <div class="text-2xl font-bold text-red-700 dark:text-red-300">{{ $summaryStats['absent'] }}</div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 dark:text-yellow-400">Late</div>
                    <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300">{{ $summaryStats['late'] }}</div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                    <div class="text-sm text-purple-600 dark:text-purple-400">On Leave</div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $summaryStats['on_leave'] }}</div>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-4">
                    <div class="text-sm text-indigo-600 dark:text-indigo-400">Half Day</div>
                    <div class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">{{ $summaryStats['half_day'] }}</div>
                </div>

                <div class="bg-teal-50 dark:bg-teal-900/20 rounded-lg p-4">
                    <div class="text-sm text-teal-600 dark:text-teal-400">Total Hours</div>
                    <div class="text-2xl font-bold text-teal-700 dark:text-teal-300">{{ number_format($summaryStats['total_hours'], 1) }}</div>
                </div>

                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4">
                    <div class="text-sm text-orange-600 dark:text-orange-400">Overtime Hours</div>
                    <div class="text-2xl font-bold text-orange-700 dark:text-orange-300">{{ number_format($summaryStats['overtime_hours'], 1) }}</div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Detailed Report
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Employee ID</th>
                            <th class="px-6 py-3">Employee Name</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Check In</th>
                            <th class="px-6 py-3">Check Out</th>
                            <th class="px-6 py-3">Hours</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Late</th>
                            <th class="px-6 py-3">Overtime</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $record)
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-6 py-4">{{ $record->employee?->employee_id ?? 'N/A' }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $record->employee?->full_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">{{ $record->employee?->department ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $record->attendance_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4">{{ $record->check_in?->format('H:i') ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $record->check_out?->format('H:i') ?? '-' }}</td>
                                <td class="px-6 py-4">{{ number_format($record->total_hours, 1) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs
                                        @if($record->status == 'present') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                        @elseif($record->status == 'absent') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                        @elseif($record->status == 'late') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                        @elseif(in_array($record->status, ['on_leave', 'leave'])) bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                        @elseif($record->status == 'half_day') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($record->is_late)
                                        <span class="text-red-600 dark:text-red-400">{{ $record->late_minutes }} min</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($record->is_overtime)
                                        <span class="text-orange-600 dark:text-orange-400">{{ number_format($record->overtime_hours, 1) }} hrs</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                    No attendance records found for the selected period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
