<?php

/**
 * 🚀 SitePulse Analytics - Fix Migration Issue
 * Este script contorna o problema de "Array to string conversion" nas migrações
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 Iniciando correção do problema de migração...\n";

try {
    // Verificar se a tabela migrations existe
    if (!Schema::hasTable('migrations')) {
        echo "📋 Criando tabela migrations...\n";
        DB::statement('
            CREATE TABLE migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL
            )
        ');
        echo "✅ Tabela migrations criada!\n";
    }

    // Verificar se as migrações já foram executadas
    $migrations = DB::table('migrations')->pluck('migration')->toArray();
    
    $expectedMigrations = [
        '0001_01_01_000000_create_users_table',
        '0001_01_01_000001_create_cache_table',
        '0001_01_01_000002_create_jobs_table',
        '2024_01_01_000001_create_clients_table',
        '2024_01_01_000002_create_sites_table',
        '2024_01_01_000003_create_sessions_table',
        '2024_01_01_000004_create_pages_table',
        '2024_01_01_000005_create_visits_table',
        '2024_01_01_000006_create_events_table',
        '2024_01_01_000007_create_reviews_table',
        '2024_01_01_000008_create_surveys_table',
        '2024_01_01_000009_create_survey_responses_table',
        '2024_01_01_000010_create_metrics_table',
    ];

    $missingMigrations = array_diff($expectedMigrations, $migrations);
    
    if (empty($missingMigrations)) {
        echo "✅ Todas as migrações já foram executadas!\n";
    } else {
        echo "📝 Inserindo registros de migração faltantes...\n";
        $batch = (int) DB::table('migrations')->max('batch') + 1;
        
        foreach ($missingMigrations as $migration) {
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch
            ]);
            echo "✅ Migração {$migration} registrada!\n";
        }
    }

    // Verificar se todas as tabelas existem
    $requiredTables = [
        'users', 'clients', 'sites', 'analytics_sessions', 'pages', 
        'visits', 'events', 'reviews', 'surveys', 'survey_responses', 
        'metrics', 'cache', 'cache_locks', 'jobs', 'job_batches', 
        'failed_jobs', 'password_reset_tokens', 'laravel_sessions'
    ];

    $existingTables = [];
    foreach ($requiredTables as $table) {
        if (Schema::hasTable($table)) {
            $existingTables[] = $table;
        }
    }

    echo "📊 Tabelas existentes: " . count($existingTables) . "/" . count($requiredTables) . "\n";
    
    if (count($existingTables) === count($requiredTables)) {
        echo "🎉 Todas as tabelas estão criadas e funcionando!\n";
        echo "✅ Sistema pronto para uso!\n";
    } else {
        $missingTables = array_diff($requiredTables, $existingTables);
        echo "⚠️ Tabelas faltantes: " . implode(', ', $missingTables) . "\n";
        echo "💡 Execute: mysql -u root -p123 analytics < create-tables.sql\n";
    }

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "💡 Solução: Execute o SQL manual: mysql -u root -p123 analytics < create-tables.sql\n";
}

echo "\n🚀 Correção concluída!\n";

