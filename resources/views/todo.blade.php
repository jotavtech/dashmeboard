<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List - Laravel + React</title>
    
    <!-- Meta tags para otimização de SEO -->
    <meta name="description" content="Aplicação de Todo List criada com Laravel e React">
    <meta name="keywords" content="todo, laravel, react, php, javascript">
    
    <!-- Token CSRF para proteção contra ataques cross-site request forgery -->
    <!-- Necessário para requisições POST/PUT/DELETE da API -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Ícone da aplicação -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Carrega os assets compilados pelo Vite -->
    <!-- Inclui CSS e JavaScript da aplicação React -->
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
</head>
<body>
    <!-- Container principal onde a aplicação React será renderizada -->
    <!-- O React irá montar toda a interface neste elemento -->
    <div id="app"></div>
    
    <!-- Mensagem de fallback para usuários sem JavaScript -->
    <!-- Aparece apenas se o JavaScript estiver desabilitado -->
    <noscript>
        <div style="text-align: center; padding: 50px;">
            <h1>Todo List</h1>
            <p>Esta aplicação requer JavaScript para funcionar.</p>
            <p>Por favor, habilite o JavaScript no seu navegador.</p>
        </div>
    </noscript>
</body>
</html> 