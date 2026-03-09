<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\Attendance;
use App\Models\EmployeeSalary;
use App\Helpers\EthiopianTaxHelper;
use Carbon\Carbon;

class PayrollCalculationService
{
    public function calculatePayrollForPeriod(PayrollPeriod $period)
    {
        $employees = Employee::where('status', 'active')->get();

        foreach ($employees as $employee) {
            $this->calculateEmployeePayroll($employee, $period);
        }

        $period->update(['status' => 'calculated']);
    }

    public function calculateEmployeePayroll(Employee $employee, PayrollPeriod $period)
    {
        // Get current salary
        $salary = $employee->currentSalary;

        if (!$salary) {
            return null;
        }

        // Get attendance for the period
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$period->start_date, $period->end_date])
            ->get();

        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $leaveDays = $attendances->whereIn('status', ['on_leave', 'half_day'])->count();
        $overtimeHours = $attendances->sum('overtime_hours');

        // Calculate basic pay based on attendance
        $dailyRate = $salary->basic_salary / 30; // Assuming 30 days
        $basicPay = $dailyRate * $presentDays;

        // Calculate overtime pay
        $hourlyRate = $dailyRate / 8;
        $overtimeRate = $hourlyRate * 1.5; // 1.5x for overtime
        $overtimePay = $overtimeHours * $overtimeRate;

        // Calculate allowances (pro-rated)
        $housingAllowance = $salary->housing_allowance;
        $transportAllowance = $salary->transport_allowance;
        $positionAllowance = $salary->position_allowance;

        // Calculate gross pay
        $grossPay = $basicPay + $housingAllowance + $transportAllowance +
                    $positionAllowance + $salary->other_allowances + $overtimePay;

        // Calculate deductions
        $incomeTax = EthiopianTaxHelper::calculateIncomeTax($grossPay);
        $pensionEmployee = EthiopianTaxHelper::calculatePension($grossPay, false);
        $pensionEmployer = EthiopianTaxHelper::calculatePension($grossPay, true);

        $totalDeductions = $incomeTax + $pensionEmployee;

        // Calculate net pay
        $netPay = $grossPay - $totalDeductions;

        // Create or update payroll run
        return PayrollRun::updateOrCreate(
            [
                'payroll_period_id' => $period->id,
                'employee_id' => $employee->id,
            ],
            [
                'basic_salary' => $basicPay,
                'housing_allowance' => $housingAllowance,
                'transport_allowance' => $transportAllowance,
                'position_allowance' => $positionAllowance,
                'other_allowances' => $salary->other_allowances,
                'overtime_pay' => $overtimePay,
                'gross_pay' => $grossPay,
                'income_tax' => $incomeTax,
                'pension_employee' => $pensionEmployee,
                'pension_employer' => $pensionEmployer,
                'total_deductions' => $totalDeductions,
                'net_pay' => $netPay,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'leave_days' => $leaveDays,
                'status' => 'calculated',
            ]
        );
    }
}
