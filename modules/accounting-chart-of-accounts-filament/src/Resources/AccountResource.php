<?php

declare(strict_types=1);

namespace Liberu\Accounting\ChartOfAccountsFilament\Resources;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\ChartOfAccounts\Actions\ArchiveAccount;
use Liberu\Accounting\ChartOfAccounts\Models\Account;
use Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource\Pages\CreateAccount;
use Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource\Pages\EditAccount;
use Liberu\Accounting\ChartOfAccountsFilament\Resources\AccountResource\Pages\ListAccounts;

final class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('legal_entity_id')->required()->numeric(),
            TextInput::make('code')->required()->maxLength(64),
            TextInput::make('name')->required()->maxLength(255),
            Textarea::make('description')->maxLength(500),
            Select::make('type')->required()->options(['asset' => 'Asset', 'liability' => 'Liability', 'equity' => 'Equity', 'revenue' => 'Revenue', 'expense' => 'Expense'])->live(),
            Select::make('normal_balance')->options(['debit' => 'Debit', 'credit' => 'Credit']),
            TextInput::make('parent_id')->nullable()->numeric(),
            Toggle::make('is_control_account')->default(false),
            Toggle::make('allow_manual_entry')->default(true),
            Toggle::make('is_active')->default(true),
            TextInput::make('locale')->maxLength(16),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->searchable()->sortable(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('type')->badge(),
            TextColumn::make('normal_balance')->badge(),
            IconColumn::make('is_control_account')->boolean(),
            IconColumn::make('is_active')->boolean(),
        ])->recordActions([
            EditAction::make(),
            Action::make('archive')->requiresConfirmation()->visible(fn (Account $record): bool => $record->is_active)->action(fn (Account $record): Account => app(ArchiveAccount::class)->handle($record)),
        ]);
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListAccounts::route('/'), 'create' => CreateAccount::route('/create'), 'edit' => EditAccount::route('/{record}/edit')];
    }
}
