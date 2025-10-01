# 🐛 Debug do Widget SitePulse

## ✅ **Verificações Realizadas**

### 1. **Script está sendo servido** ✅
```bash
curl -I http://127.0.0.1:8000/widget/sp_780370e55fca84c9bbf69fb9e9527994.js
# Resultado: HTTP/1.1 200 OK
```

### 2. **Componentes criados** ✅
- ✅ `WidgetService.php` - Serviço para gerar scripts
- ✅ `widget/script.blade.php` - Template do script JavaScript
- ✅ Métodos adicionados ao `AnalyticsService`

## 🔍 **Como Debugar**

### **Passo 1: Verificar Console do Navegador**
1. Abra `test-embed.html` no navegador
2. Pressione `F12` para abrir DevTools
3. Vá para a aba **Console**
4. Procure por:
   - ✅ `SitePulse: Events sent successfully`
   - ❌ Erros de JavaScript
   - ❌ Erros de CORS

### **Passo 2: Verificar Network Tab**
1. No DevTools, vá para **Network**
2. Recarregue a página
3. Procure por:
   - ✅ Requisição para `/widget/sp_780370e55fca84c9bbf69fb9e9527994.js`
   - ✅ Requisições POST para `/api/widget/events`

### **Passo 3: Verificar Logs do Laravel**
```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Logs
tail -f storage/logs/laravel.log
```

### **Passo 4: Testar API Manualmente**
```bash
# Testar endpoint de eventos
curl -X POST http://127.0.0.1:8000/api/widget/events \
  -H "Content-Type: application/json" \
  -H "X-Session-Token: sp_test_123" \
  -d '{
    "site_id": 1,
    "events": [{
      "type": "pageview",
      "data": {"url": "http://test.com", "title": "Test"},
      "timestamp": 1640995200000
    }]
  }'
```

## 🚨 **Problemas Comuns**

### **1. CORS Error**
```
Access to XMLHttpRequest at 'http://127.0.0.1:8000/api/widget/events' 
from origin 'null' has been blocked by CORS policy
```
**Solução**: Adicionar middleware CORS ou usar servidor HTTP

### **2. Site não encontrado**
```
SitePulse: Site not found or inactive
```
**Solução**: Verificar se o site existe e está ativo no banco

### **3. JavaScript não carrega**
```
Failed to load resource: net::ERR_CONNECTION_REFUSED
```
**Solução**: Verificar se Laravel está rodando na porta 8000

## 🛠️ **Soluções**

### **Solução 1: Usar Servidor HTTP**
```bash
# Em vez de abrir arquivo diretamente
python3 -m http.server 3000
# Acesse: http://localhost:3000/test-embed.html
```

### **Solução 2: Verificar Site no Banco**
```bash
# Verificar se o site existe
php artisan tinker
>>> App\Models\Site::where('widget_id', 'sp_780370e55fca84c9bbf69fb9e9527994')->first()
```

### **Solução 3: Debug no JavaScript**
Adicione no console do navegador:
```javascript
// Verificar se SitePulse está carregado
console.log(window.SitePulse);

// Verificar eventos
console.log(window.SitePulse.events);

// Forçar envio de eventos
window.SitePulse.sendEvents();
```

## 📊 **Verificar Dados no Dashboard**

1. Acesse: http://127.0.0.1:8000/dashboard
2. Vá para **Analytics** → **Seu Site**
3. Verifique:
   - ✅ **Sessions**: Deve mostrar 1+
   - ✅ **Visits**: Deve mostrar 1+
   - ✅ **Events**: Deve mostrar 5+

## 🎯 **Próximos Passos**

Se ainda não funcionar:
1. **Verificar banco de dados**: Se as tabelas existem
2. **Verificar migrations**: Se foram executadas
3. **Verificar logs**: Se há erros no Laravel
4. **Testar com Postman**: API manualmente

---

**💡 Dica**: Mantenha o DevTools aberto e monitore as requisições em tempo real!
