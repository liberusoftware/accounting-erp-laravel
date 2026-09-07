<?php

declare(strict_types=1);

namespace Liberu\Accounting\ForecastsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\Forecasts\Models\Forecast;

final class ForecastResource extends Resource
{
    protected static ?string $model = Forecast::class;

    protected static ?string $navigationLabel = 'Forecasts';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('forecast_ref')->required(), TextInput::make('name')->required(), TextInput::make('currency')->required()->length(3), TextInput::make('method')->required(), TextInput::make('base_period')->required(), TextInput::make('horizon_periods')->numeric()->required(), TextInput::make('scenario_ref')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('forecast_ref')->searchable(), TextColumn::make('name'), TextColumn::make('method')->badge(), TextColumn::make('scenario_ref'), TextColumn::make('base_period'), TextColumn::make('horizon_periods'), TextColumn::make('status')->badge()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListForecasts::route('/')];
    }
}
