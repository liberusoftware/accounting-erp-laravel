<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectCostingFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\ProjectCosting\Models\ProjectCost;

final class ProjectCostResource extends Resource
{
    protected static ?string $model = ProjectCost::class;

    protected static ?string $navigationLabel = 'Project costs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('project_job_id')->numeric()->required(), TextInput::make('type')->required(), TextInput::make('occurred_on')->required(), TextInput::make('amount')->numeric()->required(), TextInput::make('currency')->required()->length(3), TextInput::make('source_ref')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('project_job_id')->sortable(), TextColumn::make('type')->badge(), TextColumn::make('occurred_on')->date()->sortable(), TextColumn::make('amount'), TextColumn::make('currency'), TextColumn::make('source_ref')->searchable()])->defaultSort('occurred_on', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListProjectCosts::route('/')];
    }
}
