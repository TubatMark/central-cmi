# InfinityFree Deployment Guide - Central CMI

## Project Overview

| Component | Details |
|-----------|---------|
| **Stack** | PHP 7.4+, MySQL 5.7+, TailwindCSS (pre-compiled) |
| **Database** | MySQL with InnoDB, utf8mb4 charset |
| **External APIs** | Groq AI (for report generation) |
| **File Uploads** | `uploads/activities/`, `uploads/reports/` |

---

## Pre-Deployment Checklist

### Files to Exclude from Upload
These are already in `.gitignore` and should NOT be uploaded:
- `node_modules/` (not needed - CSS is pre-compiled)
- `test-*.php` (test files)
- `setup-*.php` (setup scripts)
- `create-test-user.php`
- `prototype/`
- `config/ai-config.php` (contains API key - recreate on server)

### Files That MUST Be Uploaded
- `css/main.css` (pre-compiled TailwindCSS)
- All PHP files in `api/`, `database/`, `includes/`, `pages/`
- `accomplishment-templates/` folder
- `assets/` and `public/` folders
- `config/app.php` and `config/env.example.php`

---

## Deployment Steps

### Step 1: Create InfinityFree Account
1. Go to https://www.infinityfree.com/
2. Sign up for a free account
3. Create a new hosting account (choose a subdomain like `central-cmi.epizy.com`)

### Step 2: Create MySQL Database
1. In InfinityFree Control Panel, go to **MySQL Databases**
2. Click **Create Database**
3. Note down:
   - **Database Name**: `epiz_XXXXXXXX_central_cmi` (prefixed with your username)
   - **Database Username**: `epiz_XXXXXXXX`
   - **Database Password**: (the one you set)
   - **Database Host**: `sql###.epizy.com` (shown in panel)

### Step 3: Modify Database Schema
InfinityFree uses MySQL 5.7. Create a modified schema file:

```sql
-- Modified for InfinityFree MySQL 5.7
-- Run this in phpMyAdmin

-- Users Table
CREATE TABLE IF NOT EXISTS `User` (
  `UserID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `firstName` VARCHAR(100) NOT NULL,
  `lastName` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `birthdate` DATE NULL,
  `designation` VARCHAR(150) NULL,
  `position` ENUM('ICTC','RDC','SCC','TTC') NOT NULL,
  `agency` VARCHAR(200) NULL,
  `is_representative` TINYINT(1) NOT NULL DEFAULT 0,
  `is_secretariat` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `uk_user_username` (`username`),
  UNIQUE KEY `uk_user_email` (`email`),
  KEY `idx_user_position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (Copy remaining tables from database/schema.sql)
```

### Step 4: Create Environment Configuration

Create `config/env.php` on the server (DO NOT upload from local):

```php
<?php
return [
    // Database Configuration for InfinityFree
    'db_host' => 'sql###.epizy.com',  // Replace with your actual host
    'db_name' => 'epiz_XXXXXXXX_central_cmi',  // Your database name
    'db_user' => 'epiz_XXXXXXXX',  // Your database username
    'db_pass' => 'YOUR_PASSWORD',  // Your database password
    'db_port' => 3306,
    
    // AI Configuration (Optional)
    'groq_api_key' => 'YOUR_GROQ_API_KEY',  // Get from https://console.groq.com
    
    // Application Settings
    'debug' => false,
    'timezone' => 'Asia/Manila',
];
```

### Step 5: Update AI Config (if using report generation)

Create `config/ai-config.php` on the server:

```php
<?php
// Load from env.php for security
$envFile = __DIR__ . '/env.php';
$env = file_exists($envFile) ? require $envFile : [];

define('GROQ_API_KEY', $env['groq_api_key'] ?? '');
define('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');
define('GROQ_MODEL', 'llama-3.3-70b-versatile');
```

### Step 6: Upload Files

**Method A: File Manager (Recommended for first upload)**
1. Go to Control Panel > File Manager
2. Navigate to `htdocs` folder
3. Upload files (you can upload a ZIP and extract)

**Method B: FTP**
1. Get FTP credentials from Control Panel
2. Use FileZilla or similar FTP client
3. Upload to `/htdocs/` directory

**Upload Structure:**
```
htdocs/
├── api/
├── accomplishment-templates/
├── assets/
├── config/
│   ├── app.php
│   ├── env.php (create on server)
│   └── ai-config.php (create on server)
├── css/
│   └── main.css
├── database/
├── includes/
├── pages/
├── public/
├── uploads/
│   ├── activities/
│   └── reports/
└── index.php
```

### Step 7: Set Folder Permissions
In File Manager, set permissions for:
- `uploads/` - 755
- `uploads/activities/` - 755
- `uploads/reports/` - 755

### Step 8: Import Database
1. Go to Control Panel > phpMyAdmin
2. Select your database
3. Click **Import** tab
4. Upload/paste your modified schema SQL
5. Click **Go**

### Step 9: Create Initial User
Run this SQL in phpMyAdmin to create an admin user:

```sql
INSERT INTO `User` (username, password_hash, firstName, lastName, email, position, is_representative, is_secretariat)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: 'password'
    'Admin',
    'User',
    'admin@yourdomain.com',
    'ICTC',
    1,
    1
);
```

---

## InfinityFree Limitations to Consider

| Limitation | Impact | Workaround |
|------------|--------|------------|
| **Daily Hits Limit** | ~50,000 hits/day | Monitor usage |
| **File Size** | Max 10MB upload | Compress files |
| **Execution Time** | 60 seconds | AI reports may timeout |
| **No SSH Access** | Cannot run CLI commands | Use File Manager/FTP |
| **No Cron Jobs** | Cannot schedule tasks | Use external cron service |
| **Subdomain Only** | Free = subdomain | Upgrade for custom domain |

---

## Post-Deployment Testing

1. **Homepage**: Visit `https://your-subdomain.epizy.com/`
2. **Login**: Test with admin credentials
3. **Activity Management**: Create/edit activities
4. **File Upload**: Test uploading attachments
5. **Report Generation**: Test AI report (may be slow)

---

## Troubleshooting

### Database Connection Error
- Verify `env.php` has correct credentials
- Check database host (sql###.epizy.com format)
- Ensure database exists in phpMyAdmin

### 500 Internal Server Error
- Check File Manager for `error_log` file
- Verify all PHP files uploaded correctly
- Check folder permissions

### CSS Not Loading
- Verify `css/main.css` was uploaded
- Check browser console for 404 errors

### AI Report Generation Fails
- InfinityFree may block external API calls
- Check if cURL is enabled
- Try reducing report size/complexity

---

## Security Reminders

1. **Never commit** `config/env.php` to git
2. **Never commit** `config/ai-config.php` with real API keys
3. **Change default passwords** after deployment
4. Keep API keys secure and rotate periodically

---

## Quick Reference

| Item | Value |
|------|-------|
| **Login URL** | `/pages/login.php` |
| **Default Admin** | `admin` / `password` |
| **phpMyAdmin** | Control Panel > MySQL > phpMyAdmin |
| **File Manager** | Control Panel > File Manager |
| **FTP Host** | ftpupload.net |

