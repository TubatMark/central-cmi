<?php
// Get current page for active navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
if (!isset($base_url)) { $base_url = '/central-cmi/'; }
if (session_status() === PHP_SESSION_NONE) { session_start(); }
@require_once __DIR__ . '/../database/config.php';

$displayName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
$displaySubtitle = '';
if (isset($_SESSION['user_id']) && isset($pdo)) {
    try {
        $stmt = $pdo->prepare('SELECT firstName, lastName, position, agency FROM `User` WHERE UserID = ? LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        if ($row = $stmt->fetch()) {
            $first = trim($row['firstName'] ?? '');
            $last = trim($row['lastName'] ?? '');
            $full = trim($first . ' ' . $last);
            if ($full !== '') { $displayName = $full; }
            $pos = strtoupper(trim($row['position'] ?? ''));
            $clusterMap = [
                'SCC' => 'Science Communication Cluster',
                'ICTC' => 'Information, Communication, Technology Cluster',
                'RDC' => 'Research & Development Cluster',
                'TTC' => 'Technology Transfer Cluster',
            ];
            if (isset($clusterMap[$pos])) {
                $displaySubtitle = $clusterMap[$pos];
            } else if (!empty($row['agency'])) {
                $displaySubtitle = $row['agency'];
            } else {
                $displaySubtitle = $pos;
            }
        }
    } catch (Throwable $e) {
        // ignore failures in navbar
    }
}
?>

