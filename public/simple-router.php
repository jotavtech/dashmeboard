<?php
// Simple router to handle basic routing without Laravel
// This is a temporary workaround for PHP version compatibility issues

session_start();

// Basic routing
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove trailing slash
$path = rtrim($path, '/');

// Simple route handling
switch ($path) {
    case '':
    case '/':
        // Redirect to login or dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
        } else {
            header('Location: /login');
        }
        exit;
        
    case '/dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        include '../resources/views/dashboard.blade.php';
        break;
        
    case '/atividades':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        include '../resources/views/atividades/index.blade.php';
        break;
        
    case '/atividades/historico':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        include '../resources/views/atividades/historico.blade.php';
        break;
        
    case '/login':
        include '../resources/views/auth/login.blade.php';
        break;
        
    case '/register':
        include '../resources/views/auth/register.blade.php';
        break;
        
    case '/test':
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Test route working!', 'path' => $path]);
        exit;
        break;
        
    case '/test-atividades':
        header('Content-Type: application/json');
        echo json_encode(['message' => 'Atividades route test working!', 'path' => $path]);
        exit;
        break;
        
    case '/debug.php':
        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'Debug test working!',
            'php_version' => PHP_VERSION,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'not set',
            'path' => parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH)
        ]);
        break;
        
    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1>';
        echo '<p>The requested page "' . htmlspecialchars($path) . '" was not found.</p>';
        break;
}
?> 