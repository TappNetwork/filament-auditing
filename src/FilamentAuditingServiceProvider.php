<?php

namespace Tapp\FilamentAuditing;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Tapp\FilamentAuditing\Models\Audit;
use Tapp\FilamentAuditing\Resolvers\TenantResolver;

class FilamentAuditingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-auditing';

    public function configurePackage(Package $package): void
    {
        $package->name('filament-auditing')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasMigrations(['add_tenant_column_to_audits_table']);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        // Default values for view and restore audits. Add a policy to override these values
        Gate::define('audit', function ($user, $resource) {
            return true;
        });

        Gate::define('restoreAudit', function ($user, $resource) {
            return true;
        });

        // Register custom Audit model if tenancy is enabled
        // This allows us to add the tenant relationship to the Audit model
        if (config('filament-auditing.tenancy.enabled')) {
            Config::set('audit.implementation', Audit::class);

            // Register tenant resolver if tenancy is enabled
            $tenantColumn = config('filament-auditing.tenancy.column');
            $tenantModel = config('filament-auditing.tenancy.model');

            $relationshipName = config('filament-auditing.tenancy.relationship_name');

            if (! $relationshipName) {
                $relationshipName = Str::snake(class_basename($tenantModel));
            }

            if (! $tenantColumn) {
                if ($tenantModel) {
                    $tenantColumn = $relationshipName.'_id';
                } else {
                    $tenantColumn = 'tenant_id';
                }
            }

            // Merge tenant resolver into Laravel Auditing config
            // This is done at runtime because the resolver key is dynamic
            $resolvers = Config::get('audit.resolvers', []);
            $resolvers[$tenantColumn] = TenantResolver::class;

            Config::set('audit.resolvers', $resolvers);
        }
    }
}
