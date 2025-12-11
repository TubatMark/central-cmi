# Central CMI - WESMAARRDEC Activity Management System

A comprehensive web-based activity management and reporting system for the Western Visayas Agriculture, Aquatic and Resources Research and Development Consortium (WESMAARRDEC).

## Overview

Central CMI enables consortium member agencies to manage, track, and report their research and development activities. The system supports two primary user roles:

- **Representatives** - Agency representatives who submit and manage activities
- **Secretariat** - Administrative staff who oversee all activities, manage users, and generate reports

## Features

### Activity Management
- Create, edit, and track R&D activities
- Multi-image attachment support
- Status tracking (Pending, Ongoing, Completed)
- Filter by date range, status, and type
- Bulk actions for activity management

### User Management (Secretariat)
- Create and manage user accounts
- Role-based access control
- Agency and cluster assignment
- Account status management

### Notification System
- In-app notification center
- Role-based notification targeting
- Read/unread tracking
- Priority levels (High, Medium, Low)
- Type categorization (Deadline, Approval, Meeting, System, Report, General)

### Report Generation
- AI-powered narrative report generation (Groq/Llama 3.3)
- Export to DOCX format
- Customizable filters (Period, Agency, Cluster)
- Professional formatting with executive summaries

### Dashboard
- Activity statistics and overview
- Recent activities display
- Quick action buttons
- Role-specific views

## Tech Stack

| Component | Technology |
|-----------|------------|
| Backend | PHP 8.x |
| Database | MySQL/MariaDB |
| Frontend | HTML5, Tailwind CSS, JavaScript |
| AI Integration | Groq API (Llama 3.3) |
| Server | Apache (XAMPP) |

## Project Structure

```
central-cmi/
├── api/                    # REST API endpoints
│   ├── activities.php      # Activity CRUD operations
│   ├── notifications.php   # Notification management
│   ├── users.php           # User management
│   ├── generate-report.php # AI report generation
│   └── download-report.php # Report file download
├── config/                 # Configuration files
│   └── ai-config.php       # AI API configuration (gitignored)
├── database/               # Database layer
│   ├── config.php          # Database connection
│   ├── schema.sql          # Database schema
│   └── auth.php            # Authentication functions
├── includes/               # Shared components
│   ├── header.php          # HTML head section
│   ├── navbar.php          # Navigation bar
│   └── footer.php          # Footer section
├── pages/                  # Application pages
│   ├── is_representative/  # Representative-only pages
│   │   ├── activity_management.php
│   │   ├── notifications.php
│   │   └── representative_dashboard.php
│   └── is_secretariat/     # Secretariat-only pages
│       ├── activity_management.php
│       ├── notification_center.php
│       ├── report_generation.php
│       ├── secretariat_dashboard.php
│       └── user_management.php
├── uploads/                # User uploaded files (gitignored)
│   ├── activities/         # Activity attachments
│   └── reports/            # Generated reports
├── assets/                 # Static assets
│   ├── css/
│   └── js/
└── accomplishment-templates/ # JSON report templates
```

## Installation

### Prerequisites
- PHP 8.x with PDO MySQL extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache or Nginx web server
- Node.js (optional, for Tailwind CSS compilation)

### Platform-Specific Setup

#### Windows (XAMPP)

1. **Install XAMPP** from https://www.apachefriends.org/
2. **Copy project** to `C:\xampp\htdocs\central-cmi`
3. **Start Apache and MySQL** from XAMPP Control Panel
4. **Create database** via phpMyAdmin or command line:
   ```cmd
   C:\xampp\mysql\bin\mysql -u root < database\schema.sql
   ```
5. **Access**: http://localhost/central-cmi/

#### macOS (XAMPP)

1. **Install XAMPP** from https://www.apachefriends.org/
2. **Copy project** to `/Applications/XAMPP/xamppfiles/htdocs/central-cmi`
3. **Start XAMPP** and enable Apache + MySQL
4. **Create database**:
   ```bash
   /Applications/XAMPP/xamppfiles/bin/mysql -u root < database/schema.sql
   ```
5. **Set permissions**:
   ```bash
   chmod -R 777 uploads/
   ```
