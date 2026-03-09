<?php

namespace App\Helpers;

class EthiopianTaxHelper
{
    public static function calculateIncomeTax($monthlySalary)
    {
        // Ethiopian tax brackets (as of 2023)
        $brackets = [
            ['min' => 0, 'max' => 600, 'rate' => 0, 'deduction' => 0],
            ['min' => 601, 'max' => 1650, 'rate' => 0.10, 'deduction' => 60],
            ['min' => 1651, 'max' => 3200, 'rate' => 0.15, 'deduction' => 142.5],
            ['min' => 3201, 'max' => 5250, 'rate' => 0.20, 'deduction' => 302.5],
            ['min' => 5251, 'max' => 7800, 'rate' => 0.25, 'deduction' => 565],
            ['min' => 7801, 'max' => 10900, 'rate' => 0.30, 'deduction' => 955],
            ['min' => 10901, 'max' => null, 'rate' => 0.35, 'deduction' => 1500],
        ];

        foreach ($brackets as $bracket) {
            if ($monthlySalary <= $bracket['max'] || $bracket['max'] === null) {
                return ($monthlySalary * $bracket['rate']) - $bracket['deduction'];
            }
        }

        return 0;
    }

    public static function calculatePension($salary, $isEmployer = false)
    {
        $rate = $isEmployer ? 0.11 : 0.07; // 11% employer, 7% employee
        return $salary * $rate;
    }
}
