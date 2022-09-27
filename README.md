# Filament Laravel Auditing

A Filament plugin for [Laravel Auditing](https://laravel-auditing.com/) package.
This plugin contains a relation manager for audits that you can add to your Filament resources.

This package provides a Filament resource manager that shows a table with all audits on view and edit pages and allows restore audits.

## Installation

> **Note**
> This plugin uses the [Laravel Auditing](https://laravel-auditing.com/) package. First install and configure this package.

You can install the plugin via composer:

```bash
composer require tapp/filament-auditing
```

You can publish the view file with:

```bash
php artisan vendor:publish --tag="filament-auditing-views"
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-auditing-config"
```

This is the content of the published config file:

```php
<?php

return [

    'audits_sort' => [
        'column' => 'created_at',
        'direction' => 'desc',
    ],
    
    'audits_extend' => [
        // eg. \Filament\Tables\Columns\TextColumn::make('url'),
    ]

];
```

The `audits_sort` can be used to change the default sort on the audits table. 

## Usage

To show the audits table in your Filament resource, just add `AuditsRelationManager::class` on your resource's `getRelations` method:

```php
use Tapp\FilamentAuditing\RelationManagers\AuditsRelationManager;

public static function getRelations(): array
{
    return [
        // ...
        AuditsRelationManager::class,
    ];
}
```

That's it, you're all set!

If you access your resource, and edit some data, you will now see the audits table on edit and view pages.

### Extending Columns
In case you need to add a column to the AuditsRelationManager that does
not already exist in the table, you can add it in the config, and it will be
prepended to the table builder.

```php
<?php

return [

    'audits_extend' => [
       \Filament\Tables\Columns\TextColumn::make('url'),
       \Filament\Tables\Columns\TextColumn::make('ip_address'),
    ]

];
```


### Permissions

Two permissions are registered by default allowing access to:

- `audit`: view audits
- `restoreAudit`: restore audits

You can override these permissions by adding a policy with `audit` and `restoreAudit`.
