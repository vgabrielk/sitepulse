<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Connection;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Fix para o erro "Array to string conversion" no Builder.php:163
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
}
