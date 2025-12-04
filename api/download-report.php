<?php
require_once __DIR__ . '/../database/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_role(['secretariat', 'representative']);

$filename = $_GET['file'] ?? '';

// Validate filename - only allow alphanumeric, underscore, hyphen, and .docx extension
if (!preg_match('/^report_[\d\-_]+\.docx$/', $filename)) {
    http_response_code(400);
    die('Invalid filename');
}

$filepath = __DIR__ . '/../uploads/reports/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    die('File not found');
}

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

// Output file
readfile($filepath);
exit;
