<?php

namespace Tapp\FilamentAuditing\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

trait BelongsToTenant
{
    /**
     * Get the tenant relationship name.
     */
    public static function getTenantRelationshipName(): string
    {
        // Use configured relationship name if provided
        if ($relationshipName = config('filament-auditing.tenancy.relationship_name')) {
            return $relationshipName;
        }

        // Auto-detect from tenant model class name
        $tenantModel = config('filament-auditing.tenancy.model');

        if (! $tenantModel) {
            if (config('filament-auditing.tenancy.enabled')) {
                throw new \Exception('Tenant model not configured in filament-auditing.tenancy.model');
            }

            return 'tenant'; // Return a default value when tenancy is disabled
        }

        return Str::snake(class_basename($tenantModel));
    }

    /**
     * Get the tenant column name.
     */
    public static function getTenantColumnName(): string
    {
        // Use configured column name if provided
        if ($columnName = config('filament-auditing.tenancy.column')) {
            return $columnName;
        }

        // Auto-detect from tenant model class name
        return static::getTenantRelationshipName().'_id';
    }

    /**
     * Get the tenant relationship instance.
     * This provides a typed method for IDEs and static analysis.
     */
    public function tenant(): ?BelongsTo
    {
        if (! config('filament-auditing.tenancy.enabled')) {
            return null;
        }

        $tenantModel = config('filament-auditing.tenancy.model');

        if (! $tenantModel) {
            throw new \Exception('Tenant model not configured in filament-auditing.tenancy.model');
        }

        return $this->belongsTo($tenantModel, static::getTenantColumnName());
    }
}
