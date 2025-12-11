<?php
// Load app configuration for dynamic base URL
require_once __DIR__ . '/../config/app.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
session_destroy();

// Use dynamic base URL
$base = defined('BASE_URL') ? BASE_URL : '/';
header('Location: ' . $base . 'pages/login.php');
exit;


