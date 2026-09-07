<?php

declare(strict_types=1);

namespace Liberu\Accounting\DepositsAndClearingFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\DepositsAndClearing\Models\ClearingDeposit;
use Liberu\Accounting\DepositsAndClearingFilament\Resources\ClearingDepositResource\Pages\ListClearingDeposits;

final class ClearingDepositResource extends Resource
{
    protected static ?string $model = ClearingDeposit::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('deposit_ref')->required(), TextInput::make('account_ref')->required(), TextInput::make('currency')->required()->maxLength(3), TextInput::make('deposit_date')->type('date')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('deposit_ref')->searchable(), TextColumn::make('provider'), TextColumn::make('gross_amount')->money(), TextColumn::make('fee_amount')->money(), TextColumn::make('status')->badge()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListClearingDeposits::route('/')];
    }
}
