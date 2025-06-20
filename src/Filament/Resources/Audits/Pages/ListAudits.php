<?php

namespace Tapp\FilamentAuditing\Filament\Resources\Audits\Pages;

use Tapp\FilamentAuditing\Filament\Resources\Audits\AuditResource;
use Filament\Resources\Pages\ListRecords;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
