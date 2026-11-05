<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Pages\AccountSetupWizard;
use App\Filament\App\Resources\BankConnections\BankConnectionResource;
use App\Filament\App\Resources\Bills\BillResource;
use App\Filament\App\Resources\ChartOfAccounts\ChartOfAccountsResource;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use Filament\Widgets\Widget;

class AccountingQuickActions extends Widget
{
    protected string $view = 'filament.app.widgets.accounting-quick-actions';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -10;

    /** @return list<array{label: string, description: string, icon: string, url: string}> */
    public function actions(): array
    {
        $actions = [];

        foreach ([
            [InvoiceResource::class, 'Invoices', 'Manage customer invoices and amounts due.', 'heroicon-o-document-text'],
            [BillResource::class, 'Supplier bills', 'Review purchases and upcoming payments.', 'heroicon-o-inbox-stack'],
            [BankConnectionResource::class, 'Bank connections', 'Connect your accounts and review bank feeds.', 'heroicon-o-building-library'],
            [ChartOfAccountsResource::class, 'Chart of accounts', 'Review your accounts before posting entries.', 'heroicon-o-book-open'],
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
