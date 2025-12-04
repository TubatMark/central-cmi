<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

function required($key) {
    return isset($_POST[$key]) && trim($_POST[$key]) !== '' ? trim($_POST[$key]) : null;
}

$firstName = required('firstName');
$lastName = required('lastName');
$email = required('email');
$username = required('username');
$password = required('password');
$designation = required('designation');
$position = required('position');
$agency = required('agency');
$birthdate = isset($_POST['birthdate']) && trim($_POST['birthdate']) !== '' ? trim($_POST['birthdate']) : null;

$validPositions = ['ICTC','RDC','SCC','TTC'];
if (!$firstName || !$lastName || !$email || !$username || !$password || !$designation || !$position || !$agency || !in_array($position, $validPositions, true)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Please fill in all required fields correctly.']);
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    if (!isset($pdo)) {
        throw new RuntimeException('Database connection is not available.');
    }
    $stmt = $pdo->prepare('INSERT INTO `User` (username, password_hash, firstName, lastName, email, birthdate, designation, position, agency, is_representative, is_secretariat, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 0, NOW())');
    $stmt->execute([$username, $password_hash, $firstName, $lastName, $email, $birthdate, $designation, $position, $agency]);
    echo json_encode(['ok' => true]);
} catch (Throwable $e) {
    http_response_code(400);
    $msg = stripos($e->getMessage(), 'Duplicate') !== false ? 'Email already registered.' : 'Registration failed. Please try again.';
    echo json_encode(['ok' => false, 'error' => $msg]);
}