<!-- Header Navigation -->
<header class="bg-surface shadow-card border-b border-secondary-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-primary" viewBox="0 0 32 32" fill="currentColor">
                        <path d="M16 2L3 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-13-5z"/>
                        <path d="M14 16l-2-2-1.41 1.41L14 18.83l6-6L18.59 11.41 14 16z" fill="white"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h1 class="text-xl font-semibold text-text-primary">Central CMI</h1>
                    <p class="text-xs text-text-secondary">WESMAARRDEC Activity Management System</p>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="hidden md:flex space-x-8">
                <?php if ($current_page !== 'login' && $current_page !== 'user_registration'): ?>
                <?php $canRep = function_exists('is_representative') ? is_representative() : true; ?>
                <?php $canSec = function_exists('is_secretariat') ? is_secretariat() : false; ?>
                <?php
                    $roleDir = $canSec ? 'is_secretariat' : 'is_representative';
                    $activityHref = $base_url . 'pages/' . $roleDir . '/activity_management.php';
                    $repDashHref = $base_url . 'pages/is_representative/representative_dashboard.php';
                    $secDashHref = $base_url . 'pages/is_secretariat/secretariat_dashboard.php';
                    $reportsHref = $base_url . 'pages/is_secretariat/report_generation.php';
                    $usersHref = $base_url . 'pages/is_secretariat/user_management.php';
                    $notifHref = $base_url . 'pages/is_secretariat/notification_center.php';
                ?>
                <?php if ($canRep || $canSec): ?>
                <a href="<?php echo $canSec ? $secDashHref : $repDashHref; ?>" class="text-text-secondary hover:text-primary transition-micro px-3 py-2 rounded-md hover:bg-secondary-100 <?php echo ($current_page == 'index' || ($canSec && $current_page == 'secretariat_dashboard') || ($canRep && !$canSec && $current_page == 'representative_dashboard')) ? 'text-primary font-medium bg-primary-50' : ''; ?>">Home</a>
                <a href="<?php echo $activityHref; ?>" class="text-text-secondary hover:text-primary transition-micro px-3 py-2 rounded-md hover:bg-secondary-100">Activities</a>
                <?php if ($canRep && !$canSec): ?>
                <a href="<?php echo $base_url; ?>pages/contact.php" class="text-text-secondary hover:text-primary transition-micro px-3 py-2 rounded-md hover:bg-secondary-100 <?php echo ($current_page == 'contact') ? 'text-primary font-medium bg-primary-50' : ''; ?>">Contact</a>
                <a href="<?php echo $base_url; ?>pages/about.php" class="text-text-secondary hover:text-primary transition-micro px-3 py-2 rounded-md hover:bg-secondary-100 <?php echo ($current_page == 'about') ? 'text-primary font-medium bg-primary-50' : ''; ?>">About</a>
                <?php endif; ?>
                <?php if ($canSec): ?>
                <a href="<?php echo $usersHref; ?>" class="text-text-secondary hover:text-primary transition-micro px-3 py-2 rounded-md hover:bg-secondary-100">Users</a>
                <a href="<?php echo $reportsHref; ?>" class="text-text-secondary hover:text-primary transition-micro px-3 py-2 rounded-md hover:bg-secondary-100">Reports</a>
                <?php endif; ?>
                <?php else: ?>
                <a href="<?php echo $base_url; ?>pages/contact.php" class="text-text-secondary hover:text-primary transition-micro px-3 py-2 rounded-md hover:bg-secondary-100 <?php echo ($current_page == 'contact') ? 'text-primary font-medium bg-primary-50' : ''; ?>">Contact</a>
                <a href="<?php echo $base_url; ?>pages/about.php" class="text-text-secondary hover:text-primary transition-micro px-3 py-2 rounded-md hover:bg-secondary-100 <?php echo ($current_page == 'about') ? 'text-primary font-medium bg-primary-50' : ''; ?>">About</a>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
            

            <!-- User Profile & Actions (for dashboard pages) -->
            <?php if ($current_page !== 'login' && $current_page !== 'user_registration'): ?>
            <div class="flex items-center space-x-4">
                <!-- Notifications (for both representatives and secretariat) -->
                <?php if ($canRep || $canSec): ?>
                <?php 
                    $notificationPageUrl = $canSec 
                        ? $base_url . 'pages/is_secretariat/notification_center.php'
                        : $base_url . 'pages/is_representative/notifications.php';
                ?>
                <div class="relative">
                    <button type="button" class="relative p-2 text-text-secondary hover:text-primary transition-micro" onclick="toggleNotificationDropdown()">
                        <i class="fas fa-bell text-lg"></i>
                        <span id="notification-badge" class="absolute -top-1 -right-1 bg-error text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-surface rounded-md shadow-modal border border-secondary-200 z-50 hidden sm:right-0 sm:left-auto left-2">
                        <div class="p-4 border-b border-secondary-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-text-primary">Notifications</h3>
                                <a href="<?php echo $notificationPageUrl; ?>" class="text-sm text-primary hover:text-primary-dark font-medium">
                                    View All
                                </a>
                            </div>
                        </div>
                        <div id="notification-preview" class="max-h-80 overflow-y-auto">
                            <!-- Notification previews will be loaded here -->
                        </div>
                        <div class="p-3 border-t border-secondary-200 text-center">
                            <a href="<?php echo $notificationPageUrl; ?>" class="text-sm text-primary hover:text-primary-dark font-medium">
                                View All Notifications
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- User Menu -->
                <div class="relative">
                    <button type="button" class="flex items-center space-x-2 text-text-secondary hover:text-primary transition-micro" onclick="toggleUserMenu()">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="User Avatar" class="w-8 h-8 rounded-full object-cover" onerror="this.src='https://images.unsplash.com/photo-1584824486509-112e4181ff6b?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'; this.onerror=null;" />
                        <span class="hidden md:block text-sm font-medium"><?php echo htmlspecialchars($displayName); ?></span>
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div id="user-menu" class="absolute right-0 mt-2 w-48 max-w-[calc(100vw-2rem)] bg-surface rounded-md shadow-modal border border-secondary-200 z-50 hidden sm:right-0 sm:left-auto left-2">
                        <div class="py-1">
                            <div class="px-4 py-2 border-b border-secondary-200">
                                <p class="text-sm font-medium text-text-primary"><?php echo htmlspecialchars($displayName); ?></p>
                                <?php if ($displaySubtitle !== ''): ?>
                                <p class="text-xs text-text-secondary"><?php echo htmlspecialchars($displaySubtitle); ?></p>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo $base_url; ?>pages/user_profile_settings.php" class="block px-4 py-2 text-sm text-text-secondary hover:bg-secondary-100 hover:text-primary transition-micro">
                                <i class="fas fa-user mr-2"></i>Profile Settings
                            </a>
                            <?php if ($canSec): ?>
                            <a href="<?php echo $notifHref; ?>" class="block px-4 py-2 text-sm text-text-secondary hover:bg-secondary-100 hover:text-primary transition-micro">
                                <i class="fas fa-cog mr-2"></i>Notification Center
                            </a>
                            <?php endif; ?>
                            <div class="border-t border-secondary-200"></div>
                            <a href="<?php echo $base_url; ?>database/logout.php" class="block px-4 py-2 text-sm text-text-secondary hover:bg-secondary-100 hover:text-primary transition-micro">
                                <i class="fas fa-sign-out-alt mr-2"></i>Sign Out
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button type="button" class="text-text-secondary hover:text-primary focus:outline-none focus:text-primary transition-micro" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Mobile Navigation Menu -->
        <?php if ($current_page !== 'login' && $current_page !== 'user_registration'): ?>
        <div id="mobile-menu" class="md:hidden hidden border-t border-secondary-200 pt-4 pb-3 bg-surface">
            <div class="space-y-1 px-2">
                <?php $canRep = function_exists('is_representative') ? is_representative() : true; ?>
                <?php $canSec = function_exists('is_secretariat') ? is_secretariat() : false; ?>
                <?php
                    $roleDir = $canSec ? 'is_secretariat' : 'is_representative';
                    $activityHref = $base_url . 'pages/' . $roleDir . '/activity_management.php';
                    $repDashHref = $base_url . 'pages/is_representative/representative_dashboard.php';
                    $secDashHref = $base_url . 'pages/is_secretariat/secretariat_dashboard.php';
                    $reportsHref = $base_url . 'pages/is_secretariat/report_generation.php';
                    $usersHref = $base_url . 'pages/is_secretariat/user_management.php';
                    $notifHref = $base_url . 'pages/is_secretariat/notification_center.php';
                ?>
                <?php if ($canRep || $canSec): ?>
                <a href="<?php echo $canSec ? $secDashHref : $repDashHref; ?>" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">Home</a>
                <a href="<?php echo $activityHref; ?>" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">Activities</a>
                <?php if ($canRep && !$canSec): ?>
                <a href="<?php echo $base_url; ?>pages/contact.php" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">Contact</a>
                <a href="<?php echo $base_url; ?>pages/about.php" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">About</a>
                <?php endif; ?>
                <?php if ($canSec): ?>
                <a href="<?php echo $repDashHref; ?>" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">Representative</a>
                <a href="<?php echo $usersHref; ?>" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">Users</a>
                <a href="<?php echo $reportsHref; ?>" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">Reports</a>
                <a href="<?php echo $secDashHref; ?>" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">Secretariat</a>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div id="mobile-menu" class="md:hidden hidden border-t border-secondary-200 pt-4 pb-3 bg-surface">
            <div class="space-y-1 px-2">
                <a href="<?php echo $base_url; ?>pages/contact.php" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">Contact</a>
                <a href="<?php echo $base_url; ?>pages/about.php" class="block px-3 py-2 text-text-secondary hover:text-primary hover:bg-secondary-100 rounded-md transition-micro">About</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</header>

