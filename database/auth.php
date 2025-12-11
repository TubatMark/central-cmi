<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_role(array $allowed_roles) {
    $isSecretariat = !empty($_SESSION['is_secretariat']) || (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'secretariat');
    $isRepresentative = !empty($_SESSION['is_representative']) || (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'representative');

    $user_has_role = false;
    foreach ($allowed_roles as $role) {
        if ($role === 'secretariat' && $isSecretariat) { $user_has_role = true; break; }
        if ($role === 'representative' && $isRepresentative) { $user_has_role = true; break; }
    }

    if (!$user_has_role && $isSecretariat) { $user_has_role = true; }

    if (!$user_has_role) {
        // Use dynamic base URL from app config
        $base = defined('BASE_URL') ? BASE_URL : (isset($GLOBALS['base_url']) ? $GLOBALS['base_url'] : '/');
        header('Location: ' . $base . 'pages/login.php');
        exit;
    }
}

function is_secretariat(): bool {
    return !empty($_SESSION['is_secretariat']) || (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'secretariat');
}

function is_representative(): bool {
    return !empty($_SESSION['is_representative']) || (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'representative');
}


