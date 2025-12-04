# Central CMI - Project Documentation

## Project Overview

**Central CMI** is a modern, government-focused activity management system designed to streamline activity tracking, reporting, and collaboration workflows for government agencies. The application provides comprehensive dashboards for different user roles, activity management capabilities, report generation, and calendar integration.

### Key Features
- **Multi-role Dashboard System**: Separate interfaces for Representatives and Secretariat
- **Activity Management**: Create, track, and manage government activities with progress monitoring
- **Report Generation**: Automated report creation with customizable templates
- **Calendar Integration**: Schedule and manage events and deadlines
- **User Management**: Registration, authentication, and profile management
- **Notification System**: Real-time notifications and alerts
- **Responsive Design**: Mobile-first approach with modern UI/UX

## Tech Stack

### Frontend Technologies
- **HTML5**: Modern semantic markup with accessibility features
- **CSS3**: Custom styling with CSS variables and modern layout techniques
- **Tailwind CSS**: Utility-first CSS framework for rapid UI development
- **JavaScript**: Vanilla JavaScript for interactive functionality
- **Font Awesome**: Icon library for consistent iconography

### Build Tools & Dependencies
- **Node.js & NPM**: Package management and build tooling
- **Tailwind CSS**: CSS framework with custom configuration
- **PostCSS**: CSS processing and optimization
- **Component Tagger**: DHiwise component organization tool

### External Services
- **Google Fonts**: Inter and JetBrains Mono font families
- **Unsplash/Pexels**: Placeholder images for user avatars and content
- **Rocket.new**: Development platform integration

## Folder Structure

```
central-cmi/
├── README.md                    # Project overview and setup instructions
├── package.json                 # NPM dependencies and build scripts
├── package-lock.json           # Locked dependency versions
├── tailwind.config.js          # Tailwind CSS configuration
├── index.html                  # Main entry point with auto-redirect
├── css/
│   ├── tailwind.css            # Source Tailwind CSS with custom styles
│   └── main.css                # Compiled CSS output (generated)
├── pages/
│   ├── login.html              # User authentication page
│   ├── user_registration.html  # New user registration form
│   ├── representative_dashboard.html  # Representative role dashboard
│   ├── secretariat_dashboard.html     # Secretariat role dashboard
│   ├── activity_management.html       # Activity CRUD operations
│   ├── report_generation.html         # Report creation and export
│   ├── calendar_management.html       # Event and schedule management
│   ├── notification_center.html       # Notification management
│   └── user_profile_settings.html     # User profile and preferences
└── public/
    ├── favicon.ico             # Application favicon
    └── manifest.json           # PWA manifest configuration
```

## Key Code Descriptions

### Entry Point (`index.html`)
- **Purpose**: Application entry point with automatic redirection
- **Features**: 
  - Loading animation with countdown timer
  - Automatic redirect to representative dashboard
  - Fallback redirect for JavaScript-disabled browsers
  - Responsive design with gradient background

### Authentication System (`login.html`)
- **Features**:
  - Email/password authentication
  - Password visibility toggle
  - Two-factor authentication support
  - Failed attempt tracking with lockout mechanism
  - Forgot password modal
  - Security compliance notices

### User Registration (`user_registration.html`)
- **Features**:
  - Multi-step registration form
  - Real-time field validation
  - Password strength indicator
  - File upload for documents
  - Agency affiliation selection
  - Terms and conditions acceptance

### Dashboard Systems

#### Representative Dashboard (`representative_dashboard.html`)
- **Features**:
  - Activity summary metrics
  - Recent activity timeline
  - Quick action buttons
  - Notification dropdown
  - User profile menu
  - Responsive navigation

#### Secretariat Dashboard (`secretariat_dashboard.html`)
- **Features**:
  - System-wide oversight
  - Bulk notification capabilities
  - User management tools
  - System statistics
  - Administrative controls
  - Consolidated reporting

### Activity Management (`activity_management.html`)
- **Features**:
  - Activity CRUD operations
  - Progress tracking with milestones
  - Category and priority management
  - Deadline monitoring
  - Status workflow management
  - Bulk operations support

### Report Generation (`report_generation.html`)
- **Features**:
  - Activity selection interface
  - Customizable report templates
  - Date range filtering
  - Department-based filtering
  - Export capabilities
  - Report preview functionality

