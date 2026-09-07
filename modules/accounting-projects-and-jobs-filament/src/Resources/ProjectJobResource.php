<?php

declare(strict_types=1);

namespace Liberu\Accounting\ProjectsAndJobsFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\ProjectsAndJobs\Models\ProjectJob;

final class ProjectJobResource extends Resource
{
    protected static ?string $model = ProjectJob::class;

    protected static ?string $navigationLabel = 'Projects and jobs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('name')->required(), TextInput::make('code'), TextInput::make('customer_id')->numeric(), TextInput::make('start_date'), TextInput::make('end_date'), TextInput::make('budget_amount')->numeric(), TextInput::make('budget_currency')->length(3)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('name')->searchable(), TextColumn::make('code')->searchable(), TextColumn::make('customer.name'), TextColumn::make('start_date')->date()->sortable(), TextColumn::make('end_date')->date()->sortable(), TextColumn::make('status')->badge()])->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListProjectJobs::route('/')];
    }
}
