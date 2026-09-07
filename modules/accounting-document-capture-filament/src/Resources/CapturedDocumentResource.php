<?php

declare(strict_types=1);

namespace Liberu\Accounting\DocumentCaptureFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\DocumentCapture\Models\CapturedDocument;

final class CapturedDocumentResource extends Resource
{
    protected static ?string $model = CapturedDocument::class;

    protected static ?string $navigationLabel = 'Document capture';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('source_channel')->options(['mobile' => 'Mobile', 'web' => 'Web', 'email' => 'Email'])->required(), TextInput::make('file_ref')->required(), TextInput::make('checksum')->required(), TextInput::make('mime_type')->required(), TextInput::make('team_id')->numeric()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('source_channel'), TextColumn::make('file_ref')->searchable(), TextColumn::make('status')->badge(), TextColumn::make('supplier_ref'), TextColumn::make('confidence'), TextColumn::make('retention_until')->date()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListCapturedDocuments::route('/')];
    }
}
