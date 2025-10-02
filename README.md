# SitePulse Widgets - Micro-SaaS Platform

SitePulse é uma plataforma de widgets de feedback para websites, desenvolvida como um micro-SaaS multi-tenant. O sistema permite que clientes coletem avaliações e exibam widgets em seus sites.

## 🚀 Funcionalidades

### Para Clientes
- **Widget Embed**: Script JavaScript leve para integração em qualquer site
- **Widgets de Reviews**: Coleta e exibição de avaliações
- **Feedback Collection**: Sistema de reviews e testemunhos
- **Dashboard Completo**: Métricas detalhadas e relatórios
- **Customização**: Cores, posicionamento e configurações do widget
- **Exportação de Dados**: CSV, Excel e JSON

### Para Administradores
- **Gestão Multi-tenant**: Controle de todos os clientes e sites
- **Sistema de Planos**: Free, Basic, Premium e Enterprise
- **Monitoramento do Sistema**: Métricas agregadas de uso
- **Controle de Limites**: Rate limiting e quotas por plano

## 🏗️ Arquitetura

O sistema foi desenvolvido seguindo **Clean Architecture** com separação clara de responsabilidades:

- **Controllers**: Recebem requests e chamam Services
- **Services**: Contêm toda a lógica de negócio
- **Repositories**: Acesso a banco de dados e queries
- **DTOs**: Transferência de dados entre camadas
- **Models**: Representam entidades do banco de dados

## 📋 Pré-requisitos

- PHP 8.1+
- Laravel 10+
- MySQL 8.0+
- Composer
- Node.js (para assets)

## 🛠️ Instalação

1. **Clone o repositório**
```bash
git clone <repository-url>
cd analytics
```

2. **Instale as dependências**
```bash
composer install
npm install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sitepulse
DB_USERNAME=root
DB_PASSWORD=
```

5. **Execute as migrations**
```bash
php artisan migrate
```

6. **Execute os seeders (opcional)**
```bash
php artisan db:seed
```

7. **Compile os assets**
```bash
npm run build
```

8. **Inicie o servidor**
```bash
php artisan serve
```

## 🔧 Configuração

### Variáveis de Ambiente Importantes

```env
# SitePulse specific
SITEPULSE_WIDGET_URL=http://localhost:8000/widget
SITEPULSE_ADMIN_EMAIL=admin@sitepulse.com
SITEPULSE_RATE_LIMIT_PER_MINUTE=60
SITEPULSE_ANONYMIZE_IPS=true
SITEPULSE_WEBHOOK_SECRET=your-webhook-secret-here
```

## 📚 Uso da API

### Autenticação

Todas as rotas protegidas requerem uma API key no header:
```
X-API-Key: your-api-key-here
```

### Endpoints Principais

#### Clientes
- `POST /api/auth/register` - Registrar novo cliente
- `POST /api/auth/login` - Autenticar com API key
- `GET /api/auth/me` - Dados do cliente atual
- `PUT /api/auth/profile` - Atualizar perfil

#### Sites
- `GET /api/sites` - Listar sites do cliente
- `POST /api/sites` - Criar novo site
- `GET /api/sites/{id}` - Detalhes do site
- `PUT /api/sites/{id}` - Atualizar site
- `DELETE /api/sites/{id}` - Deletar site
- `GET /api/sites/{id}/widget-code` - Código do widget

#### Widgets
Rotas públicas e APIs para script do widget, reviews e configuração.

#### Reviews
- `GET /api/reviews/sites/{id}` - Listar reviews
- `POST /api/reviews/{id}/approve` - Aprovar review
- `POST /api/reviews/{id}/reject` - Rejeitar review
- `GET /api/reviews/sites/{id}/stats` - Estatísticas de reviews

## 🎯 Widget Embed

### Integração Básica

```html
<!-- SitePulse Widget -->
<script async src="http://localhost:8000/widget/{widget-id}.js"></script>
<!-- End SitePulse Widget -->
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

## 📊 Planos e Limites

### Free
- 1,000 visitas/mês
- 5,000 eventos/mês
- 1 site
- 50 reviews/mês
- 0 exportações

### Basic
- 10,000 visitas/mês
- 50,000 eventos/mês
- 3 sites
- 500 reviews/mês
- 10 exportações/mês

### Premium
- 100,000 visitas/mês
- 500,000 eventos/mês
- 10 sites
- 5,000 reviews/mês
- 100 exportações/mês

### Enterprise
- Ilimitado
- Suporte prioritário
- Integrações customizadas

## 🔒 Segurança e Privacidade

- **GDPR/CCPA Compliant**: Coleta de dados anonimizados
- **Rate Limiting**: Proteção contra abusos
- **API Key Authentication**: Autenticação segura
- **IP Anonymization**: Opção de anonimizar IPs
- **Do Not Track**: Respeita preferências do usuário

## 🚀 Deploy

### Produção

1. **Configure o servidor web** (Nginx/Apache)
2. **Configure SSL** para HTTPS
3. **Configure cache** (Redis/Memcached)
4. **Configure queue workers** para processamento assíncrono
5. **Configure monitoring** e logs

### Docker (Opcional)

```dockerfile
FROM php:8.1-fpm
# ... configuração do Docker
```

## 📈 Monitoramento

- **Logs**: Laravel logs para debugging
- **Métricas**: Dashboard administrativo
- **Alertas**: Webhooks para eventos críticos
- **Health Checks**: Endpoints de status

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para detalhes.

## 📞 Suporte

- **Email**: support@sitepulse.com
- **Documentação**: [docs.sitepulse.com](https://docs.sitepulse.com)
- **Issues**: [GitHub Issues](https://github.com/sitepulse/analytics/issues)

---

**SitePulse Widgets** - Colete reviews e exiba widgets lindos no seu site.