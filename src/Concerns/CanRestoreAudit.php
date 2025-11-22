<?php

namespace Tapp\FilamentAuditing\Concerns;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

trait CanRestoreAudit
{
    protected static function restoreAuditSelected($audit)
    {
        // Verify tenant ownership if tenancy is enabled
        // This is a defense-in-depth security check. Filament's automatic scoping
        // should already prevent accessing audits from other tenants, but we check
        // here as an additional safety measure.
        if (config('filament-auditing.tenancy.enabled') && class_exists(Filament::class)) {
            $tenant = Filament::getTenant();
            if ($tenant) {
                // Use the tenant relationship to check ownership
                $auditTenant = $audit->tenant();
                if ($auditTenant && $auditTenant->getKey() !== $tenant->getKey()) {
                    Notification::make()
                        ->title(trans('filament-auditing::filament-auditing.notification.unauthorized'))
                        ->danger()
                        ->send();

                    return;
                }
            }
        }

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

        if (is_array($restore)) {
            Arr::pull($restore, 'id');

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
}
