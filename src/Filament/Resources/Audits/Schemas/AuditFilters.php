<?php

namespace Tapp\FilamentAuditing\Filament\Resources\Audits\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class AuditFilters
{
    public static function configure(): array
    {
        return [
            Filter::make('created_at')
                ->schema([
                    Fieldset::make(trans('filament-auditing::filament-auditing.filter.created_at'))
                        ->schema([
                            DatePicker::make('created_from')
                                ->label(trans('filament-auditing::filament-auditing.filter.created_from'))
                                ->columnSpanFull(),
                            DatePicker::make('created_until')
                                ->label(trans('filament-auditing::filament-auditing.filter.created_until'))
                                ->columnSpanFull(),
                        ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['created_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
        ];
    }
}
