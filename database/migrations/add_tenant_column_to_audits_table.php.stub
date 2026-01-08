<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run if tenancy is enabled
        if (! config('filament-auditing.tenancy.enabled')) {
            return;
        }

        // Validate tenant configuration
        $tenantModel = config('filament-auditing.tenancy.model');
        
        throw_unless(
            $tenantModel,
            new \InvalidArgumentException(
                'Tenancy is enabled but no tenant model is configured. '.
                'Please set "filament-auditing.tenancy.model" in config/filament-auditing.php '.
                'to your tenant model class (e.g., App\Models\Team::class), '.
                'or disable tenancy by setting "filament-auditing.tenancy.enabled" to false.'
            )
        );

        $connection = config('audit.drivers.database.connection', config('database.default'));
        $tableName = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->table($tableName, function (Blueprint $table) use ($connection, $tableName): void {
            $tenantModel = config('filament-auditing.tenancy.model');
            $tenantColumn = config('filament-auditing.tenancy.column');

            // Auto-detect column name if not configured
            if (! $tenantColumn) {
                $relationshipName = config('filament-auditing.tenancy.relationship_name');
                if (! $relationshipName) {
                    $relationshipName = \Illuminate\Support\Str::snake(class_basename($tenantModel));
                }
                $tenantColumn = $relationshipName.'_id';
            }

            // Check if column already exists
            if (! Schema::connection($connection)->hasColumn($tableName, $tenantColumn)) {
                // Add column after id column
                $table->foreignIdFor($tenantModel, $tenantColumn)
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! config('filament-auditing.tenancy.enabled')) {
            return;
        }

        // Validate tenant configuration
        $tenantModel = config('filament-auditing.tenancy.model');
        
        throw_unless(
            $tenantModel,
            new \InvalidArgumentException(
                'Tenancy is enabled but no tenant model is configured. '.
                'Please set "filament-auditing.tenancy.model" in config/filament-auditing.php '.
                'to your tenant model class (e.g., App\Models\Team::class), '.
                'or disable tenancy by setting "filament-auditing.tenancy.enabled" to false.'
            )
        );

        $connection = config('audit.drivers.database.connection', config('database.default'));
        $tableName = config('audit.drivers.database.table', 'audits');

        Schema::connection($connection)->table($tableName, function (Blueprint $table) use ($connection, $tableName): void {
            $tenantColumn = config('filament-auditing.tenancy.column');

            // Auto-detect column name if not configured
            if (! $tenantColumn) {
                $tenantModel = config('filament-auditing.tenancy.model');
                $relationshipName = config('filament-auditing.tenancy.relationship_name');
                if (! $relationshipName) {
                    $relationshipName = \Illuminate\Support\Str::snake(class_basename($tenantModel));
                }
                $tenantColumn = $relationshipName.'_id';
            }

            if ($tenantColumn && Schema::connection($connection)->hasColumn($tableName, $tenantColumn)) {
                $table->dropForeign([$tenantColumn]);
                $table->dropColumn($tenantColumn);
            }
        });
    }
};
