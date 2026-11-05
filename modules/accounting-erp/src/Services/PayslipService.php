<?php

declare(strict_types=1);

namespace Liberu\Accounting\Erp\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Liberu\Accounting\Erp\Models\Payroll;

/**
 * Renders an employee payslip for a pay-run row, as HTML or a PDF document.
 */
class PayslipService
{
    /**
     * @return array<string, mixed>
     */
    private function data(Payroll $payroll): array
    {
        $payroll->loadMissing('employee');

        return [
            'payroll' => $payroll,
            'employee' => $payroll->employee,
            'gross' => $payroll->grossSalary(),
            'totalDeductions' => $payroll->totalDeductions(),
        ];
    }

    public function html(Payroll $payroll): string
    {
        return view('accounting-erp::payslips.template', $this->data($payroll))->render();
    }

    public function pdf(Payroll $payroll): string
    {
        return Pdf::loadView('accounting-erp::payslips.template', $this->data($payroll))->output();
    }
}
