<?php

namespace Tapp\FilamentAuditing\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;

class ManageRelatedAuditRecords extends ManageRelatedRecords
{
    use Tapp\FilamentAuditing\Concerns\HasAuditsTable;
}
