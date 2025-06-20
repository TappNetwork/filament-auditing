<?php

namespace Tapp\FilamentAuditing\Filament\Resources\Audits\Pages;

use Filament\Resources\Pages\ListRecords;
use Tapp\FilamentAuditing\Filament\Resources\Audits\AuditResource;

class ListAudits extends ListRecords
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
