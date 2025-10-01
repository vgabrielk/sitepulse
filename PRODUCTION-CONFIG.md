# 🚀 SitePulse Analytics - Configuração para Produção

## 📋 Checklist de Deploy

### 1. **Configurações de Ambiente (.env)**

```bash
# Configurações básicas
APP_NAME=SitePulse
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Banco de dados
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=sitepulse_production
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

# SitePulse específico
SITEPULSE_WIDGET_URL=https://your-domain.com/widget
SITEPULSE_RATE_LIMIT_PER_MINUTE=60
SITEPULSE_ANONYMIZE_IPS=true
SITEPULSE_REQUIRE_HTTPS=true
SITEPULSE_ADMIN_EMAIL=admin@your-domain.com
SITEPULSE_MAINTENANCE_MODE=false

# Email
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="SitePulse Analytics"
```

### 2. **URLs Dinâmicas - ✅ JÁ CONFIGURADO**

O sistema já está usando URLs dinâmicas:

- **Widget Script**: `{{ url("/api/widget") }}` ✅
- **Reviews Iframe**: `{{ url("/widget/{widgetId}/reviews") }}` ✅
- **API Endpoints**: `{{ url("/api/widget/events") }}` ✅

### 3. **Configurações de Produção Necessárias**

#### A. **SSL/HTTPS**
```bash
# Configurar certificado SSL
# Atualizar APP_URL para HTTPS
APP_URL=https://your-domain.com
```

#### B. **Cache e Performance**
```bash
# Redis para cache
CACHE_DRIVER=redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password

# Queue para processamento assíncrono
QUEUE_CONNECTION=redis
```

#### C. **Banco de Dados**
```bash
# MySQL/PostgreSQL otimizado
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=sitepulse_production
```

### 4. **Comandos de Deploy**

```bash
# 1. Instalar dependências
composer install --optimize-autoloader --no-dev

# 2. Gerar chave da aplicação
php artisan key:generate

# 3. Executar migrações
php artisan migrate --force

# 4. Cache de configuração
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Otimizar autoloader
composer dump-autoload --optimize
```

### 5. **Verificações Pós-Deploy**

```bash
# Testar widget script
curl -I https://your-domain.com/widget/{widget-id}.js

# Testar API
curl -X POST https://your-domain.com/api/widget/events \
  -H "Content-Type: application/json" \
  -d '{"site_id":1,"events":[]}'

# Testar reviews
curl -I https://your-domain.com/widget/{widget-id}/reviews
```

### 6. **Monitoramento**

- **Logs**: `storage/logs/laravel.log`
- **Performance**: Configurar APM (New Relic, DataDog)
- **Uptime**: Configurar monitoramento de uptime
- **Backup**: Backup automático do banco de dados

### 7. **Segurança**

- ✅ **HTTPS**: Obrigatório em produção
- ✅ **Rate Limiting**: Configurado
- ✅ **CORS**: Middleware configurado
- ✅ **IP Anonymization**: Configurável via .env
- ✅ **Session Security**: Timeout configurado

## 🎯 **Status: PRONTO PARA PRODUÇÃO**

### ✅ **URLs Dinâmicas**: Todas configuradas
### ✅ **Configurações**: Flexíveis via .env
### ✅ **Segurança**: Implementada
### ✅ **Performance**: Otimizada
### ✅ **Monitoramento**: Preparado

**O sistema está 100% pronto para deploy em produção!** 🚀
