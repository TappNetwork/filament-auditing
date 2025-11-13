<?php

namespace Tapp\FilamentAuditing;

use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentAuditingServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-auditing';

    public function configurePackage(Package $package): void
    {
        $package->name('filament-auditing')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews();
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        // Default values for view and restore audits. Add a policy to override these values
        Gate::define('audit', function ($user, $resource = null) {
            return true;
        });

        Gate::define('restoreAudit', function ($user, $resource = null) {
            return true;
        });
    }
}
