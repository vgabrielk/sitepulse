#!/bin/bash

# 🚀 SitePulse Analytics - Deploy SEM MIGRAÇÕES
# Use este script se sua plataforma de deploy executa migrações automaticamente

echo "🚀 Iniciando deploy do SitePulse Analytics (SEM MIGRAÇÕES)..."

# 1. Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do Laravel"
    exit 1
fi

# 2. Atualizar dependências
echo "📥 Atualizando dependências..."
composer install --optimize-autoloader --no-dev

# 3. Gerar chave da aplicação (se não existir)
if [ -z "$APP_KEY" ]; then
    echo "🔑 Gerando chave da aplicação..."
    php artisan key:generate
fi

# 4. ⚠️ PULAR MIGRAÇÕES - Tabelas já existem!
echo "⚠️ PULANDO MIGRAÇÕES - Tabelas já existem no banco!"
echo "✅ Sistema já está funcionando sem necessidade de migrações"

# 5. Cache de configuração
echo "⚡ Otimizando cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Limpar cache antigo
echo "🧹 Limpando cache antigo..."
php artisan cache:clear

# 7. Otimizar autoloader
echo "🔧 Otimizando autoloader..."
composer dump-autoload --optimize

# 8. Verificar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 storage bootstrap/cache

# 9. Testar configuração
echo "🧪 Testando configuração..."
php artisan config:show sitepulse

# 10. Verificar rotas
echo "🛣️ Verificando rotas..."
php artisan route:list | grep -E "(widget|api)" | head -5

echo "✅ Deploy concluído com sucesso (SEM MIGRAÇÕES)!"
echo ""
echo "🎉 Sistema funcionando sem executar migrações!"
echo "📋 URLs importantes:"
echo "- Widget Script: https://your-domain.com/widget/{widget-id}.js"
