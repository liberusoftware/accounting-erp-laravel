<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCost;

final class WorkforceCostResource extends Resource
{
    protected static ?string $model = WorkforceCost::class;

    protected static ?string $navigationLabel = 'Workforce costs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('worker_ref')->required(), DatePicker::make('cost_date')->required(), TextInput::make('hours')->numeric(), TextInput::make('hourly_rate')->numeric(), TextInput::make('amount')->numeric()->required(), TextInput::make('allocation_type'), TextInput::make('allocation_ref')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('worker_ref'), TextColumn::make('cost_date')->date(), TextColumn::make('hours'), TextColumn::make('amount'), TextColumn::make('allocation_type')->badge(), TextColumn::make('allocation_ref'), IconColumn::make('capitalized')->boolean(), TextColumn::make('status')->badge()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListWorkforceCosts::route('/')];
    }
}
