<?php

declare(strict_types=1);

namespace Liberu\Accounting\TimeTrackingFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\TimeTracking\Models\TimeEntry;

final class TimeEntryResource extends Resource
{
    protected static ?string $model = TimeEntry::class;

    protected static ?string $navigationLabel = 'Time entries';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('worker_ref')->required(), TextInput::make('worked_on')->required(), TextInput::make('hours')->numeric()->required(), TextInput::make('project_ref'), TextInput::make('task_ref'), TextInput::make('billable_rate')->numeric(), TextInput::make('cost_rate')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('worker_ref')->searchable(), TextColumn::make('worked_on')->date()->sortable(), TextColumn::make('hours'), TextColumn::make('project_ref'), TextColumn::make('status')->badge(), IconColumn::make('billable')->boolean()]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListTimeEntries::route('/')];
    }
}
