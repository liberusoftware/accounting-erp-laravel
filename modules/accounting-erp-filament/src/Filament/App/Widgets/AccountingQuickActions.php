<?php

declare(strict_types=1);

namespace Liberu\Accounting\ErpFilament\Filament\App\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Liberu\Accounting\Erp\Models\Team;
use Liberu\Accounting\ErpFilament\Filament\App\Pages\AccountSetupWizard;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\BankConnections\BankConnectionResource;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Bills\BillResource;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\ChartOfAccounts\ChartOfAccountsResource;
use Liberu\Accounting\ErpFilament\Filament\App\Resources\Invoices\InvoiceResource;

class AccountingQuickActions extends Widget
{
    protected string $view = 'accounting-erp-filament::filament.app.widgets.accounting-quick-actions';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -10;

    /** @return array{name: string, currency: string, fiscal_year_start: string, completed: bool}|null */
    public function workspace(): ?array
    {
        $team = Filament::getTenant() ?? auth()->user()?->currentTeam;

        if (! $team instanceof Team) {
            return null;
        }

        $setup = $team->accounting_setup ?? [];

        return [
            'name' => $team->name,
            'currency' => $setup['currency'] ?? 'Not configured',
            'fiscal_year_start' => is_string($setup['fiscal_year_start'] ?? null) && Carbon::canBeCreatedFromFormat($setup['fiscal_year_start'], '!m-d')
                ? Carbon::createFromFormat('!m-d', $setup['fiscal_year_start'])->format('j F')
                : 'Not configured',
            'completed' => $team->accounting_setup_completed_at !== null,
        ];
    }

    /** @return list<array{label: string, description: string, icon: string, url: string}> */
    public function actions(): array
    {
        $actions = [];

        foreach ([
            [InvoiceResource::class, 'Money in · Invoices', 'Manage customer invoices and amounts due.', 'heroicon-o-document-text'],
            [BillResource::class, 'Money out · Supplier bills', 'Review purchases and upcoming payments.', 'heroicon-o-inbox-stack'],
            [BankConnectionResource::class, 'Banking · Bank connections', 'Connect your accounts and review bank feeds.', 'heroicon-o-building-library'],
            [ChartOfAccountsResource::class, 'Books · Chart of accounts', 'Review your accounts before posting entries.', 'heroicon-o-book-open'],
        ] as [$resource, $label, $description, $icon]) {
            if ($resource::canViewAny()) {
                $actions[] = compact('label', 'description', 'icon') + ['url' => $resource::getUrl('index')];
            }
        }

        if (AccountSetupWizard::canAccess()) {
            $actions[] = [
                'label' => 'Workspace settings',
                'description' => 'Review your business profile and optional connections.',
                'icon' => 'heroicon-o-cog-6-tooth',
                'url' => AccountSetupWizard::getUrl(),
            ];
        }

        return $actions;
    }
}
