<?php

declare(strict_types=1);

namespace Liberu\Accounting\MigrationFrameworkFilament\Resources;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\MigrationFramework\Models\MigrationBatch;

final class MigrationBatchResource extends Resource
{
    protected static ?string $model = MigrationBatch::class;

    protected static ?string $navigationLabel = 'Migration Batches';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('batch_ref')->required(), TextInput::make('source_id')->numeric()->required(), TextInput::make('mapping_id')->numeric()->required(), Toggle::make('dry_run')]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('batch_ref')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('total_count'), TextColumn::make('processed_count'), TextColumn::make('success_count'), TextColumn::make('error_count'), TextColumn::make('created_at')->dateTime()->sortable()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListMigrationBatches::route('/')];
    }
}
