<?php
require_once __DIR__ . '/../../database/auth.php';
require_role(['representative', 'secretariat']);
?>
<?php
$pageTitle = "Notifications - Central CMI";
$bodyClass = "bg-background";
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-text-primary">Notifications</h1>
                <p class="text-text-secondary mt-2">Stay updated with important announcements and updates</p>
            </div>
            <div class="flex space-x-3 mt-4 sm:mt-0">
                <button type="button" class="btn-secondary" onclick="markAllAsRead()">
                    <i class="fas fa-check-double mr-2"></i>
                    Mark All as Read
                </button>
                <button type="button" class="btn-primary" onclick="refreshNotifications()">
                    <i class="fas fa-sync mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6 mb-6">
            <div class="flex flex-wrap gap-2">
                <button type="button" class="notification-filter active" data-filter="all" onclick="filterNotifications('all')">
                    <i class="fas fa-list mr-2"></i>
                    All Notifications
                    <span class="ml-2 bg-primary text-white text-xs rounded-full px-2 py-1" id="all-count">0</span>
                </button>
                <button type="button" class="notification-filter" data-filter="unread" onclick="filterNotifications('unread')">
                    <i class="fas fa-envelope mr-2"></i>
                    Unread
                    <span class="ml-2 bg-warning text-white text-xs rounded-full px-2 py-1" id="unread-count">0</span>
                </button>
                <button type="button" class="notification-filter" data-filter="announcements" onclick="filterNotifications('announcements')">
                    <i class="fas fa-bullhorn mr-2"></i>
                    Announcements
                </button>
                <button type="button" class="notification-filter" data-filter="activities" onclick="filterNotifications('activities')">
                    <i class="fas fa-tasks mr-2"></i>
                    Activities
                </button>
                <button type="button" class="notification-filter" data-filter="system" onclick="filterNotifications('system')">
                    <i class="fas fa-cog mr-2"></i>
                    System
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="space-y-4" id="notifications-container">
            <!-- Sample notifications will be loaded here -->
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="text-center py-12 hidden">
            <div class="bg-surface rounded-xl shadow-card border border-secondary-200 p-12">
                <i class="fas fa-bell-slash text-6xl text-secondary-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-text-primary mb-2">No Notifications</h3>
                <p class="text-text-secondary mb-6">You don't have any notifications at the moment.</p>
                <button type="button" class="btn-primary" onclick="refreshNotifications()">
                    <i class="fas fa-sync mr-2"></i>
                    Refresh Notifications
                </button>
            </div>
        </div>
    </main>

    <!-- Notification Details Modal -->
    <div id="notification-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-surface rounded-lg shadow-modal max-w-2xl w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-text-primary">Notification Details</h3>
                    <button type="button" class="text-secondary-400 hover:text-secondary-600" onclick="closeNotificationModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="notification-content" class="space-y-4">
                    <!-- Notification details will be populated here -->
                </div>
                
                <div class="flex gap-3 pt-4 border-t border-secondary-200 mt-6">
                    <button type="button" class="flex-1 btn-primary" onclick="markAsRead()">
                        <i class="fas fa-check mr-2"></i>
                        Mark as Read
                    </button>
                    <button type="button" class="flex-1 btn-secondary" onclick="closeNotificationModal()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '/central-cmi/api/notifications.php';
        let notifications = [];
        let currentFilter = 'all';
        let currentNotification = null;

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            fetchNotifications();
        });

        // Fetch notifications from database API
        async function fetchNotifications() {
            try {
                const response = await fetch(API_URL);
                const data = await response.json();
                
                if (data.success) {
                    // Transform API data to match expected format
                    notifications = data.notifications.map(n => ({
                        id: n.NotificationID,
                        from: n.firstName && n.lastName ? `${n.firstName} ${n.lastName}` : 'System',
                        subject: n.subject,
                        content: n.content,
                        type: mapNotificationType(n.type),
                        dateReceived: n.created_at,
                        dateSent: n.sent_at || n.created_at,
                        isRead: n.is_read == 1,
                        priority: n.priority || 'medium'
                    }));
                    loadNotifications();
                    updateNotificationCounts();
                } else {
                    console.error('Failed to load notifications:', data.error);
                    showNotification('Failed to load notifications', 'error');
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
                showNotification('Error loading notifications', 'error');
            }
        }

        // Map API notification type to UI categories
        function mapNotificationType(type) {
            const typeMap = {
                'deadline': 'activities',
                'approval': 'activities',
                'meeting': 'announcements',
                'system': 'system',
                'report': 'activities',
                'general': 'announcements'
            };
            return typeMap[type] || 'announcements';
        }

        function loadNotifications() {
            const container = document.getElementById('notifications-container');
            const emptyState = document.getElementById('empty-state');
            
            let filteredNotifications = notifications;
            
            if (currentFilter !== 'all') {
                filteredNotifications = notifications.filter(notification => {
                    if (currentFilter === 'unread') {
                        return !notification.isRead;
                    }
                    return notification.type === currentFilter;
                });
            }

            if (filteredNotifications.length === 0) {
                container.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            
            container.innerHTML = filteredNotifications.map(notification => {
                const receivedDate = new Date(notification.dateReceived);
                const sentDate = new Date(notification.dateSent);
                
                const priorityColors = {
                    'high': 'border-l-error bg-error-50',
                    'medium': 'border-l-warning bg-warning-50',
                    'low': 'border-l-primary bg-primary-50'
                };

                const typeIcons = {
                    'announcements': 'fas fa-bullhorn',
                    'activities': 'fas fa-tasks',
                    'system': 'fas fa-cog'
                };

                const priorityIcons = {
                    'high': 'fas fa-exclamation-circle text-error',
                    'medium': 'fas fa-exclamation-triangle text-warning',
                    'low': 'fas fa-info-circle text-primary'
                };

                return `
                    <div class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6 hover:shadow-lg transition-all duration-200 cursor-pointer ${!notification.isRead ? 'ring-2 ring-primary-200' : ''}" onclick="viewNotification(${notification.id})">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="${typeIcons[notification.type]} text-primary text-lg"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <h3 class="text-lg font-semibold text-text-primary truncate">${notification.subject}</h3>
                                        ${!notification.isRead ? '<span class="bg-primary text-white text-xs rounded-full px-2 py-1">New</span>' : ''}
                                        <i class="${priorityIcons[notification.priority]} text-sm"></i>
                                    </div>
                                    <span class="text-sm text-text-secondary">${receivedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                                </div>
                                <p class="text-sm text-text-secondary mb-2">From: <span class="font-medium text-text-primary">${notification.from}</span></p>
                                <p class="text-sm text-text-secondary line-clamp-2">${notification.content}</p>
                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-xs text-text-secondary">Received: ${receivedDate.toLocaleString('en-US', { 
                                        month: 'short', 
                                        day: 'numeric', 
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}</span>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs px-2 py-1 rounded-full ${priorityColors[notification.priority]}">
                                            ${notification.priority.charAt(0).toUpperCase() + notification.priority.slice(1)} Priority
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function filterNotifications(filter) {
            currentFilter = filter;
            
            // Update filter buttons
            document.querySelectorAll('.notification-filter').forEach(btn => {
                btn.classList.remove('active', 'bg-primary', 'text-white');
                btn.classList.add('bg-secondary-100', 'text-text-secondary');
            });
            
            const activeBtn = document.querySelector(`[data-filter="${filter}"]`);
            activeBtn.classList.add('active', 'bg-primary', 'text-white');
            activeBtn.classList.remove('bg-secondary-100', 'text-text-secondary');
            
            loadNotifications();
        }

        function viewNotification(id) {
            const notification = notifications.find(n => n.id === id);
            if (!notification) return;

            currentNotification = notification;
            
            const modal = document.getElementById('notification-modal');
            const content = document.getElementById('notification-content');
            
            const receivedDate = new Date(notification.dateReceived);
            const sentDate = new Date(notification.dateSent);
            
            const typeIcons = {
                'announcements': 'fas fa-bullhorn',
                'activities': 'fas fa-tasks',
                'system': 'fas fa-cog'
            };

            const priorityColors = {
                'high': 'text-error',
                'medium': 'text-warning',
                'low': 'text-primary'
            };

            content.innerHTML = `
                <div class="flex items-start mb-4">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                        <i class="${typeIcons[notification.type]} text-primary text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xl font-semibold text-text-primary mb-1">${notification.subject}</h4>
                        <p class="text-text-secondary">From: <span class="font-medium text-text-primary">${notification.from}</span></p>
                        <p class="text-sm ${priorityColors[notification.priority]}">
                            <i class="fas fa-flag mr-1"></i>
                            ${notification.priority.charAt(0).toUpperCase() + notification.priority.slice(1)} Priority
                        </p>
                    </div>
                </div>
                
                <div class="bg-secondary-50 rounded-lg p-4 mb-4">
                    <h5 class="font-medium text-text-primary mb-2">Message Content:</h5>
                    <p class="text-text-secondary leading-relaxed">${notification.content}</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <h6 class="font-medium text-text-primary mb-1">Date Received:</h6>
                        <p class="text-text-secondary">${receivedDate.toLocaleString('en-US', { 
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                    </div>
                    <div>
                        <h6 class="font-medium text-text-primary mb-1">Date Sent:</h6>
                        <p class="text-text-secondary">${sentDate.toLocaleString('en-US', { 
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
            
            // Mark as read when viewed via API
            if (!notification.isRead) {
                markNotificationRead(notification.id, true);
            }
        }

        function closeNotificationModal() {
            document.getElementById('notification-modal').classList.add('hidden');
            currentNotification = null;
        }

        // Mark single notification as read via API
        async function markNotificationRead(id, isRead) {
            try {
                const response = await fetch(API_URL, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id, is_read: isRead })
                });
                
                const result = await response.json();
                if (result.success) {
                    // Update local data
                    const notification = notifications.find(n => n.id == id);
                    if (notification) {
                        notification.isRead = isRead;
                        updateNotificationCounts();
                        loadNotifications();
                    }
                }
            } catch (error) {
                console.error('Error updating notification:', error);
            }
        }

        function markAsRead() {
            if (currentNotification && !currentNotification.isRead) {
                markNotificationRead(currentNotification.id, true);
                closeNotificationModal();
                showNotification('Notification marked as read', 'success');
            }
        }

        async function markAllAsRead() {
            try {
                const response = await fetch(API_URL, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ markAllRead: true })
                });
                
                const result = await response.json();
                if (result.success) {
                    notifications.forEach(n => n.isRead = true);
                    updateNotificationCounts();
                    loadNotifications();
                    showNotification('All notifications marked as read', 'success');
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
                showNotification('Error updating notifications', 'error');
            }
        }

        function refreshNotifications() {
            fetchNotifications();
            showNotification('Notifications refreshed', 'success');
        }

        function updateNotificationCounts() {
            const allCount = notifications.length;
            const unreadCount = notifications.filter(n => !n.isRead).length;
            
            document.getElementById('all-count').textContent = allCount;
            document.getElementById('unread-count').textContent = unreadCount;
            
            // Update navbar notification badge using navbar's function if available
            if (typeof updateNavbarBadge === 'function') {
                updateNavbarBadge(unreadCount);
            } else {
                // Fallback: directly update badge
                const navbarBadge = document.getElementById('notification-badge');
                if (navbarBadge) {
                    navbarBadge.textContent = unreadCount;
                    if (unreadCount === 0) {
                        navbarBadge.classList.add('hidden');
                    } else {
                        navbarBadge.classList.remove('hidden');
                    }
                }
            }
            
            // Also update navbar's cached notifications
            if (typeof navbarNotifications !== 'undefined') {
                navbarNotifications = notifications.map(n => ({
                    NotificationID: n.id,
                    subject: n.subject,
                    content: n.content,
                    type: n.type,
                    created_at: n.dateReceived,
                    is_read: n.isRead ? 1 : 0,
                    firstName: n.from.split(' ')[0],
                    lastName: n.from.split(' ').slice(1).join(' ')
                }));
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg transition-all duration-300 transform translate-x-full`;
            
            const colors = {
                success: 'bg-success-100 text-success-700 border border-success-200',
                error: 'bg-error-100 text-error-700 border border-error-200',
                warning: 'bg-warning-100 text-warning-700 border border-warning-200',
                info: 'bg-primary-100 text-primary-700 border border-primary-200'
            };
            
            notification.className += ` ${colors[type]}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-current opacity-70 hover:opacity-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    </script>

    <style>
        .notification-filter {
            @apply px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer;
        }
        
        .notification-filter.active {
            @apply bg-primary text-white;
        }
        
        .notification-filter:not(.active) {
            @apply bg-secondary-100 text-text-secondary hover:bg-secondary-200;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

<?php include '../../includes/footer.php'; ?>