<script>
// Mobile menu and user dropdown functionality
function toggleMobileMenu() {
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenu) {
        mobileMenu.classList.toggle('hidden');
    }
}

function toggleUserMenu() {
    const userMenu = document.getElementById('user-menu');
    if (userMenu) {
        userMenu.classList.toggle('hidden');
    }
}

function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notification-dropdown');
    if (dropdown) {
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            loadNotificationPreview();
        }
    }
}

// Global variable to store navbar notifications
let navbarNotifications = [];

// Fetch notifications from API for navbar
async function fetchNavbarNotifications() {
    try {
        const response = await fetch('/central-cmi/api/notifications.php');
        const data = await response.json();
        
        if (data.success) {
            navbarNotifications = data.notifications;
            updateNavbarBadge(data.stats.unread);
            return data;
        }
    } catch (error) {
        console.error('Error fetching navbar notifications:', error);
    }
    return null;
}

// Update the notification badge in navbar
function updateNavbarBadge(count) {
    const badge = document.getElementById('notification-badge');
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
}

// Load notification preview in dropdown
async function loadNotificationPreview() {
    const preview = document.getElementById('notification-preview');
    if (!preview) return;

    // Show loading state
    preview.innerHTML = '<div class="p-4 text-center"><i class="fas fa-spinner fa-spin text-primary"></i></div>';

    // Fetch fresh data if not already loaded
    if (navbarNotifications.length === 0) {
        await fetchNavbarNotifications();
    }

    const typeIcons = {
        'general': 'fas fa-bell',
        'deadline': 'fas fa-clock',
        'approval': 'fas fa-check-circle',
        'meeting': 'fas fa-users',
        'system': 'fas fa-cog',
        'report': 'fas fa-file-alt'
    };

    // Get first 3 notifications
    const displayNotifications = navbarNotifications.slice(0, 3);

    if (displayNotifications.length === 0) {
        preview.innerHTML = `
            <div class="p-6 text-center">
                <i class="fas fa-bell-slash text-3xl text-secondary-300 mb-2"></i>
                <p class="text-sm text-text-secondary">No new notifications</p>
            </div>
        `;
        return;
    }

    preview.innerHTML = displayNotifications.map(notification => {
        const receivedDate = new Date(notification.created_at);
        const isRead = notification.is_read == 1;
        const senderName = notification.firstName && notification.lastName 
            ? `${notification.firstName} ${notification.lastName}` 
            : 'System';
        const icon = typeIcons[notification.type] || 'fas fa-bell';
        
        return `
            <div class="p-3 hover:bg-secondary-50 cursor-pointer border-b border-secondary-100 last:border-b-0 ${!isRead ? 'bg-primary-50' : ''}" onclick="viewNotificationFromDropdown(${notification.NotificationID})">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-3">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                            <i class="${icon} text-primary text-sm"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-sm font-semibold text-text-primary truncate">${escapeHtml(notification.subject)}</h4>
                            ${!isRead ? '<span class="bg-primary text-white text-xs rounded-full px-1.5 py-0.5">New</span>' : ''}
                        </div>
                        <p class="text-xs text-text-secondary mb-1">From: ${escapeHtml(senderName)}</p>
                        <p class="text-xs text-text-secondary line-clamp-2">${escapeHtml(notification.content)}</p>
                        <p class="text-xs text-text-secondary mt-1">${receivedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Helper function to escape HTML
function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// Initialize navbar notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchNavbarNotifications();
});

function viewNotificationFromDropdown(id) {
    // Close dropdown and redirect to notifications page
    document.getElementById('notification-dropdown').classList.add('hidden');
    <?php if (isset($notificationPageUrl)): ?>
    window.location.href = '<?php echo $notificationPageUrl; ?>';
    <?php else: ?>
    window.location.href = '<?php echo $canSec ? $notifHref : $base_url . "pages/is_representative/notifications.php"; ?>';
    <?php endif; ?>
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('user-menu');
    const userButton = event.target.closest('[onclick="toggleUserMenu()"]');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationButton = event.target.closest('[onclick="toggleNotificationDropdown()"]');
    
    if (userMenu && !userButton && !userMenu.contains(event.target)) {
        userMenu.classList.add('hidden');
    }
    
    // Only handle notification dropdown if it exists (representatives only)
    if (notificationDropdown && !notificationButton && !notificationDropdown.contains(event.target)) {
        notificationDropdown.classList.add('hidden');
    }
});

// Mobile-specific touch handling
document.addEventListener('touchstart', function(event) {
    // Close dropdowns on touch outside
    const userMenu = document.getElementById('user-menu');
    const userButton = event.target.closest('[onclick="toggleUserMenu()"]');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationButton = event.target.closest('[onclick="toggleNotificationDropdown()"]');
    
    if (userMenu && !userButton && !userMenu.contains(event.target)) {
        userMenu.classList.add('hidden');
    }
    
    // Only handle notification dropdown if it exists (representatives only)
    if (notificationDropdown && !notificationButton && !notificationDropdown.contains(event.target)) {
        notificationDropdown.classList.add('hidden');
    }
});

// Handle window resize for mobile responsiveness
window.addEventListener('resize', function() {
    const userMenu = document.getElementById('user-menu');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const mobileMenu = document.getElementById('mobile-menu');
    
    // Close dropdowns on resize to prevent layout issues
    if (userMenu) userMenu.classList.add('hidden');
    if (notificationDropdown) notificationDropdown.classList.add('hidden');
    
    // Close mobile menu on resize to desktop
    if (window.innerWidth >= 768 && mobileMenu) {
        mobileMenu.classList.add('hidden');
    }
});
</script>

<style>
/* Mobile-specific styles for better responsiveness */
@media (max-width: 767px) {
    /* Ensure dropdowns don't overflow on mobile */
    #notification-dropdown,
    #user-menu {
        max-width: calc(100vw - 1rem);
        right: 0.5rem;
        left: 0.5rem;
        width: auto;
    }
    
    /* Improve mobile menu spacing */
    #mobile-menu {
        max-height: calc(100vh - 4rem);
        overflow-y: auto;
    }
    
    /* Better touch targets for mobile */
    #mobile-menu a {
        padding: 0.75rem 1rem;
        min-height: 44px;
        display: flex;
        align-items: center;
    }
    
    /* Improve notification preview on mobile */
    #notification-preview {
        max-height: 60vh;
    }
    
    /* Better spacing for mobile dropdowns */
    #notification-dropdown .p-4,
    #user-menu .py-1 {
        padding: 0.75rem;
    }
    
    /* Ensure proper text wrapping on mobile */
    .truncate {
        white-space: normal;
        word-wrap: break-word;
    }
}

/* Tablet-specific adjustments */
@media (min-width: 768px) and (max-width: 1023px) {
    #notification-dropdown {
        width: 20rem;
        max-width: calc(100vw - 2rem);
    }
    
    #user-menu {
        width: 12rem;
        max-width: calc(100vw - 2rem);
    }
}

/* Prevent horizontal scroll on mobile */
@media (max-width: 767px) {
    .max-w-7xl {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}
</style>