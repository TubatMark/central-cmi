<?php
/**
 * Environment Configuration Template
 * 
 * Copy this file to 'env.php' and customize for your environment.
 * The env.php file should NOT be committed to version control.
 */

return [
    // Database Configuration
    'db_host' => 'localhost',
    'db_name' => 'central_cmi',
    'db_user' => 'root',
    'db_pass' => '',
    'db_port' => 3306,
    
    // AI Configuration (Optional - for report generation)
    'groq_api_key' => '',  // Get from https://console.groq.com
    
    // Application Settings
    'debug' => false,      // Set to true for development
    'timezone' => 'Asia/Manila',
];
