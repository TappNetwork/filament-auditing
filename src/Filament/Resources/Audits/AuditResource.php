<?php

namespace Tapp\FilamentAuditing\Filament\Resources\Audits;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Models\Audit;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Pages\ListAudits;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Pages\ViewAudit;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Schemas\AuditInfolist;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Tables\AuditsTable;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function infolist(Schema $schema): Schema
    {
        return AuditInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAudits::route('/'),
            'view' => ViewAudit::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
