<?php

declare(strict_types=1);

namespace Liberu\Accounting\MultiCurrencyFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Liberu\Accounting\MultiCurrency\Models\RevaluationRun;

final class RevaluationResource extends Resource
{
    protected static ?string $model = RevaluationRun::class;

    protected static ?string $navigationLabel = 'Currency Revaluations';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('run_ref')->required(), TextInput::make('as_of_date')->required(), TextInput::make('functional_currency')->required()->length(3)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('run_ref')->searchable(), TextColumn::make('as_of_date')->date()->sortable(), TextColumn::make('functional_currency'), TextColumn::make('status')->badge(), TextColumn::make('unrealized_gain')->money(fn ($record) => $record->functional_currency), TextColumn::make('unrealized_loss')->money(fn ($record) => $record->functional_currency), TextColumn::make('positions_count')->counts('positions')])->filters([SelectFilter::make('status')->options(['calculated' => 'Calculated', 'posted' => 'Posted', 'reconciled' => 'Reconciled', 'failed' => 'Failed'])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListRevaluations::route('/')];
    }
}
