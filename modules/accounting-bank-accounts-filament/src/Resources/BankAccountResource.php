<?php

declare(strict_types=1);

namespace Liberu\Accounting\BankAccountsFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\BankAccounts\Models\BankAccount;
use Liberu\Accounting\BankAccountsFilament\Resources\BankAccountResource\Pages\CreateBankAccount;
use Liberu\Accounting\BankAccountsFilament\Resources\BankAccountResource\Pages\ListBankAccounts;

final class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    protected static ?string $navigationLabel = 'Bank Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('legal_entity_id')->numeric()->required(),
            TextInput::make('name')->required()->maxLength(160),
            TextInput::make('institution_name')->maxLength(160),
            Select::make('account_type')->required()->options(['bank' => 'Bank', 'current' => 'Current', 'savings' => 'Savings', 'credit' => 'Credit', 'loan' => 'Loan', 'cash' => 'Cash']),
            TextInput::make('currency')->required()->length(3)->dehydrateStateUsing(fn (string $state): string => strtoupper($state)),
            TextInput::make('opening_balance')->numeric()->required()->minValue(0), DatePicker::make('opening_date')->required(),
            TextInput::make('account_number')->password()->dehydrated(), TextInput::make('routing_number')->password()->dehydrated(),
            TextInput::make('feed_reference')->maxLength(160),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(), TextColumn::make('institution_name')->searchable(), TextColumn::make('account_type')->badge(), TextColumn::make('currency')->badge(), TextColumn::make('current_balance')->money()->sortable(), TextColumn::make('status')->badge(),
        ])->defaultSort('name');
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListBankAccounts::route('/'), 'create' => CreateBankAccount::route('/create')];
    }
}
