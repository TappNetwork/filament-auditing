# Filament Laravel Auditing

A Filament plugin for [Laravel Auditing](https://laravel-auditing.com/) package.
This plugin contains a relation manager for audits that you can add to your Filament resources.

This package provides a Filament resource manager that shows a table with all audits on view and edit pages and allows
restore audits.

## Installation

> **Note**
> This plugin uses the [Laravel Auditing](https://laravel-auditing.com/) package. First install and configure this
> package.

You can install the plugin via composer:

```bash
composer require tapp/filament-auditing:"^3.0"
```

> **Note** 
> For **Filament 2.x** check the **[2.x](https://github.com//TappNetwork/filament-auditing/tree/2.x)** branch

You can publish the view file with:

```bash
php artisan vendor:publish --tag="filament-auditing-views"
```
You can publish the translation files with:

```bash
php artisan vendor:publish --tag="filament-auditing-translations"
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

    'is_lazy' => true,
    
    'audits_extend' => [
        // 'url' => [
        //     'class' => \Filament\Tables\Columns\TextColumn::class,
        //     'methods' => [
        //         'sortable',
        //         'searchable' => true,
        //         'default' => 'N/A'
        //     ]
        // ],
    ]

];
```

The `audits_sort` can be used to change the default sort on the audits table.

## Usage

To show the audits table in your Filament resource, just add `AuditsRelationManager::class` on your
resource's `getRelations` method:

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
not already exist in the table, you can add it in the config using the format denoted in the example below, and it will be
prepended to the table builder. The name of the column to be added is the key of an associative array that contains other information about the class, as shown in the example below. The class instance of the column must be added, but the methods can be left out if not required, or added wherever necessary. 

```php
<?php

return [

    'audits_extend' => [
       'url' => [
           'class' => \Filament\Tables\Columns\TextColumn::class, // required
           'methods' => [
               'sortable',
               'default' => 'NIL',
            ],
        ],
    ]

];
```


After adding this information in the config, please run this command for changes to take place.

```bash
php artisan optimize
```

As things stand, methods with two required parameters are not supported.

### Permissions

Two permissions are registered by default, allowing access to:

- `audit`: view audits
- `restoreAudit`: restore audits

You can override these permissions by adding a policy with `audit` and `restoreAudit`.

### Event emitted

The `auditRestored` event is emitted when an audit is restored, so you could register a listener using the $listeners property to execute some extra code after the audit is restored.

E.g.: on Edit page of your resource:

```php
protected $listeners = [
    'auditRestored',
];

public function auditRestored()
{
    // your code
}
```

### Event listener

The audits relation manager listen to the `updateAuditsRelationManager` event to refresh the audits table.

So you can dispatch this event in the Edit page of your resource (e.g.: in a edit page of a `PostResource` -> `app/Filament/Resources/PostResource/Pages/EditPost.php`) when the form is updated:

```php
protected function afterSave(): void
{
    $this->dispatch('updateAuditsRelationManager');
}
```

> [!WARNING]
> When dispaching this event, set the [is_lazy](https://filamentphp.com/docs/3.x/panels/resources/relation-managers#disabling-lazy-loading) configuration to `false`, on `filament-auditing.php` 
> config file, to avoid this exception: "Typed property Filament\Resources\RelationManagers\RelationManager::$table
> must not be accessed before initialization"
