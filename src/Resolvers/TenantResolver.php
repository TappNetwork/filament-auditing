<?php

namespace Tapp\FilamentAuditing\Resolvers;

use Filament\Facades\Filament;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

class TenantResolver implements Resolver
{
    /**
     * Resolve the tenant ID for the audit.
     *
     * @return int|string|null
     */
    public static function resolve(Auditable $auditable)
    {
        if (! config('filament-auditing.tenancy.enabled')) {
            return null;
        }

        // Try to get tenant from Filament context
        if (class_exists(Filament::class)) {
            $tenant = Filament::getTenant();
            if ($tenant) {
                return $tenant->getKey();
            }
        }

        return null;
    }
}
