<?php

namespace Tapp\FilamentAuditing\RelationManagers;

use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Tapp\FilamentAuditing\Concerns\HasExtraColumns;
use Tapp\FilamentAuditing\Concerns\HasFormattedData;
use Tapp\FilamentAuditing\Filament\Actions\RestoreAuditAction;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Schemas\AuditFilters;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Schemas\AuditInfolist;
use Tapp\FilamentAuditing\Filament\Tables\Columns\AuditValuesColumn;

class AuditsRelationManager extends RelationManager
{
    use HasExtraColumns;
    use HasFormattedData;

    protected static string $relationship = 'audits';

    protected static ?string $recordTitleAttribute = 'id';

    protected $listeners = ['updateAuditsRelationManager' => '$refresh'];

    public static function isLazy(): bool
    {
        return config('filament-auditing.is_lazy');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Filament::auth()->user()->can('audit', $ownerRecord);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-auditing::filament-auditing.table.heading');
    }

    public function infolist(Schema $schema): Schema
    {
        return AuditInfolist::configure($schema);
    }

    public function table(Table $table): Table
    {
        $tableActions = [
            ViewAction::make(),
            RestoreAuditAction::make('restore'),
        ];

        if (config('filament-auditing.grouped_table_actions')) {
            $tableActions = ActionGroup::make($tableActions);
        }

        return $table
            ->recordTitle(fn (Model $record): string => 'Audit')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['user', 'auditable'])->orderBy(config('filament-auditing.audits_sort.column'), config('filament-auditing.audits_sort.direction')))
            ->content(fn (): ?View => config('filament-auditing.custom_audits_view') ? view('filament-auditing::tables.custom-audit-content', Arr::add(self::customViewParameters(), 'owner', $this->getOwnerRecord())) : null)
            ->emptyStateHeading(trans('filament-auditing::filament-auditing.table.empty_state_heading'))
            ->columns(Arr::flatten([
                TextColumn::make('user.name')
                    ->label(trans('filament-auditing::filament-auditing.column.user_name')),
                TextColumn::make('event')
                    ->label(trans('filament-auditing::filament-auditing.column.event')),
                TextColumn::make('created_at')
                    ->since()
                    ->label(trans('filament-auditing::filament-auditing.column.created_at')),
                AuditValuesColumn::make('old_values')
                    ->formatStateUsing(fn (Column $column, Model $record, $state) => $this->formatData($record, name: $column->getName(), state: $column->getState()))
                    ->label(trans('filament-auditing::filament-auditing.column.old_values')),
                AuditValuesColumn::make('new_values')
                    ->formatStateUsing(fn (Column $column, $record, $state) => $this->formatData($record, name: $column->getName(), state: $column->getState()))
                    ->label(trans('filament-auditing::filament-auditing.column.new_values')),
                self::extraColumns(),
            ]))
            ->filters(AuditFilters::configure())
            ->headerActions([
                //
            ])
            ->recordActions($tableActions)
            ->toolbarActions([
                //
            ]);
    }

    protected static function customViewParameters(): array
    {
        return config('filament-auditing.custom_view_parameters');
    }

    protected function canCreate(): bool
    {
        return false;
    }

    protected function canEdit(Model $record): bool
    {
        return false;
    }

    protected function canDelete(Model $record): bool
    {
        return false;
    }
}
