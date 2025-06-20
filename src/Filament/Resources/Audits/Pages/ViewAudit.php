<?php

namespace Tapp\FilamentAuditing\Filament\Resources\Audits\Pages;

use Filament\Resources\Pages\ViewRecord;
use Tapp\FilamentAuditing\Filament\Resources\Audits\AuditResource;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