6. **Access**: http://localhost/central-cmi/

#### Linux (Apache + MySQL)

1. **Install requirements**:
   ```bash
   sudo apt install apache2 php php-mysql php-pdo mysql-server
   ```
2. **Clone/copy project** to `/var/www/html/central-cmi`
3. **Create database**:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
4. **Set permissions**:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/central-cmi
   chmod -R 755 /var/www/html/central-cmi
   chmod -R 777 uploads/
   ```
5. **Access**: http://localhost/central-cmi/

#### Production Deployment

1. **Upload files** to your web server's document root
2. **Create database** and import `database/schema.sql`
3. **Configure environment** - Copy and edit config:
   ```bash
   cp config/env.example.php config/env.php
   ```
   Edit `config/env.php` with your database credentials:
   ```php
   return [
       'db_host' => 'your-db-host',
       'db_name' => 'central_cmi',
       'db_user' => 'your-db-user',
       'db_pass' => 'your-db-password',
       'db_port' => 3306,
   ];
   ```
4. **Set permissions** on `uploads/` directory
5. **Configure AI** (Optional) - Create `config/ai-config.php`:
   ```php
   <?php
   define('GROQ_API_KEY', 'your-groq-api-key');
   define('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');
   define('GROQ_MODEL', 'llama-3.3-70b-versatile');
   ```
   Get a free API key at: https://console.groq.com

### Folder Installation

The application auto-detects its base URL, so it works in:
- Root: `http://yourdomain.com/`
- Subfolder: `http://yourdomain.com/central-cmi/`
- Any custom folder name: `http://yourdomain.com/my-app/`

### Tailwind CSS (Optional)

If you need to modify styles:
```bash
npm install
npm run build:css
```

## Database Schema

### Core Tables

| Table | Description |
|-------|-------------|
| `User` | User accounts with roles and agency info |
| `Activity` | R&D activities with details and status |
| `ActivityAttachment` | File attachments for activities |
| `EmailNotification` | Notification messages |
| `NotificationRecipient` | Notification delivery tracking |

### User Roles

| Role | Access Level |
|------|--------------|
| `is_representative` | Submit/manage own activities, view notifications |
| `is_secretariat` | Full access: all activities, users, reports, notifications |

## API Endpoints

### Activities
- `GET /api/activities.php` - List activities
- `POST /api/activities.php` - Create activity
- `PUT /api/activities.php` - Update activity
- `DELETE /api/activities.php?id={id}` - Delete activity

### Notifications
- `GET /api/notifications.php` - Get received notifications
- `GET /api/notifications.php?sent=1` - Get sent notifications
- `POST /api/notifications.php` - Send notification
- `PUT /api/notifications.php` - Mark read/unread
- `DELETE /api/notifications.php?id={id}` - Delete notification

### Users
- `GET /api/users.php` - List users
- `POST /api/users.php` - Create user
- `PUT /api/users.php` - Update user
- `DELETE /api/users.php?id={id}` - Delete user

### Reports
- `POST /api/generate-report.php` - Generate AI narrative report
- `GET /api/download-report.php?file={filename}` - Download report

## Member Agencies

The system supports the following WESMAARRDEC member agencies:

- PCAARRD
- DOST-IX
- DA-RFO IX
- WMSU
- JHCSC
- DTI-IX
- BFAR-IX
- NEDA-IX
- PRRI-IX
- PhilFIDA-IX
- DA-BAR
- PCA-ZRC

## Clusters

Activities are organized into four clusters:

| Code | Cluster Name |
|------|--------------|
| ICTC | Information, Communication, Technology Cluster |
| RDC | Research & Development Cluster |
| SCC | Science Communication Cluster |
| TTC | Technology Transfer Cluster |

## Security Considerations

- Session-based authentication
- Role-based access control on all pages and APIs
- SQL injection prevention via prepared statements
- XSS prevention via output escaping
- Sensitive config files excluded from version control
- File upload validation and restrictions

## Development

### Build Tailwind CSS
```bash
npm run build:css
```

### Watch for changes
```bash
npm run dev
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is developed for WESMAARRDEC internal use.

---

**Central CMI** - Centralized Monitoring and Information System for WESMAARRDEC
