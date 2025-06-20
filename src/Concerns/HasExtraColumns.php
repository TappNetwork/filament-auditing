<?php

namespace Tapp\FilamentAuditing\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasExtraColumns
{
    protected static function extraColumns()
    {
        return Arr::map(config('filament-auditing.audits_extend'), function ($buildParameters, $columnName) {
            return collect($buildParameters)->pipeThrough([
                function ($collection) use ($columnName) {
                    $columnClass = (string) $collection->get('class');

                    if (! is_null($collection->get('methods'))) {
                        $columnClass = $columnClass::make($columnName);

                        collect($collection->get('methods'))->transform(function ($value, $key) use ($columnClass) {
                            if (is_numeric($key)) {
                                return $columnClass->$value();
                            }

                            return $columnClass->$key(...Arr::wrap($value));
                        });

                        return $columnClass;
                    }

                    return $columnClass::make($columnName);
                },
            ]);
        });
    }
}
