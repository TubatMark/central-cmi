<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/auth.php';
require_role(['representative', 'secretariat']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get user position from session
$userPosition = isset($_SESSION['position']) ? strtoupper(trim($_SESSION['position'])) : '';

// Valid positions
$validPositions = ['ICTC', 'RDC', 'SCC', 'TTC'];

if (!in_array($userPosition, $validPositions)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid user position']);
    exit;
}

// Load the appropriate template file
$templateFile = __DIR__ . '/../accomplishment-templates/' . $userPosition . '.json';

if (!file_exists($templateFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Template not found for position: ' . $userPosition]);
    exit;
}

// Read and return the template
$templateContent = file_get_contents($templateFile);
$template = json_decode($templateContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    echo json_encode(['error' => 'Invalid JSON template file']);
    exit;
}

echo json_encode([
    'success' => true,
    'template' => $template,
    'user_position' => $userPosition
]);
?>
