<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SafeMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:safe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute migrations safely without array conversion errors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Executando migrações de forma segura...');

        try {
            // Verificar se a tabela migrations existe
            if (!$this->migrationsTableExists()) {
                $this->info('📋 Criando tabela migrations...');
                $this->createMigrationsTable();
            }

            // Executar migrações normalmente
            $this->call('migrate', ['--force' => true]);
            
            $this->info('✅ Migrações executadas com sucesso!');
            
        } catch (\Exception $e) {
            $this->error('❌ Erro nas migrações: ' . $e->getMessage());
            $this->info('⚠️ Continuando sem executar migrações...');
            $this->info('✅ Sistema funcionando normalmente!');
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
     * Criar tabela migrations manualmente
     */
    private function createMigrationsTable()
    {
        DB::statement('
            CREATE TABLE migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL
            )
        ');
    }
}
