<?php

namespace Liberu\Accounting\PeriodsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Periods\Models\AccountingPeriod;
use Liberu\Accounting\PeriodsFilament\Resources\AccountingPeriodResource\Pages\ListAccountingPeriods;

final class AccountingPeriodResource extends Resource
{
    protected static ?string $model = AccountingPeriod::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('book_id')->required(), TextInput::make('starts_on')->required(), TextInput::make('ends_on')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('book_id'), TextColumn::make('starts_on')->date(), TextColumn::make('ends_on')->date(), TextColumn::make('state')->badge(), TextColumn::make('posting_ends_on')->date()]);
    }

    /** @return array<string,PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListAccountingPeriods::route('/')];
    }
}
