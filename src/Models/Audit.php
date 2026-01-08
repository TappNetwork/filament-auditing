<?php

namespace Tapp\FilamentAuditing\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Models\Audit as BaseAudit;

class Audit extends BaseAudit
{
    /**
     * Boot the model and set up dynamic tenant relationship.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Dynamically resolve the tenant relationship based on configuration
        if (config('filament-auditing.tenancy.enabled') && config('filament-auditing.tenancy.model')) {
            $tenantModel = config('filament-auditing.tenancy.model');
            $relationshipName = static::getTenantRelationshipName();

            static::resolveRelationUsing($relationshipName, function ($model) use ($tenantModel) {
                $tenantColumn = config('filament-auditing.tenancy.column');
                if (! $tenantColumn) {
                    $relationshipName = config('filament-auditing.tenancy.relationship_name');
                    if (! $relationshipName) {
                        $relationshipName = \Illuminate\Support\Str::snake(class_basename($tenantModel));
                    }
                    $tenantColumn = $relationshipName.'_id';
                }

                return $model->belongsTo($tenantModel, $tenantColumn);
            });
        }
    }

    /**
     * Get the tenant relationship name based on configuration.
     */
    protected static function getTenantRelationshipName(): string
    {
        $relationshipName = config('filament-auditing.tenancy.relationship_name');
        if ($relationshipName) {
            return $relationshipName;
        }

        $tenantModel = config('filament-auditing.tenancy.model');
        if (! $tenantModel) {
            return 'tenant';
        }

        return \Illuminate\Support\Str::snake(class_basename($tenantModel));
    }

    /**
     * Get the tenant relationship.
     * This is a convenience method that uses the dynamically resolved relationship.
     */
    public function tenant(): ?BelongsTo
    {
        if (! config('filament-auditing.tenancy.enabled')) {
            return null;
        }

        $relationshipName = static::getTenantRelationshipName();

        return $this->{$relationshipName}();
    }
}
