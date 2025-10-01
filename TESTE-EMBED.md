# 🧪 Guia de Teste do Widget SitePulse

## 📋 Pré-requisitos

1. **Laravel rodando**: `php artisan serve` (http://127.0.0.1:8000)
2. **Site criado**: Com widget ID `sp_780370e55fca84c9bbf69fb9e9527994`
3. **Navegador**: Chrome, Firefox ou Safari

## 🚀 Métodos de Teste

### **Método 1: Arquivo HTML Simples**

```bash
# 1. Abra o arquivo de teste
open test-embed.html

# 2. Ou use um servidor local
python3 -m http.server 3000
# Acesse: http://localhost:3000/test-embed.html
```

### **Método 2: Servidor Python (Recomendado)**

```bash
# 1. Execute o servidor de teste
python3 test-server.py

# 2. Acesse automaticamente: http://localhost:3000
# 3. Navegue entre as páginas para testar
```

### **Método 3: Teste Manual**

1. **Crie um site** no dashboard SitePulse
2. **Copie o código de embed** da página do site
3. **Cole em qualquer página HTML** local
4. **Abra no navegador** e teste as interações

## 🎯 O que Testar

### **Eventos Automáticos**
- ✅ **Page Views**: Carregamento de páginas
- ✅ **Scroll Events**: Rolagem da página
- ✅ **Session Tracking**: Sessões de usuário

### **Eventos de Clique**
- ✅ **Botões**: Cliques em botões
- ✅ **Links**: Navegação entre páginas
- ✅ **Formulários**: Submissão de forms

### **Eventos de Formulário**
- ✅ **Input Focus**: Foco em campos
- ✅ **Form Submit**: Envio de formulários
- ✅ **Field Changes**: Mudanças em campos

## 🔍 Verificando os Dados

### **No Dashboard SitePulse**
1. Acesse: http://127.0.0.1:8000/dashboard
2. Vá para **Analytics** → **Seu Site**
3. Verifique as métricas em tempo real

### **No Console do Navegador**
```javascript
// Abra o DevTools (F12)
// Verifique se há erros de carregamento do script
// Monitore as requisições para o servidor SitePulse
```

### **Logs do Laravel**
```bash
# Monitore os logs em tempo real
tail -f storage/logs/laravel.log

# Ou use o comando do Laravel
php artisan log:tail
```

## 🐛 Solução de Problemas

### **Script não carrega**
- ✅ Verifique se o Laravel está rodando
- ✅ Confirme se o widget ID está correto
- ✅ Teste a URL: http://localhost/widget/sp_780370e55fca84c9bbf69fb9e9527994.js

### **Eventos não aparecem**
- ✅ Aguarde alguns segundos (processamento assíncrono)
- ✅ Verifique se o site está ativo no dashboard
- ✅ Teste com diferentes navegadores

### **CORS Errors**
- ✅ Use `http://localhost` em vez de `http://127.0.0.1`
- ✅ Configure CORS no Laravel se necessário

## 📊 Dados de Teste Esperados

Após testar por alguns minutos, você deve ver:

- **Sessions**: 1+ sessões
- **Visits**: 1+ visitas
- **Events**: 10+ eventos (cliques, scrolls, etc.)
- **Pages**: Páginas visitadas
- **Real-time**: Atividade em tempo real

## 🎉 Próximos Passos

1. **Teste em produção**: Use ngrok ou deploy real
2. **Teste em mobile**: Responsividade do widget
3. **Teste de performance**: Múltiplas sessões simultâneas
4. **Teste de dados**: Exportação e relatórios

---

**💡 Dica**: Mantenha o dashboard aberto em uma aba para ver os dados chegando em tempo real!
