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
        if (static::isUnauthorizedTenantAccess($audit)) {
            static::unauthorizedAccessNotification();

            return;
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

    /**
     * Check if the audit access is unauthorized based on tenancy settings.
     */
    protected static function isUnauthorizedTenantAccess($audit): bool
    {
        // Early return if tenancy is disabled
        if (! config('filament-auditing.tenancy.enabled')) {
            return false;
        }

        $tenant = Filament::getTenant();

        // No tenant in context means no restriction
        if (! $tenant) {
            return false;
        }

        // Get the tenant relationship name dynamically
        $tenantRelationshipName = $audit::getTenantRelationshipName();
        
        // Access the tenant as a property (not method) to get the actual model
        $auditTenant = $audit->{$tenantRelationshipName};

        // No tenant on audit or tenant matches - access is authorized
        if (! $auditTenant || $auditTenant->getKey() === $tenant->getKey()) {
            return false;
        }

        // Tenant mismatch - unauthorized access
        return true;
    }

    /**
     * Send an unauthorized access notification.
     */
    protected static function unauthorizedAccessNotification(): void
    {
        Notification::make()
            ->title(trans('filament-auditing::filament-auditing.notification.unauthorized'))
            ->danger()
            ->send();
    }

    protected static function restoredAuditNotification(): void
    {
        Notification::make()
            ->title(trans('filament-auditing::filament-auditing.notification.restored'))
            ->success()
            ->send();
    }

    protected static function unchangedAuditNotification(): void
    {
        Notification::make()
            ->title(trans('filament-auditing::filament-auditing.notification.unchanged'))
            ->warning()
            ->send();
    }
}
