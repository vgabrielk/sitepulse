<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrationStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:status-fixed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show migration status without array conversion errors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Verificando status das migrações...');

        try {
            // Verificar se a tabela migrations existe
            if (!$this->migrationsTableExists()) {
                $this->warn('⚠️ Tabela migrations não existe!');
                $this->info('💡 Execute: php fix-migration-issue.php');
                return;
            }

            // Obter migrações executadas
            $executedMigrations = DB::table('migrations')
                ->orderBy('batch')
                ->orderBy('migration')
                ->get();

            // Obter arquivos de migração
            $migrationFiles = $this->getMigrationFiles();

            $this->info("\n📋 Status das Migrações:");
            $this->info("========================\n");

            $executed = $executedMigrations->pluck('migration')->toArray();
            $pending = array_diff($migrationFiles, $executed);

            // Mostrar migrações executadas
            if (!empty($executed)) {
                $this->info("✅ Migrações Executadas:");
                foreach ($executed as $migration) {
                    $this->line("  - {$migration}");
                }
                $this->line("");
            }

            // Mostrar migrações pendentes
            if (!empty($pending)) {
                $this->warn("⏳ Migrações Pendentes:");
                foreach ($pending as $migration) {
                    $this->line("  - {$migration}");
                }
                $this->line("");
            }

            // Resumo
            $this->info("📊 Resumo:");
            $this->info("  - Executadas: " . count($executed));
            $this->info("  - Pendentes: " . count($pending));
            $this->info("  - Total: " . count($migrationFiles));

            if (empty($pending)) {
                $this->info("\n🎉 Todas as migrações foram executadas!");
            } else {
                $this->warn("\n⚠️ Existem migrações pendentes!");
                $this->info("💡 Execute: php artisan migrate --force");
            }

        } catch (\Exception $e) {
            $this->error("❌ Erro: " . $e->getMessage());
            $this->info("💡 Solução: Execute php fix-migration-issue.php");
        }
    }

    /**
     * Verificar se a tabela migrations existe
     */
    private function migrationsTableExists(): bool
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $tableName = DB::getDatabaseName() . '.migrations';
            
            foreach ($tables as $table) {
                $tableArray = (array) $table;
                if (in_array('migrations', $tableArray)) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obter arquivos de migração
     */
    private function getMigrationFiles(): array
    {
        $migrationPath = database_path('migrations');
        $files = glob($migrationPath . '/*.php');
        
        $migrations = [];
        foreach ($files as $file) {
            $filename = basename($file);
            $migration = str_replace('.php', '', $filename);
            $migrations[] = $migration;
        }
        
        sort($migrations);
        return $migrations;
    }
}
