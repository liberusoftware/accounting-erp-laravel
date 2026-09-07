<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ConsolidationGroups;

use App\Filament\App\Resources\ConsolidationGroups\Pages\CreateConsolidationGroup;
use App\Filament\App\Resources\ConsolidationGroups\Pages\EditConsolidationGroup;
use App\Filament\App\Resources\ConsolidationGroups\Pages\ListConsolidationGroups;
use App\Models\ConsolidationGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ConsolidationGroupResource extends Resource
{
    protected static string|\UnitEnum|null $navigationGroup = 'Reports';

    #[\Override]
    protected static ?string $model = ConsolidationGroup::class;

    // ConsolidationGroup has an ownerTeam() relation, not the `team()` Filament's
    // auto tenant scope expects — scope + stamp owner_team_id manually instead.
    protected static bool $isScopedToTenant = false;

    #[\Override]
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    #[\Override]
    protected static ?string $navigationLabel = 'Consolidation Groups';

    #[\Override]
    public static function getEloquentQuery(): Builder
    {
        // A team only manages the groups it owns.
        return parent::getEloquentQuery()->where('owner_team_id', Filament::getTenant()?->getKey());
    }

    /**
     * Team ids the acting user may add as members — the teams they belong to.
     * Used both to filter the form options and to enforce membership server-side
     * (Filament does not re-validate submitted relationship ids against options).
     *
     * @return array<int, int>
     */
    public static function allowedTeamIds(): array
    {
        return Auth::user()?->allTeams()->pluck('id')->map(fn ($v): int => (int) $v)->all() ?? [];
    }

    /**
     * Drop any attached member team the acting user is not entitled to add.
     * Called after create/save so a crafted request can't attach foreign teams
     * (option-filtering alone is not enforced by Filament on submit).
     */
    public static function enforceAllowedMembers(ConsolidationGroup $group): void
    {
        $allowed = self::allowedTeamIds();
        $keep = $group->members()->pluck('teams.id')->map(fn ($v): int => (int) $v)->intersect($allowed)->all();
        $group->members()->sync($keep);
    }

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            Select::make('members')
                ->label('Member teams')
                // Only teams the user belongs to are selectable — a group must
                // not be able to pull in (and later disclose) another tenant's books.
                ->relationship('members', 'name', modifyQueryUsing: fn (Builder $query): Builder => $query->whereIn('teams.id', self::allowedTeamIds()))
                ->multiple()
                ->preload()
                ->required(),
        ]);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('members_count')->counts('members')->label('Members'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListConsolidationGroups::route('/'),
            'create' => CreateConsolidationGroup::route('/create'),
            'edit' => EditConsolidationGroup::route('/{record}/edit'),
        ];
    }
}
