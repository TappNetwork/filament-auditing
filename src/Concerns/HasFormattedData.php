<?php

namespace Tapp\FilamentAuditing\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasFormattedData
{
    public function formatData(Model $record, ?string $name = null, ?array $state = null): mixed
    {
        if (method_exists($this->getOwnerRecord(), 'formatAuditFieldsForPresentation')) {
            return $this->getOwnerRecord()->formatAuditFieldsForPresentation($name, $record);
        }

        return view('filament-auditing::tables.columns.key-value', ['data' => $this->mapRelatedColumns($state, $record)]);
    }

    protected function mapRelatedColumns($state, $record)
    {
        $relationshipsToUpdate = Arr::wrap(config('filament-auditing.mapping'));

        if (count($relationshipsToUpdate) !== 0) {
            foreach ($relationshipsToUpdate as $key => $relationship) {
                if (array_key_exists($key, $state)) {
                    $state[$relationship['label']] = $relationship['model']::find($state[$key])?->{$relationship['field']};
                    unset($state[$key]);
                }
            }
        }

        return $state;
    }
}
