<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Connection;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Override the hasTable method to fix the array to string conversion error
        Builder::macro('hasTableFixed', function ($table) {
            $table = is_array($table) ? $table[0] : $table;
            $prefix = $this->connection->getTablePrefix();
            $prefix = is_array($prefix) ? '' : $prefix;
            $table = $prefix . $table;

            foreach ($this->getTables(false) as $value) {
                if (strtolower($table) === strtolower($value['name'])) {
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
        // Override the original hasTable method
        Builder::macro('hasTable', function ($table) {
            return $this->hasTableFixed($table);
        });
    }
}
