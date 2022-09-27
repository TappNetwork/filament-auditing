<?php

return [

    'audits_sort' => [
        'column' => 'created_at',
        'direction' => 'desc',
    ],

    /**
     *  Extending Columns
     * --------------------------------------------------------------------------
     *  In case you need to add a column to the AuditsRelationManager that does
     *  not already exist in the table, you can add it here, and it will be
     *  prepended to the table builder.
     *
     *
     */
    'audits_extend' => [
        // eg. \Filament\Tables\Columns\TextColumn::make('url'),
    ]

];
