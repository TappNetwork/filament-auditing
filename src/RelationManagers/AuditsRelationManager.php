<?php

namespace Tapp\FilamentAuditing\RelationManagers;

use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Models\Audit;

class AuditsRelationManager extends RelationManager
{
    protected static string $relationship = 'audits';

    protected static ?string $recordTitleAttribute = 'id';

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return auth()->user()->can('audit', $ownerRecord);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('event'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created'),
                Tables\Columns\ViewColumn::make('old_values')
                    ->view('filament-auditing::tables.columns.key-value'),
                Tables\Columns\ViewColumn::make('new_values')
                    ->view('filament-auditing::tables.columns.key-value'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->action(fn (Audit $record) => static::restoreAuditSelected($record))
                    ->icon('heroicon-o-refresh')
                    ->requiresConfirmation()
                    ->visible(fn (Audit $record): bool => auth()->user()->can('restoreAudit', $record) && $record->event === 'updated'),
            ])
            ->bulkActions([
                //
            ]);
    }

    protected static function restoreAuditSelected($audit)
    {
        $record = $audit->auditable_type::find($audit->auditable_id);

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
            $record->fill($restore);
            $record->save();

            self::restoredAuditNotification();

            $refresh;

            return;
        }

        self::unchangedAuditNotification();
    }

    protected static function restoredAuditNotification()
    {
        Notification::make()
            ->title(__('Audit restored'))
            ->success()
            ->send();
    }

    protected static function unchangedAuditNotification()
    {
        Notification::make()
            ->title(__('Nothing to change'))
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
