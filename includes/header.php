<?php
// Load app configuration for dynamic base URL
require_once __DIR__ . '/../config/app.php';

// Set default page title if not provided
if (!isset($pageTitle)) {
    $pageTitle = "Central CMI";
}

// Set default body class if not provided
if (!isset($bodyClass)) {
    $bodyClass = "bg-background min-h-screen";
}

// Use dynamic base URL from app config
$base_url = defined('BASE_URL') ? BASE_URL : '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle); ?> - WESMAARRDEC Activity Management System</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/main.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <script type="module" src="https://static.rocket.new/rocket-web.js?_cfg=https%3A%2F%2Factivitytr6661back.builtwithrocket.new&_be=https%3A%2F%2Fapplication.rocket.new&_v=0.1.8"></script>
</head>
<body class="<?php echo htmlspecialchars($bodyClass); ?>">