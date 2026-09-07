<?php

declare(strict_types=1);

namespace Liberu\Accounting\PayrollIntegrationLivewire\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\View as ViewFacade;
use Liberu\Accounting\PayrollIntegration\Actions\ImportPayrollRun;
use Liberu\Accounting\PayrollIntegration\Actions\MarkPayrollImport;
use Liberu\Accounting\PayrollIntegration\Enums\ImportStatus;
use Liberu\Accounting\PayrollIntegration\Models\PayrollImport;
use Livewire\Component;

final class PayrollImports extends Component
{
    public int|string $selectedImportId = '';

    public string $status = '';

    public string $provider = '';

    public string $periodStart = '';

    public string $periodEnd = '';

    public string $runRef = '';

    public string $currency = 'GBP';

    public string $employeeRefs = '';

    public function selectImport(int $importId): void
    {
        $this->selectedImportId = $importId;
    }

    public function import(ImportPayrollRun $action): void
    {
        $data = $this->validate(['provider' => ['required', 'string', 'max:100'], 'periodStart' => ['required', 'date'], 'periodEnd' => ['required', 'date', 'after_or_equal:periodStart'], 'runRef' => ['required', 'string', 'max:150'], 'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/']]);
        $action->handle(['team_id' => (int) auth()->user()->current_team_id, 'provider' => $data['provider'], 'period_start' => $data['periodStart'], 'period_end' => $data['periodEnd'], 'run_ref' => $data['runRef'], 'currency' => $data['currency'], 'employee_refs' => array_values(array_filter(array_map('trim', explode(',', $this->employeeRefs))))]);
        $this->reset('provider', 'periodStart', 'periodEnd', 'runRef', 'employeeRefs');
        $this->dispatch('payroll-imported');
    }

    public function mark(MarkPayrollImport $action): void
    {
        $data = $this->validate(['selectedImportId' => ['required', 'integer'], 'status' => ['required', 'in:received,validated,imported,failed,reconciled']]);
        $action->handle($this->query()->findOrFail($data['selectedImportId']), ImportStatus::from($data['status']));
        $this->reset('selectedImportId', 'status');
        $this->dispatch('payroll-import-status-changed');
    }

    public function render(): View
    {
        return ViewFacade::make('accounting-payroll-integration-livewire::livewire.payroll-imports', ['imports' => $this->query()->latest()->paginate(15), 'statuses' => ImportStatus::cases()]);
    }

    private function query(): Builder
    {
        return PayrollImport::query()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }
}
