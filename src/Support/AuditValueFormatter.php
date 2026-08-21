<?php

namespace Tapp\FilamentAuditing\Support;

class AuditValueFormatter
{
    /**
     * Format a nested audit value for display.
     *
     * Related-model snapshots may store `['id' => 1]` maps, while many
     * attributes store a plain list of IDs such as `[1, 2, 3]`.
     */
    public static function nested(mixed $value): string
    {
        if (is_array($value)) {
            if (array_key_exists('id', $value)) {
                return static::nested($value['id']);
            }

            $encoded = json_encode($value);

            return $encoded === false ? '' : $encoded;
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        $encoded = json_encode($value);

        return $encoded === false ? '' : $encoded;
    }
}
