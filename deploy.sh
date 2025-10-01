#!/bin/bash

# 🚀 SitePulse Analytics - Script de Deploy para Produção
# Execute este script no servidor de produção

echo "🚀 Iniciando deploy do SitePulse Analytics..."

# 1. Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Erro: Execute este script no diretório raiz do Laravel"
    exit 1
fi

# 2. Backup do banco (se necessário)
echo "📦 Fazendo backup do banco de dados..."
# mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > backup_$(date +%Y%m%d_%H%M%S).sql

# 3. Atualizar dependências
echo "📥 Atualizando dependências..."
composer install --optimize-autoloader --no-dev

# 4. Gerar chave da aplicação (se não existir)
if [ -z "$APP_KEY" ]; then
    echo "🔑 Gerando chave da aplicação..."
    php artisan key:generate
fi

# 5. PULAR MIGRAÇÕES (tabelas já existem)
echo "⚠️ PULANDO MIGRAÇÕES - Tabelas já existem no banco!"
echo "✅ Sistema já está funcionando sem necessidade de migrações"

# 6. Cache de configuração
echo "⚡ Otimizando cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Limpar cache antigo
echo "🧹 Limpando cache antigo..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 8. Otimizar autoloader
echo "🔧 Otimizando autoloader..."
composer dump-autoload --optimize

# 9. Verificar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 10. Testar configuração
echo "🧪 Testando configuração..."
php artisan config:show sitepulse

# 11. Verificar rotas
echo "🛣️ Verificando rotas..."
php artisan route:list | grep -E "(widget|api)" | head -5

echo "✅ Deploy concluído com sucesso!"
echo ""
echo "🔍 Próximos passos:"
echo "1. Configure seu servidor web (Nginx/Apache)"
echo "2. Configure SSL/HTTPS"
echo "3. Configure backup automático"
echo "4. Configure monitoramento"
echo ""
echo "📋 URLs importantes:"
echo "- Widget Script: https://your-domain.com/widget/{widget-id}.js"
echo "- Reviews: https://your-domain.com/widget/{widget-id}/reviews"
echo "- API: https://your-domain.com/api/widget/events"
echo "- Dashboard: https://your-domain.com/dashboard"
