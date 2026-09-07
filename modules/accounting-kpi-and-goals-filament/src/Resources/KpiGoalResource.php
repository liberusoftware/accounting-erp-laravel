<?php

declare(strict_types=1);

namespace Liberu\Accounting\KpiAndGoalsFilament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\KpiAndGoals\Models\KpiGoal;

final class KpiGoalResource extends Resource
{
    protected static ?string $model = KpiGoal::class;

    protected static ?string $navigationLabel = 'KPI Goals';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('goal_ref')->required(), TextInput::make('name')->required(), TextInput::make('owner_ref')->required(), DatePicker::make('period_start')->required(), DatePicker::make('period_end')->required(), TextInput::make('baseline')->numeric()->required(), TextInput::make('target')->numeric()->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('goal_ref')->searchable(), TextColumn::make('name')->searchable(), TextColumn::make('owner_ref'), TextColumn::make('period_end')->date()->sortable(), TextColumn::make('target'), TextColumn::make('status')->badge(), TextColumn::make('alerts_count')->counts('alerts')])->defaultSort('period_end', 'asc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListKpiGoals::route('/')];
    }
}
