<?php

namespace Tapp\FilamentAuditing\RelationManagers;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Audit;

class AuditsRelationManager extends RelationManager
{
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

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user')->orderBy(config('filament-auditing.audits_sort.column'), config('filament-auditing.audits_sort.direction')))
            ->columns(Arr::flatten([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(trans('filament-auditing::filament-auditing.column.user_name')),
                Tables\Columns\TextColumn::make('event')
                    ->label(trans('filament-auditing::filament-auditing.column.event')),
                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->label(trans('filament-auditing::filament-auditing.column.created_at')),
                Tables\Columns\ViewColumn::make('old_values')
                    ->view('filament-auditing::tables.columns.key-value')
                    ->label(trans('filament-auditing::filament-auditing.column.old_values')),
                Tables\Columns\ViewColumn::make('new_values')
                    ->view('filament-auditing::tables.columns.key-value')
                    ->label(trans('filament-auditing::filament-auditing.column.new_values')),
                self::extraColumns(),
            ]))
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label(trans('filament-auditing::filament-auditing.action.restore'))
                    ->action(fn (Audit $record) => static::restoreAuditSelected($record))
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn (Audit $record, RelationManager $livewire): bool => Filament::auth()->user()->can('restoreAudit', $livewire->ownerRecord) && $record->event === 'updated')
                    ->after(function ($livewire) {
                        $livewire->dispatch('auditRestored');
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }

    protected static function extraColumns()
    {
        return Arr::map(config('filament-auditing.audits_extend'), function ($buildParameters, $columnName) {
            return collect($buildParameters)->pipeThrough([
                function ($collection) use ($columnName) {
                    $columnClass = (string) $collection->get('class');

                    if (! is_null($collection->get('methods'))) {
                        $columnClass = $columnClass::make($columnName);

                        collect($collection->get('methods'))->transform(function ($value, $key) use ($columnClass) {
                            if (is_numeric($key)) {
                                return $columnClass->$value();
                            }

                            return $columnClass->$key($value);
                        });

                        return $columnClass;
                    }

                    return $columnClass::make($columnName);
                },
            ]);
        });
    }

    protected static function restoreAuditSelected($audit)
    {
        $morphClass = Relation::getMorphedModel($audit->auditable_type) ?? $audit->auditable_type;

        $record = $morphClass::find($audit->auditable_id);

        if (! $record) {
            self::unchangedAuditNotification();

            return;
        }

        if ($audit->event !== 'updated') {
            self::unchangedAuditNotification();

            return;
        }

        $restore = $audit->old_values;

        Arr::pull($restore, 'id');

        if (is_array($restore)) {

            foreach ($restore as $key => $item) {
                $decode = json_decode($item);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $restore[$key] = $decode;
                }
            }

            $record->fill($restore);
            $record->save();

            self::restoredAuditNotification();

            return;
        }

        self::unchangedAuditNotification();
    }

    protected static function restoredAuditNotification()
    {
        Notification::make()
            ->title(trans('filament-auditing::filament-auditing.notification.restored'))
            ->success()
            ->send();
    }

    protected static function unchangedAuditNotification()
    {
        Notification::make()
            ->title(trans('filament-auditing::filament-auditing.notification.unchanged'))
            ->warning()
            ->send();
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
