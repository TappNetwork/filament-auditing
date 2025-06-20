<?php

namespace Tapp\FilamentAuditing\Filament\Tables\Columns;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\Concerns\CanFormatState;

class AuditValuesColumn extends Column
{
    use CanFormatState;

    protected string $view = 'filament-auditing::tables.columns.audit-values-column';
}
