#!/usr/bin/env python3
"""
Servidor de teste simples para testar o widget SitePulse
Execute: python3 test-server.py
Acesse: http://localhost:3000
"""

import http.server
import socketserver
import os
import webbrowser
from urllib.parse import urlparse, parse_qs

class TestHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        if self.path == '/':
            self.send_response(200)
            self.send_header('Content-type', 'text/html')
            self.end_headers()
            
            html_content = """
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste SitePulse - Página 1</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f0f8ff; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .button:hover { background: #0056b3; }
        .form-group { margin: 15px 0; }
        .form-group input, .form-group textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏠 Página Inicial - Teste SitePulse</h1>
        <p>Esta é a página inicial para testar o widget de analytics.</p>
        
        <div class="form-group">
            <label>Nome:</label>
            <input type="text" id="name" placeholder="Digite seu nome">
        </div>
        
        <div class="form-group">
            <label>Email:</label>
            <input type="email" id="email" placeholder="Digite seu email">
        </div>
        
        <button class="button" onclick="testClick()">Clique Aqui</button>
        <button class="button" onclick="testForm()">Enviar Formulário</button>
        <a href="/page2" class="button">Ir para Página 2</a>
        
        <div style="height: 1500px; background: linear-gradient(to bottom, #e8f4f8, #d1ecf1); margin: 20px 0; padding: 20px;">
            <h3>Área de Scroll</h3>
            <p>Role para baixo para testar eventos de scroll...</p>
        </div>
    </div>

    <!-- SitePulse Analytics -->
    <script async src="http://localhost/widget/sp_780370e55fca84c9bbf69fb9e9527994.js"></script>
    <!-- End SitePulse Analytics -->

    <script>
        function testClick() {
            alert('Botão clicado! Evento registrado.');
        }
        
        function testForm() {
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            if (name && email) {
                alert('Formulário enviado! Evento registrado.');
            } else {
                alert('Preencha todos os campos.');
            }
        }
    </script>
</body>
</html>
            """
            self.wfile.write(html_content.encode())
            
        elif self.path == '/page2':
            self.send_response(200)
            self.send_header('Content-type', 'text/html')
            self.end_headers()
            
            html_content = """
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste SitePulse - Página 2</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #fff5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .button { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
        .button:hover { background: #1e7e34; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📄 Página 2 - Teste SitePulse</h1>
        <p>Esta é a segunda página para testar navegação entre páginas.</p>
        
        <button class="button" onclick="testAction()">Ação Especial</button>
        <button class="button" onclick="testPurchase()">Simular Compra</button>
        <a href="/" class="button">Voltar para Página 1</a>
        
        <div style="height: 1000px; background: linear-gradient(to bottom, #f8f9fa, #e9ecef); margin: 20px 0; padding: 20px;">
            <h3>Mais Conteúdo para Scroll</h3>
            <p>Continue testando os eventos de scroll...</p>
        </div>
    </div>

    <!-- SitePulse Analytics -->
    <script async src="http://localhost/widget/sp_780370e55fca84c9bbf69fb9e9527994.js"></script>
    <!-- End SitePulse Analytics -->

    <script>
        function testAction() {
            alert('Ação especial executada!');
        }
        
        function testPurchase() {
            alert('Compra simulada! Evento de conversão registrado.');
        }
    </script>
</body>
</html>
            """
            self.wfile.write(html_content.encode())
        else:
            super().do_GET()

if __name__ == "__main__":
    PORT = 3000
    
    with socketserver.TCPServer(("", PORT), TestHandler) as httpd:
        print(f"🚀 Servidor de teste rodando em http://localhost:{PORT}")
        print("📝 Substitua 'SEU_WIDGET_ID' pelo ID real do seu widget")
        print("🔄 Mantenha o Laravel rodando em http://localhost:8000")
        print("❌ Pressione Ctrl+C para parar")
        
        # Abrir automaticamente no navegador
        webbrowser.open(f'http://localhost:{PORT}')
        
        try:
            httpd.serve_forever()
        except KeyboardInterrupt:
            print("\n👋 Servidor parado!")
