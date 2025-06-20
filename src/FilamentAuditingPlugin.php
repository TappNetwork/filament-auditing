<?php

namespace Tapp\FilamentAuditing;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentAuditingPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-auditing';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources(
                config('filament-auditing.resources')
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
