<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Attendance Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Attendance Today</h3>
                <dl class="mt-2 grid grid-cols-2 gap-2">
                    <div>
                        <dt class="text-sm text-gray-500">Present</dt>
                        <dd class="text-2xl font-semibold text-green-600">{{ $this->getAttendanceSummary()['present_today'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Absent</dt>
                        <dd class="text-2xl font-semibold text-red-600">{{ $this->getAttendanceSummary()['absent_today'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">On Leave</dt>
                        <dd class="text-xl font-semibold text-yellow-600">{{ $this->getAttendanceSummary()['on_leave'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">Total</dt>
                        <dd class="text-xl font-semibold text-blue-600">{{ $this->getAttendanceSummary()['total_employees'] }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Payroll Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Monthly Payroll</h3>
                <dl class="mt-2 space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Gross Pay</dt>
                        <dd class="text-sm font-semibold">ETB {{ number_format($this->getPayrollSummary()['total_gross'], 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Net Pay</dt>
                        <dd class="text-sm font-semibold text-green-600">ETB {{ number_format($this->getPayrollSummary()['total_net'], 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Tax</dt>
                        <dd class="text-sm font-semibold text-red-600">ETB {{ number_format($this->getPayrollSummary()['total_tax'], 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Employees</dt>
                        <dd class="text-sm font-semibold">{{ $this->getPayrollSummary()['employee_count'] }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Orders Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Monthly Orders</h3>
                <dl class="mt-2 space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Total Orders</dt>
                        <dd class="text-sm font-semibold">{{ $this->getOrderSummary()['total_orders'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Completed</dt>
                        <dd class="text-sm font-semibold text-green-600">{{ $this->getOrderSummary()['completed_orders'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Pending</dt>
                        <dd class="text-sm font-semibold text-yellow-600">{{ $this->getOrderSummary()['pending_orders'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Revenue</dt>
                        <dd class="text-sm font-semibold text-blue-600">ETB {{ number_format($this->getOrderSummary()['total_revenue'], 2) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Inventory Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Inventory Status</h3>
                <dl class="mt-2 space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Low Stock Items</dt>
                        <dd class="text-sm font-semibold text-red-600">{{ $this->getInventorySummary()['low_stock_items'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Total Items</dt>
                        <dd class="text-sm font-semibold">{{ $this->getInventorySummary()['total_items'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Today's Transactions</dt>
                        <dd class="text-sm font-semibold">{{ $this->getInventorySummary()['recent_transactions'] }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Report Links Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
            <!-- Attendance Reports -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Attendance Reports</h3>
                <div class="space-y-2">
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Daily Attendance Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Monthly Attendance Summary</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Leave Balance Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Overtime Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Absenteeism Report</a>
                </div>
            </div>

            <!-- Payroll Reports -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Payroll Reports</h3>
                <div class="space-y-2">
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Monthly Payroll Summary</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Payroll Register</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Tax Deduction Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Pension Contribution Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Employee Payslips</a>
                </div>
            </div>

            <!-- Financial Reports -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Financial Reports</h3>
                <div class="space-y-2">
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Revenue Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Income Statement</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Balance Sheet</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Cash Flow Statement</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Journal Entries Report</a>
                </div>
            </div>

            <!-- Inventory Reports -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Inventory Reports</h3>
                <div class="space-y-2">
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Stock Status Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Low Stock Alert Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Inventory Transactions</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Usage Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Reorder Recommendations</a>
                </div>
            </div>

            <!-- Customer Reports -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Customer Reports</h3>
                <div class="space-y-2">
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Customer Orders Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Outstanding Balance Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Order History Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">VIP Customer Report</a>
                </div>
            </div>

            <!-- Printer Reports -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Printer Reports</h3>
                <div class="space-y-2">
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Printer Usage Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Toner Consumption Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Paper Usage Report</a>
                    <a href="#" class="block text-sm text-primary-600 hover:underline">Maintenance Report</a>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
