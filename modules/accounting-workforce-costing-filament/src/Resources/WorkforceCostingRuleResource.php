<?php

declare(strict_types=1);

namespace Liberu\Accounting\WorkforceCostingFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\WorkforceCosting\Models\WorkforceCostingRule;

final class WorkforceCostingRuleResource extends Resource
{
    protected static ?string $model = WorkforceCostingRule::class;

    protected static ?string $navigationLabel = 'Workforce costing rules';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), TextInput::make('allocation_type')->required(), TextInput::make('allocation_ref'), TextInput::make('hourly_rate')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name'), TextColumn::make('allocation_type')->badge(), TextColumn::make('allocation_ref'), TextColumn::make('hourly_rate'), IconColumn::make('capitalize')->boolean(), IconColumn::make('active')->boolean()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListWorkforceCostingRules::route('/')];
    }
}
