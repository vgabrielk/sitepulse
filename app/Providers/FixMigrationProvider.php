<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Connection;

class FixMigrationProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Override the hasTable method to fix the array to string conversion
        Builder::macro('hasTable', function ($table) {
            // Ensure $table is a string
            $table = is_array($table) ? $table[0] : (string) $table;
            
            // Get prefix and ensure it's a string
            $prefix = $this->connection->getTablePrefix();
            $prefix = is_array($prefix) ? '' : (string) $prefix;
            
            // Concatenate prefix and table
            $fullTableName = $prefix . $table;

            // Get tables and check if our table exists
            foreach ($this->getTables(false) as $value) {
                $tableName = is_array($value) ? $value['name'] : $value;
                if (strtolower($fullTableName) === strtolower($tableName)) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
