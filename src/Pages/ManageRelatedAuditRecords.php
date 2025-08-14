<?php

namespace Tapp\FilamentAuditing\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;

class ManageRelatedAuditRecords extends ManageRelatedRecords
{
    use Tapp\FilamentAuditing\Concerns\HasAuditsTable;

    protected static string $relationship = 'audits';

    public function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-auditing::filament-auditing.table.heading');
    }
}
