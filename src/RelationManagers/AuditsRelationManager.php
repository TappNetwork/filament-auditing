<?php

namespace Tapp\FilamentAuditing\RelationManagers;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use OwenIt\Auditing\Contracts\Audit;

class AuditsRelationManager extends RelationManager
{
    use \Tapp\FilamentAuditing\Concerns\HasAuditsTable;

    protected static string $relationship = 'audits';

    protected static ?string $recordTitleAttribute = 'id';

    protected $listeners = ['updateAuditsRelationManager' => '$refresh'];

    public static function isLazy(): bool
    {
        return config('filament-auditing.is_lazy');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Filament::auth()->user()->can('audit', $ownerRecord);
    }

    protected static function customViewParameters(): array
    {
        return config('filament-auditing.custom_view_parameters');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-auditing::filament-auditing.table.heading');
    }
}
