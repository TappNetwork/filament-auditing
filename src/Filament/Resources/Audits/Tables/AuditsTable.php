<?php

namespace Tapp\FilamentAuditing\Filament\Resources\Audits\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tapp\FilamentAuditing\Concerns\HasExtraColumns;
use Tapp\FilamentAuditing\Concerns\HasFormattedData;
use Tapp\FilamentAuditing\Filament\Actions\RestoreAuditAction;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Schemas\AuditFilters;
use Tapp\FilamentAuditing\Filament\Tables\Columns\AuditValuesColumn;

class AuditsTable
{
    use HasExtraColumns;
    use HasFormattedData;

    public static function configure(Table $table): Table
    {
        $tableActions = [
            ViewAction::make(),
            RestoreAuditAction::make('restore'),
        ];

        if (config('filament-auditing.grouped_table_actions')) {
            $tableActions = ActionGroup::make($tableActions);
        }

        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['user', 'auditable'])
                    ->orderBy(config('filament-auditing.audits_sort.column'), config('filament-auditing.audits_sort.direction'));
            })
            ->emptyStateHeading(trans('filament-auditing::filament-auditing.table.empty_state_heading'))
            ->columns(Arr::flatten([
                TextColumn::make('user.name')
                    ->label(trans('filament-auditing::filament-auditing.column.user_name')),
                TextColumn::make('auditable_type')
                    ->label(trans('filament-auditing::filament-auditing.column.auditable_type'))
                    ->formatStateUsing(function (string $state) {
                        return Str::afterLast($state, '\\');
                    }),
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
            ->recordActions($tableActions)
            ->toolbarActions([
            ]);
    }
}
