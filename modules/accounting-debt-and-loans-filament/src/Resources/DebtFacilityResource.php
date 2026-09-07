<?php

declare(strict_types=1);

namespace Liberu\Accounting\DebtAndLoansFilament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\DebtAndLoans\Models\DebtFacility;
use Liberu\Accounting\DebtAndLoansFilament\Resources\DebtFacilityResource\Pages\ListDebtFacilities;

final class DebtFacilityResource extends Resource
{
    protected static ?string $model = DebtFacility::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('facility_ref')->required(), TextInput::make('lender_ref')->required(), TextInput::make('currency')->required()->maxLength(3), TextInput::make('limit_amount')->numeric()->required(), TextInput::make('interest_rate')->numeric(), TextInput::make('start_date')->type('date')->required(), TextInput::make('maturity_date')->type('date')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('facility_ref')->searchable(), TextColumn::make('lender_ref'), TextColumn::make('currency'), TextColumn::make('drawn_amount')->money(), TextColumn::make('status')->badge()]);
    }

    public static function getPages(): array
    {
        return ['index' => ListDebtFacilities::route('/')];
    }
}