### Styling Architecture (`css/`)

#### Tailwind Configuration (`tailwind.config.js`)
- **Custom Color Palette**: Government-appropriate color scheme
- **Typography**: Inter and JetBrains Mono font stacks
- **Component Classes**: Reusable button and form styles
- **Utility Extensions**: Custom transitions and shadows
- **Responsive Breakpoints**: Mobile-first design approach

#### Custom CSS (`css/tailwind.css`)
- **CSS Variables**: Consistent color and spacing system
- **Component Layer**: Reusable component styles
- **Utility Layer**: Custom utility classes
- **Base Layer**: Global typography and layout styles

## Data Flow Architecture

### Current Implementation (Frontend-Only)
```
User Interface (HTML) 
    ↓
User Interactions (JavaScript)
    ↓
Local State Management
    ↓
UI Updates & Feedback
```

### Recommended Full-Stack Architecture
```
Client (Browser)
    ↓ HTTP Requests
Web Server (Apache/Nginx)
    ↓ Route Handling
PHP Application Layer
    ↓ Database Queries
MySQL Database
    ↓ Data Response
PHP Processing
    ↓ JSON Response
Client-Side JavaScript
    ↓ DOM Updates
User Interface
```

## Database Schema Recommendations

### Core Tables

```sql
-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    designation ENUM('director', 'assistant-director', 'manager', 'senior-officer', 'officer', 'assistant-officer', 'coordinator', 'specialist', 'analyst', 'secretary') NOT NULL,
    position VARCHAR(255) NOT NULL,
    agency_id INT NOT NULL,
    role ENUM('representative', 'secretariat') NOT NULL,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agency_id) REFERENCES agencies(id)
);

-- Agencies table
CREATE TABLE agencies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Activities table
CREATE TABLE activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('policy', 'training', 'outreach', 'audit', 'research') NOT NULL,
    status ENUM('not-started', 'in-progress', 'completed', 'on-hold') DEFAULT 'not-started',
    priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    progress INT DEFAULT 0 CHECK (progress >= 0 AND progress <= 100),
    start_date DATE,
    deadline DATE,
    accomplishments TEXT,
    created_by INT NOT NULL,
    assigned_to INT,
    agency_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (agency_id) REFERENCES agencies(id)
);

-- Activity milestones table
CREATE TABLE activity_milestones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activity_id INT NOT NULL,
    text VARCHAR(255) NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMP NULL,
    order_index INT NOT NULL,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
);

-- Reports table
CREATE TABLE reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    type ENUM('activity', 'consolidated', 'department', 'custom') NOT NULL,
    content TEXT,
    generated_by INT NOT NULL,
    date_range_start DATE,
    date_range_end DATE,
    filters JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(id)
);

-- Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type ENUM('info', 'warning', 'error', 'success') DEFAULT 'info',
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Improvement Plan

### Phase 1: Backend Implementation (Priority: High)

#### 1.1 Database Setup
- [ ] Set up MySQL database with recommended schema
- [ ] Create database migration scripts
- [ ] Implement database connection configuration
- [ ] Add data seeding for initial agencies and test users

#### 1.2 PHP Backend Development
- [ ] Create PHP configuration files (`config/database.php`, `config/app.php`)
- [ ] Implement database abstraction layer (PDO-based)
- [ ] Create user authentication system with session management
- [ ] Develop RESTful API endpoints for all CRUD operations
- [ ] Implement role-based access control (RBAC)

#### 1.3 Security Implementation
- [ ] Add password hashing (bcrypt/Argon2)
- [ ] Implement CSRF protection
- [ ] Add input validation and sanitization
- [ ] Create secure session management
- [ ] Implement rate limiting for API endpoints

### Phase 2: Enhanced Functionality (Priority: Medium)

#### 2.1 Advanced Features
- [ ] File upload system for activity documents
- [ ] Email notification system (PHPMailer)
- [ ] Advanced search and filtering capabilities
- [ ] Data export functionality (PDF, Excel)
- [ ] Activity collaboration features

#### 2.2 Performance Optimization
- [ ] Implement database indexing strategy
- [ ] Add caching layer (Redis/Memcached)
- [ ] Optimize SQL queries
- [ ] Implement pagination for large datasets
- [ ] Add database connection pooling

#### 2.3 UI/UX Improvements
- [ ] Add loading states and skeleton screens
- [ ] Implement real-time notifications (WebSockets/Server-Sent Events)
- [ ] Create progressive web app (PWA) features
- [ ] Add dark mode support
- [ ] Implement keyboard navigation and accessibility features

### Phase 3: Advanced Features (Priority: Low)

#### 3.1 Analytics and Reporting
- [ ] Create dashboard analytics with charts (Chart.js)
- [ ] Implement activity performance metrics
- [ ] Add automated report scheduling
- [ ] Create data visualization components
- [ ] Implement audit logging system

#### 3.2 Integration and Scalability
- [ ] API documentation (OpenAPI/Swagger)
- [ ] Third-party calendar integration (Google Calendar, Outlook)
- [ ] Single Sign-On (SSO) implementation
- [ ] Multi-tenant architecture support
- [ ] Microservices architecture consideration

## Recommended File Structure (After Backend Implementation)

```
central-cmi/
├── config/
│   ├── database.php            # Database configuration
│   ├── app.php                 # Application settings
│   └── routes.php              # API route definitions
├── src/
│   ├── Controllers/            # Request handling logic
│   ├── Models/                 # Data models and database interaction
│   ├── Services/               # Business logic layer
│   ├── Middleware/             # Authentication, validation, etc.
│   └── Utils/                  # Helper functions and utilities
├── api/
│   ├── auth/                   # Authentication endpoints
│   ├── activities/             # Activity CRUD endpoints
│   ├── reports/                # Report generation endpoints
│   └── users/                  # User management endpoints
├── database/
│   ├── migrations/             # Database schema migrations
│   ├── seeds/                  # Initial data seeding
│   └── schema.sql              # Complete database schema
├── uploads/                    # File upload directory
├── logs/                       # Application logs
├── public/                     # Web-accessible files
│   ├── css/
│   ├── js/
│   ├── images/
│   └── index.php               # Application entry point
└── vendor/                     # Composer dependencies
```

## Security Best Practices

### Authentication & Authorization
- Use strong password policies with complexity requirements
- Implement multi-factor authentication for sensitive roles
- Use secure session management with proper timeout
- Implement role-based access control (RBAC)
- Add account lockout after failed login attempts

### Data Protection
- Encrypt sensitive data at rest and in transit
- Use prepared statements to prevent SQL injection
- Implement proper input validation and sanitization
- Add CSRF protection for all forms
- Use HTTPS for all communications

### Infrastructure Security
- Keep PHP and dependencies updated
- Configure proper file permissions
- Disable unnecessary PHP functions
- Implement proper error handling without information disclosure
- Add security headers (HSTS, CSP, X-Frame-Options)

## Performance Optimization Strategies

### Database Optimization
- Create appropriate indexes for frequently queried columns
- Implement database query optimization
- Use connection pooling for better resource management
- Consider read replicas for heavy read workloads
- Implement proper database backup and recovery procedures

### Caching Strategy
- Implement application-level caching for frequently accessed data
- Use browser caching for static assets
- Consider CDN for static file delivery
- Implement query result caching
- Use session caching for user data

### Frontend Optimization
- Minimize and compress CSS/JavaScript files
- Optimize images and use appropriate formats
- Implement lazy loading for large datasets
- Use efficient DOM manipulation techniques
- Minimize HTTP requests through bundling

## Deployment Recommendations

### Development Environment
- Use XAMPP/WAMP for local development
- Implement version control with Git
- Use environment-specific configuration files
- Set up automated testing framework
- Implement code quality tools (PHPStan, PHPCS)

### Production Environment
- Use dedicated web server (Apache/Nginx)
- Implement proper SSL/TLS configuration
- Set up automated backups
- Implement monitoring and logging
- Use process managers for PHP (PHP-FPM)

### Maintenance
- Regular security updates and patches
- Database maintenance and optimization
- Log rotation and cleanup
- Performance monitoring and optimization
- Regular backup testing and recovery procedures

---

*This documentation provides a comprehensive overview of the Central CMI project and serves as a roadmap for future development and improvements.*