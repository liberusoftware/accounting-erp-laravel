<?php

declare(strict_types=1);

namespace Liberu\Accounting\EInvoicingFilament\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Liberu\Accounting\EInvoicing\Models\EInvoiceDocument;

final class EInvoiceResource extends Resource
{
    protected static ?string $model = EInvoiceDocument::class;

    protected static ?string $navigationLabel = 'E-invoicing';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('legal_entity_id')->numeric()->required(), TextInput::make('document_ref')->required(), Select::make('document_type')->options(['invoice' => 'Invoice', 'credit' => 'Credit'])->required(), Select::make('format')->options(['ubl' => 'UBL', 'factur-x' => 'Factur-X', 'peppol' => 'Peppol'])->required(), TextInput::make('tax_id')->required(), TextInput::make('counterparty_ref')->required(), TextInput::make('currency')->required()->length(3)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('document_ref')->searchable(), TextColumn::make('document_type'), TextColumn::make('format'), TextColumn::make('status')->badge(), TextColumn::make('provider_ref'), TextColumn::make('received_at')->dateTime()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListEInvoices::route('/')];
    }
}
