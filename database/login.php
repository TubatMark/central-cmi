<?php
require_once __DIR__ . '/config.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Detect if the request expects JSON (AJAX/fetch) vs normal form POST
$acceptHeader = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
$requestedWith = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) : '';
$wantsJson = (strpos($acceptHeader, 'application/json') !== false) || ($requestedWith === 'xmlhttprequest');
if ($wantsJson) {
    header('Content-Type: application/json');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($wantsJson) {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    } else {
        header('Location: ../pages/login.php');
    }
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    if ($wantsJson) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Email and password are required.']);
    } else {
        header('Location: ../pages/login.php');
    }
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT UserID, username, password_hash, firstName, lastName, email, position, agency, is_representative, is_secretariat FROM `User` WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        if ($wantsJson) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Invalid credentials.']);
        } else {
            header('Location: ../pages/login.php');
        }
        exit;
    }

    // Set session
    $_SESSION['user_id'] = (int)$user['UserID'];
    $_SESSION['user_name'] = $user['firstName'] . ' ' . $user['lastName'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['position'] = $user['position'];
    $_SESSION['agency'] = $user['agency'];
    $_SESSION['is_representative'] = (int)$user['is_representative'];
    $_SESSION['is_secretariat'] = (int)$user['is_secretariat'];
    $_SESSION['user_role'] = $user['is_secretariat'] ? 'secretariat' : 'representative';

    if ($wantsJson) {
        echo json_encode(['ok' => true, 'role' => $_SESSION['user_role']]);
    } else {
        if (!headers_sent()) {
            if (!empty($user['is_secretariat'])) {
                header('Location: ../pages/is_secretariat/secretariat_dashboard.php');
            } else {
                header('Location: ../pages/is_representative/representative_dashboard.php');
            }
        }
    }
} catch (Throwable $e) {
    if ($wantsJson) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'Server error']);
    } else {
        header('Location: ../pages/login.php');
    }
}


