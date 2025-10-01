<?php

/**
 * 🚀 SitePulse Analytics - Fix Production Database
 * Este script resolve problemas de banco de dados em produção
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🚀 Corrigindo banco de dados em produção...\n";

try {
    // Verificar conexão com banco
    echo "📡 Testando conexão com banco...\n";
    $database = DB::getDatabaseName();
    $connection = DB::connection()->getName();
    echo "✅ Conectado ao banco: {$database} (conexão: {$connection})\n";

    // Verificar se a tabela users existe
    if (!Schema::hasTable('users')) {
        echo "❌ Tabela 'users' não existe!\n";
        echo "🔧 Criando tabela users...\n";
        
        DB::statement('
            CREATE TABLE users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                email_verified_at TIMESTAMP NULL,
                password VARCHAR(255) NOT NULL,
                remember_token VARCHAR(100) NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL
            )
        ');
        echo "✅ Tabela users criada!\n";
    } else {
        echo "✅ Tabela users existe!\n";
    }

    // Verificar se há usuários
    $userCount = DB::table('users')->count();
    echo "👥 Usuários no banco: {$userCount}\n";

    // Se não há usuários, criar um admin padrão
    if ($userCount === 0) {
        echo "🔧 Criando usuário admin padrão...\n";
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@sitepulse.com',
            'password' => bcrypt('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Usuário admin criado: admin@sitepulse.com / admin123\n";
    }

    // Verificar outras tabelas essenciais
    $essentialTables = ['clients', 'sites', 'analytics_sessions'];
    $missingTables = [];

    foreach ($essentialTables as $table) {
        if (!Schema::hasTable($table)) {
            $missingTables[] = $table;
        }
    }

    if (!empty($missingTables)) {
        echo "⚠️ Tabelas faltantes: " . implode(', ', $missingTables) . "\n";
        echo "💡 Execute o SQL completo para criar todas as tabelas\n";
    } else {
        echo "✅ Todas as tabelas essenciais existem!\n";
    }

    // Testar uma query simples
    echo "🧪 Testando query de usuários...\n";
    $users = DB::table('users')->select('id', 'name', 'email')->get();
    echo "✅ Query funcionando! Usuários encontrados: " . $users->count() . "\n";

    echo "\n🎉 Banco de dados em produção corrigido!\n";
    echo "✅ Sistema pronto para uso!\n";

} catch (Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "💡 Verifique a configuração do banco de dados\n";
    echo "💡 Verifique se o banco existe e está acessível\n";
}

