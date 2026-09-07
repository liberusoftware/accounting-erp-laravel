<?php

declare(strict_types=1);

namespace Liberu\Accounting\PurchaseRequisitionsFilament\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\Accounting\PurchaseRequisitions\Actions\RecordApproval;
use Liberu\Accounting\PurchaseRequisitions\Actions\TransitionRequisition;
use Liberu\Accounting\PurchaseRequisitions\Enums\RequisitionStatus;
use Liberu\Accounting\PurchaseRequisitions\Models\PurchaseRequisition;
use Liberu\Accounting\PurchaseRequisitionsFilament\Resources\PurchaseRequisitionResource\Pages\ListPurchaseRequisitions;

final class PurchaseRequisitionResource extends Resource
{
    protected static ?string $model = PurchaseRequisition::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'Purchasing';

    protected static ?string $navigationLabel = 'Purchase requisitions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('requester_ref')->label('Requester')->searchable(),
            TextColumn::make('title')->searchable()->placeholder('—'),
            TextColumn::make('total_amount')->money(fn (PurchaseRequisition $record): string => $record->currency),
            TextColumn::make('status')->badge(),
            TextColumn::make('submitted_at')->dateTime(),
            TextColumn::make('approved_at')->dateTime(),
        ])->recordActions([
            Action::make('transition')->label('Change status')->icon('heroicon-o-arrow-path')
                ->schema([Select::make('status')->options(collect(RequisitionStatus::cases())->mapWithKeys(fn (RequisitionStatus $status): array => [$status->value => ucfirst(str_replace('_', ' ', $status->value))])->all())->required()])
                ->action(fn (PurchaseRequisition $record, array $data, TransitionRequisition $action): PurchaseRequisition => $action->handle($record, RequisitionStatus::from((string) $data['status']))),
            Action::make('approve')->label('Record approval')->icon('heroicon-o-check-badge')->visible(fn (PurchaseRequisition $record): bool => $record->status === RequisitionStatus::Submitted)
                ->schema([TextInput::make('approver_ref')->required()->maxLength(190), Select::make('decision')->options(['approved' => 'Approved', 'rejected' => 'Rejected'])->required(), TextInput::make('reason')->maxLength(1000)])
                ->action(fn (PurchaseRequisition $record, array $data, RecordApproval $action): mixed => $action->handle($record, $data)),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('team_id', (int) (auth()->user()?->current_team_id ?? -1));
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return ['index' => ListPurchaseRequisitions::route('/')];
    }
}
