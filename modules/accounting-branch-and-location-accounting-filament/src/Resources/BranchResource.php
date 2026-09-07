<?php

declare(strict_types=1);

namespace Liberu\Accounting\BranchLocationAccountingFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\BranchLocationAccounting\Models\Branch;
use Liberu\Accounting\BranchLocationAccountingFilament\Resources\BranchResource\Pages\ListBranches;

final class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('code')->required(), TextInput::make('name')->required(), TextInput::make('location'), TextInput::make('local_tax_code'), TextInput::make('sequence_prefix'), TextInput::make('allocation_rule'), TextInput::make('performance_target')->numeric(), TextInput::make('statutory_reference')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('code')->searchable()->sortable(), TextColumn::make('name')->searchable(), TextColumn::make('location'), TextColumn::make('local_tax_code'), TextColumn::make('status')->badge(), TextColumn::make('performance_target')]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    public static function getPages(): array
    {
        return ['index' => ListBranches::route('/')];
    }
}
