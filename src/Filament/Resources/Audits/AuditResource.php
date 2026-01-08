<?php

namespace Tapp\FilamentAuditing\Filament\Resources\Audits;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Pages\ListAudits;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Pages\ViewAudit;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Schemas\AuditInfolist;
use Tapp\FilamentAuditing\Filament\Resources\Audits\Tables\AuditsTable;
use Tapp\FilamentAuditing\Models\Audit;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    /**
     * Check if this resource should be scoped to a tenant.
     * This is called by Filament to determine if tenant scoping should be applied.
     */
    public static function isScopedToTenant(): bool
    {
        return config('filament-auditing.tenancy.enabled', false) 
            && config('filament-auditing.tenancy.model') !== null;
    }

    /**
     * Get the tenant ownership relationship name.
     * This tells Filament which relationship to use for tenant scoping.
     */
    public static function getTenantOwnershipRelationshipName(): string
    {
        if (! config('filament-auditing.tenancy.enabled') || ! config('filament-auditing.tenancy.model')) {
            return 'tenant';
        }

        // Use the relationship name from config, or auto-detect from tenant model
        $relationshipName = config('filament-auditing.tenancy.relationship_name');
        if ($relationshipName) {
            return $relationshipName;
        }

        $tenantModel = config('filament-auditing.tenancy.model');

        return \Illuminate\Support\Str::snake(class_basename($tenantModel));
    }

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
