# SitePulse Analytics - Guia de Instalação

## 🚀 Instalação Rápida

### Pré-requisitos
- PHP 8.1+
- Laravel 10+
- MySQL 8.0+
- Composer
- Node.js (para assets)

### 1. Clone e Instale Dependências
```bash
git clone <repository-url>
cd analytics
composer install
npm install
```

### 2. Configure o Ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configure o Banco de Dados
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sitepulse
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Execute as Migrations
```bash
php artisan migrate
```

### 5. Compile os Assets
```bash
npm run build
```

### 6. Inicie o Servidor
```bash
php artisan serve
```

## 🔧 Configuração Avançada

### Variáveis de Ambiente Importantes
```env
# SitePulse specific
SITEPULSE_WIDGET_URL=http://localhost:8000/widget
SITEPULSE_ADMIN_EMAIL=admin@sitepulse.com
SITEPULSE_RATE_LIMIT_PER_MINUTE=60
SITEPULSE_ANONYMIZE_IPS=true
SITEPULSE_WEBHOOK_SECRET=your-webhook-secret-here
SITEPULSE_REQUIRE_HTTPS=false
```

### Configuração de Cache (Redis)
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Configuração de Queue
```env
QUEUE_CONNECTION=redis
```

## 📊 Uso da API

### Autenticação
Todas as rotas protegidas requerem uma API key:
```
X-API-Key: your-api-key-here
```

### Endpoints Principais

#### Clientes
- `POST /api/auth/register` - Registrar novo cliente
- `POST /api/auth/login` - Autenticar com API key
- `GET /api/auth/me` - Dados do cliente atual

#### Sites
- `GET /api/sites` - Listar sites do cliente
- `POST /api/sites` - Criar novo site
- `GET /api/sites/{id}/widget-code` - Código do widget

#### Analytics
- `GET /api/analytics/sites/{id}/overview` - Visão geral
- `GET /api/analytics/sites/{id}/sessions` - Sessões
- `GET /api/analytics/sites/{id}/events` - Eventos

#### Reviews
- `GET /api/reviews/sites/{id}` - Listar reviews
- `POST /api/reviews/{id}/approve` - Aprovar review
- `POST /api/reviews/{id}/reject` - Rejeitar review

## 🎯 Widget Embed

### Integração Básica
```html
<!-- SitePulse Analytics -->
<script async src="http://localhost:8000/widget/{widget-id}.js"></script>
<!-- End SitePulse Analytics -->
```

### Configuração Avançada
```javascript
window.SitePulse = {
    config: {
        siteId: 'your-site-id',
        tracking: {
            pageviews: true,
            events: true,
            scroll: true,
            clicks: true,
            forms: true
        },
        widget: {
            position: 'bottom-right',
            theme: 'light',
            colors: {
                primary: '#007bff',
                background: '#ffffff',
                text: '#333333'
            },
            showCounter: true,
            showFeedback: true
        }
    }
};
```

## 🔒 Segurança

### Rate Limiting
- API: 60 requests/minuto
- Widget: 100 requests/minuto
- Login: 5 tentativas/minuto
- Registro: 3 tentativas/hora

### Privacidade
- Anonimização de IPs (configurável)
- Respeita "Do Not Track"
- Retenção de dados por plano
- Compliance GDPR/CCPA

## 📈 Planos

### Free
- 1,000 visitas/mês
- 5,000 eventos/mês
- 1 site
- 50 reviews/mês
- 0 exportações

### Basic ($9.99/mês)
- 10,000 visitas/mês
- 50,000 eventos/mês
- 3 sites
- 500 reviews/mês
- 10 exportações/mês

### Premium ($29.99/mês)
- 100,000 visitas/mês
- 500,000 eventos/mês
- 10 sites
- 5,000 reviews/mês
- 100 exportações/mês

### Enterprise ($99.99/mês)
- Ilimitado
- Suporte prioritário
- Integrações customizadas

## 🚀 Deploy

### Produção
1. Configure servidor web (Nginx/Apache)
2. Configure SSL para HTTPS
3. Configure cache (Redis/Memcached)
4. Configure queue workers
5. Configure monitoring e logs

### Docker (Opcional)
```dockerfile
FROM php:8.1-fpm
# ... configuração do Docker
```

## 📊 Monitoramento

### Logs
- Laravel logs para debugging
- Audit logs para segurança
- Webhook logs para integrações

### Métricas
- Dashboard administrativo
- Alertas configuráveis
- Health checks

## 🤝 Suporte

- **Email**: support@sitepulse.com
- **Documentação**: [docs.sitepulse.com](https://docs.sitepulse.com)
- **Issues**: [GitHub Issues](https://github.com/sitepulse/analytics/issues)

---

**SitePulse Analytics** - Transformando dados em insights valiosos para seu negócio.
