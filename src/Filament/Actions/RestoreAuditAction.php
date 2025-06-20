<?php

namespace Tapp\FilamentAuditing\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Facades\Filament;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Schemas\Components\Section;
use OwenIt\Auditing\Contracts\Audit;
use Tapp\FilamentAuditing\Concerns\CanRestoreAudit;
use Tapp\FilamentAuditing\Filament\Infolists\Components\AuditValuesEntry;

class RestoreAuditAction extends Action
{
    use CanCustomizeProcess;
    use CanRestoreAudit;

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->icon('heroicon-o-arrow-path')
            ->modalWidth('xl')
            ->modalAutofocus(false)
            ->label(trans('filament-auditing::filament-auditing.action.restore'))
            ->action(fn (Audit $record) => static::restoreAuditSelected($record))
            ->schema([
                Section::make('Restoring From')
                    ->schema([
                        KeyValueEntry::make('new_values')
                            ->keyLabel('Field')
                            ->hiddenLabel(),
                    ]),
                Section::make('Restoring To')
                    ->schema([
                        KeyValueEntry::make('old_values')
                            ->keyLabel('Field')
                            ->hiddenLabel(),
                        //AuditValuesEntry::make('old_values')
                        //    ->hiddenLabel()
                    ]),
            ])
            ->requiresConfirmation()
            ->visible(fn (Audit $record): bool => Filament::auth()->user()->can('restoreAudit', $record->auditable) && $record->event === 'updated')
            ->after(function ($livewire) {
                $livewire->dispatch('auditRestored');
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'restoreAudit';
    }
}
