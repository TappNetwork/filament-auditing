# Filament Laravel Auditing

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tapp/filament-auditing.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-auditing)
![Code Style Action Status](https://github.com/TappNetwork/filament-auditing/actions/workflows/fix-php-code-style-issues.yml/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/tapp/filament-auditing.svg?style=flat-square)](https://packagist.org/packages/tapp/filament-auditing)

A Filament plugin for [Laravel Auditing](https://laravel-auditing.com/) package.
This plugin contains a relation manager for audits that you can add to your Filament resources.

This package provides a Filament resource manager that shows a table with all audits on view and edit pages and allows
restore audits.

## Version Compatibility

 Filament | Filament Auditing
:---------|:-----------------
 2.x      | 2.x
 3.x      | 3.x
 4.x      | 4.x

## Installation

> [!IMPORTANT]
> Please check the **Filament Auditing** plugin version you should use in the **Version Compatibility** table above.

> **Note**
> This plugin uses the [Laravel Auditing](https://laravel-auditing.com/) package. First install and configure this
> package.

You can install the plugin via Composer.

### For Filament 3

```bash
composer require tapp/filament-auditing:"^3.0"
```

### For Filament 4

Please visit the **[4.x](https://github.com//TappNetwork/filament-auditing/tree/4.x)** branch for a detailed documention of the new features available in version 4.

```bash
composer require tapp/filament-auditing:"^4.0"
```

### For Filament 2

Please visit the **[2.x](https://github.com//TappNetwork/filament-auditing/tree/2.x)** branch for a detailed documention about version 2.

```bash
composer require tapp/filament-auditing:"^2.0"
```

You can publish the view files with:

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

    'custom_audits_view' => false,

    'custom_view_parameters' => [
    ],

    'mapping' => [
    ],
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

Methods with two or more parameters can be specified with an array like so:

```php
<?php

return [

    'audits_extend' => [
       'created_at' => [
           'class' => \Filament\Tables\Columns\TextColumn::class, // required
           'methods' => [
               'sortable',
               'date' => ['Y-m-d H:i:s', 'America/New_York'],
            ],
        ],
    ]

];
```

### Custom View Data Formatting

If you want to modify the content of the audit to display the old and new values in a specific way, such as showing the value of a specific column instead of the ID for relationships, or even customize the entire view to display data in a different way than the default table, you can use one of these methods described below (first, make sure the plugin views are published):

#### Show a related column instead of foreign id

To use another field to be displayed for relationships instead of the foreign id, in old and new values, you can add on the `mapping` array, in `filament-auditing.php` config file, the label and field that should be displayed, as well as the related model, using the foreign key as the array key. For example, on an `user` relationship with the `user_id` foreing key, this config will display the user `name` along with the `User` label:

```bash
'mapping' => [
        'user_id' => [
            'model' => App\Models\User::class,
            'field' => 'name',
            'label' => 'User',
        ],
    ],
```

And you'd like to customize the view, you can do it in the published view `views/vendor/filament-auditing/tables/columns/key-value.blade.php` file.

#### Customizing the Old and New Values

If you need to customize the presentation for other old and new values, besides the related fields, you can add a `formatAuditFieldsForPresentation($field, $record)` method on the model that is auditable, with two parameters:
- the first parameter contains the name of the field (`old_values` or `new_values`).
- the second parameter contains de current audit record

This method must return the formatted audit fields.

For example, let's say you have an `Article` model that is auditable and contains a related user, and you added a `formatAuditFieldsForPresentation($field, $record)` method that returns the related user name instead of the id, and the data formatted with some HTML code:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Audit;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class Article extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    // ...

    public function formatAuditFieldsForPresentation($field, Audit $record)
    {
        $fields = Arr::wrap($record->{$field});

        $formattedResult = '<ul>';

        foreach ($fields as $key => $value) {
            $formattedResult .= '<li>';
            $formattedResult .= match ($key) {
                'user_id' => '<strong>User</strong>: '.User::find($record->{$field}['user_id'])?->name.'<br />',
                'title' => '<strong>Title</strong>: '.(string) str($record->{$field}['title'])->title().'<br />',
                'order' => '<strong>Order</strong>: '.$record->{$field}['order'].'<br />',
                'content' => '<strong>Content</strong>: '.$record->{$field}['content'].'<br />',
                default => ' - ',
            };
            $formattedResult .= '</li>';
        }

        $formattedResult .= '</ul>';

        return new HtmlString($formattedResult);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

#### Customizing the Entire View Content

If you'd like to customize the entire view content, you may set the `custom_audits_view` config value to `true` on `config/filament-auditing.php` file:

```php
'custom_audits_view' => true,
```

This modification will allow you to take full control of the display and tailor it to your specific requirements. You can now add your custom content on `resources/views/vendor/filament-auditing/tables/custom-audit-content.blade.php` file. 
For example:

```php
@php
$type = (string) str(class_basename($owner))->lower();
@endphp

@if(isset($records))
    <x-filament-tables::table>
        <x-slot name="header">
            @foreach($headers as $header)
            <x-filament-tables::header-cell>
                {{$header}}
            </x-filament-tables::header-cell>
            @endforeach
        </x-slot>
        @foreach($records as $audit)
            <x-filament-tables::row>
                @foreach ($audit->getModified() as $attribute => $modified)
                    <x-filament-tables::cell>
                        @lang($type.'.metadata', $audit->getMetadata())
                        <br />
                        @php
                            $current = $type.'.'.$audit->event.'.modified.'.$attribute;

                            $modified['new'] = $owner->formatFieldForPresentation($attribute, $modified['new']);

                            if (isset($modified['old'])) {
                                $modified['old'] = $owner->formatFieldForPresentation($attribute, $modified['old']);
                            }
                        @endphp

                        @lang($current, $modified)
                    </x-filament-tables::cell>
                @endforeach
            </x-filament-tables::row>
        @endforeach
    </x-filament-tables::table>
@else
    <div class="flex items-center justify-center h-32 text-gray-500 dark:text-gray-400">
        @lang($type.'.unavailable_audits')
    </div>
@endif
```

The owner record is available to this view via `$owner` variable. To pass some additional parameters to the view, you may use the `custom_view_parameters` config:

```php
'custom_view_parameters' => [
    'headers' => [
        'Audit',
    ],
],
```

To format a field, you may also add a `formatFieldForPresentation` method on the owner model, with the field name and value as parameters, like in the example above. This method must return a formatted field.

For example, in an `Article` model, to return the name of the related user:

```php
public function formatFieldForPresentation($field, $value)
{
    return match($field) {
        'user_id' => $value ? optional(User::find($value))->name : $value,
        default => $value,
    };
}
```

An example of the `article.php` lang file content used in the `custom-audit-content.blade.php` view code above:

```php
<?php

return [
    'unavailable_audits' => 'No article audits available',

    'metadata' => 'On :audit_created_at, :user_name [:audit_ip_address] :audit_event this record via :audit_url',

    'updated' => [
        'modified' => [
            'order' => 'The Order has been modified from <strong>:old</strong> to <strong>:new</strong>',
            'title' => 'The Title has been modified from <strong>:old</strong> to <strong>:new</strong>',
            'content' => 'The Content has been modified from <strong>:old</strong> to <strong>:new</strong>',
            'user_id' => 'The User has been modified from <strong>:old</strong> to <strong>:new</strong>',
        ],
    ],
];
```

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
