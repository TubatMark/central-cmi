<?php
$pageTitle = "Notification Center - Central CMI";
$bodyClass = "bg-background min-h-screen";
require_once __DIR__ . '/../../database/auth.php';
require_role(['representative', 'secretariat']);
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <section class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-text-primary">Notification Center</h1>
                <p class="mt-2 text-text-secondary">Stay updated with important alerts and messages</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button type="button" onclick="openSendNotificationModal()" class="btn-secondary">
                    <i class="fas fa-bullhorn mr-2"></i>Send Notification
                </button>
                <button type="button" onclick="markAllAsRead()" class="btn-secondary">
                    <i class="fas fa-check-double mr-2"></i>Mark All Read
                </button>
            </div>
        </div>
    </section>

    <!-- Notification Statistics -->
    <section class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bell text-primary"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-secondary">Total</p>
                        <p class="text-2xl font-semibold text-text-primary" id="stat-total">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-warning-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-warning"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-secondary">Unread</p>
                        <p class="text-2xl font-semibold text-text-primary" id="stat-unread">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-error-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-flag text-error"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-secondary">High Priority</p>
                        <p class="text-2xl font-semibold text-text-primary" id="stat-high-priority">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-success-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-week text-success"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-text-secondary">This Week</p>
                        <p class="text-2xl font-semibold text-text-primary" id="stat-this-week">0</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters and Search -->
    <section class="mb-8">
        <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-text-secondary"></i>
                        </div>
                        <input type="text" id="search-notifications" placeholder="Search notifications..." 
                               class="block w-full pl-10 pr-3 py-2 border border-secondary-300 rounded-md leading-5 bg-surface placeholder:text-text-secondary focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary"
                               oninput="filterNotifications()">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                    <select id="type-filter" onchange="filterNotifications()" class="form-select">
                        <option value="">All Types</option>
                        <option value="deadline">Deadlines</option>
                        <option value="approval">Approvals</option>
                        <option value="meeting">Meetings</option>
                        <option value="system">System</option>
                        <option value="report">Reports</option>
                        <option value="general">General</option>
                    </select>
                    <select id="priority-filter" onchange="filterNotifications()" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button" onclick="setQuickFilter('all')" data-filter="all" class="quick-filter-btn px-3 py-1 text-sm rounded-full bg-primary text-white">All</button>
                <button type="button" onclick="setQuickFilter('unread')" data-filter="unread" class="quick-filter-btn px-3 py-1 text-sm rounded-full bg-secondary-100 text-secondary-700 hover:bg-secondary-200">Unread</button>
                <button type="button" onclick="setQuickFilter('high')" data-filter="high" class="quick-filter-btn px-3 py-1 text-sm rounded-full bg-secondary-100 text-secondary-700 hover:bg-secondary-200">High Priority</button>
            </div>
        </div>
    </section>

    <!-- Tabs -->
    <section class="mb-4">
        <div class="bg-surface rounded-lg shadow-card border border-secondary-200 px-6 py-4">
            <div class="flex items-center space-x-2">
                <button id="tab-received" type="button" class="px-4 py-2 rounded-md text-sm font-medium bg-primary text-white" onclick="switchTab('received')">
                    Received
                </button>
                <button id="tab-sent" type="button" class="px-4 py-2 rounded-md text-sm font-medium bg-secondary-100 text-secondary-700 hover:bg-secondary-200" onclick="switchTab('sent')">
                    Sent
                </button>
            </div>
        </div>
    </section>

    <!-- Received Notifications -->
    <section id="received-section">
        <div class="bg-surface rounded-lg shadow-card border border-secondary-200">
            <div class="px-6 py-4 border-b border-secondary-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-text-primary">Notifications</h2>
                    <span id="results-count" class="text-sm text-text-secondary">0 notifications</span>
                </div>
            </div>
            <div id="notifications-list" class="divide-y divide-secondary-200">
                <div class="p-8 text-center text-text-secondary">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading notifications...
                </div>
            </div>
        </div>
    </section>

    <!-- Sent Notifications -->
    <section id="sent-section" class="hidden">
        <div class="bg-surface rounded-lg shadow-card border border-secondary-200">
            <div class="px-6 py-4 border-b border-secondary-200">
                <h2 class="text-lg font-medium text-text-primary">Sent Notifications</h2>
            </div>
            <div id="sent-notifications-list" class="divide-y divide-secondary-200">
                <div class="p-8 text-center text-text-secondary">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Send Notification Modal -->
<div id="send-notification-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-surface rounded-lg shadow-modal max-w-2xl w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-text-primary">Send Notification</h3>
            <button type="button" onclick="closeSendNotificationModal()" class="text-text-secondary hover:text-primary">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="send-notification-form" onsubmit="return sendNotification(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">Recipients <span class="text-error">*</span></label>
                    <select id="notif-recipient" class="form-input" required>
                        <option value="all">All Users</option>
                        <option value="representatives">Representatives Only</option>
                        <option value="secretariat">Secretariat Only</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">Type</label>
                        <select id="notif-type" class="form-input">
                            <option value="general">General</option>
                            <option value="deadline">Deadline</option>
                            <option value="approval">Approval</option>
                            <option value="meeting">Meeting</option>
                            <option value="system">System</option>
                            <option value="report">Report</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">Priority</label>
                        <select id="notif-priority" class="form-input">
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">Subject <span class="text-error">*</span></label>
                    <input type="text" id="notif-subject" class="form-input" placeholder="Enter subject" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-text-primary mb-2">Message <span class="text-error">*</span></label>
                    <textarea id="notif-content" rows="4" class="form-input" placeholder="Enter your message" required></textarea>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" class="btn-secondary" onclick="closeSendNotificationModal()">Cancel</button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i>Send
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const API_URL = BASE_URL + 'api/notifications.php';
    let allNotifications = [];
    let currentQuickFilter = 'all';

    // Load notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();
    });

    // Load received notifications
    async function loadNotifications() {
        try {
            const response = await fetch(API_URL);
            const data = await response.json();
            
            if (data.success) {
                allNotifications = data.notifications;
                updateStats(data.stats);
                renderNotifications(allNotifications);
            } else {
                showError('Failed to load notifications');
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Error loading notifications');
        }
    }

    // Load sent notifications
    async function loadSentNotifications() {
        try {
            const response = await fetch(API_URL + '?sent=1');
            const data = await response.json();
            
            if (data.success) {
                renderSentNotifications(data.notifications);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Update statistics
    function updateStats(stats) {
        document.getElementById('stat-total').textContent = stats.total;
        document.getElementById('stat-unread').textContent = stats.unread;
        document.getElementById('stat-high-priority').textContent = stats.highPriority;
        document.getElementById('stat-this-week').textContent = stats.thisWeek;
    }

    // Render notifications list
    function renderNotifications(notifications) {
        const container = document.getElementById('notifications-list');
        
        if (notifications.length === 0) {
            container.innerHTML = `
                <div class="p-8 text-center">
                    <i class="fas fa-bell-slash text-4xl text-secondary-300 mb-4"></i>
                    <p class="text-text-secondary">No notifications found</p>
                </div>
            `;
            document.getElementById('results-count').textContent = '0 notifications';
            return;
        }
        
        container.innerHTML = notifications.map(notif => {
            const isRead = notif.is_read == 1;
            const priorityClass = getPriorityClass(notif.priority);
            const typeClass = getTypeClass(notif.type);
            const timeAgo = getTimeAgo(notif.created_at);
            const senderName = notif.firstName && notif.lastName 
                ? `${notif.firstName} ${notif.lastName}` 
                : 'System';
            
            return `
                <div class="notification-item p-6 hover:bg-secondary-50 ${isRead ? 'opacity-75' : ''}" 
                     data-id="${notif.NotificationID}" data-type="${notif.type}" data-priority="${notif.priority}" data-read="${isRead}">
                    <div class="flex items-start space-x-4">
                        <div class="w-3 h-3 ${isRead ? 'bg-secondary-300' : 'bg-primary'} rounded-full mt-2 flex-shrink-0"></div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        ${notif.priority === 'high' ? `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${priorityClass}"><i class="fas fa-flag mr-1"></i>High Priority</span>` : ''}
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${typeClass}">
                                            <i class="fas ${getTypeIcon(notif.type)} mr-1"></i>${capitalizeFirst(notif.type)}
                                        </span>
                                    </div>
                                    <h3 class="text-sm font-medium text-text-primary">${escapeHtml(notif.subject)}</h3>
                                    <p class="text-sm text-text-secondary mt-1">${escapeHtml(notif.content)}</p>
                                    <div class="flex items-center space-x-4 mt-3 text-xs text-text-secondary">
                                        <span><i class="fas fa-user mr-1"></i>${escapeHtml(senderName)}</span>
                                        <span><i class="fas fa-users mr-1"></i>${getRecipientLabel(notif.recipient)}</span>
                                        <span><i class="fas fa-clock mr-1"></i>${timeAgo}</span>
                                    </div>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <button type="button" class="text-primary hover:text-primary-700" title="${isRead ? 'Mark as unread' : 'Mark as read'}" onclick="toggleRead(${notif.NotificationID}, ${isRead})">
                                        <i class="fas fa-${isRead ? 'eye-slash' : 'eye'}"></i>
                                    </button>
                                    <button type="button" class="text-error hover:text-error-700" title="Delete" onclick="deleteNotification(${notif.NotificationID})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
        
        document.getElementById('results-count').textContent = `${notifications.length} notification${notifications.length !== 1 ? 's' : ''}`;
    }

    // Render sent notifications
    function renderSentNotifications(notifications) {
        const container = document.getElementById('sent-notifications-list');
        
        if (notifications.length === 0) {
            container.innerHTML = `
                <div class="p-8 text-center">
                    <i class="fas fa-paper-plane text-4xl text-secondary-300 mb-4"></i>
                    <p class="text-text-secondary">No sent notifications</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = notifications.map(notif => `
            <div class="p-6 hover:bg-secondary-50">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary">
                                <i class="fas fa-paper-plane mr-1"></i>Sent
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-secondary-100 text-secondary-700">
                                <i class="fas fa-users mr-1"></i>${getRecipientLabel(notif.recipient)}
                            </span>
                        </div>
                        <h3 class="text-sm font-medium text-text-primary">${escapeHtml(notif.subject)}</h3>
                        <p class="text-sm text-text-secondary mt-1">${escapeHtml(notif.content)}</p>
                        <div class="flex items-center space-x-4 mt-3 text-xs text-text-secondary">
                            <span><i class="fas fa-calendar mr-1"></i>${formatDate(notif.created_at)}</span>
                        </div>
                    </div>
                    <div class="ml-4 text-xs text-success">
                        <i class="fas fa-check mr-1"></i>Delivered
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Filter notifications
    function filterNotifications() {
        const search = document.getElementById('search-notifications').value.toLowerCase();
        const typeFilter = document.getElementById('type-filter').value;
        const priorityFilter = document.getElementById('priority-filter').value;
        
        let filtered = allNotifications.filter(notif => {
            let match = true;
            
            if (search) {
                match = match && (notif.subject.toLowerCase().includes(search) || notif.content.toLowerCase().includes(search));
            }
            if (typeFilter) {
                match = match && notif.type === typeFilter;
            }
            if (priorityFilter) {
                match = match && notif.priority === priorityFilter;
            }
            if (currentQuickFilter === 'unread') {
                match = match && notif.is_read == 0;
            }
            if (currentQuickFilter === 'high') {
                match = match && notif.priority === 'high';
            }
            
            return match;
        });
        
        renderNotifications(filtered);
    }

    // Quick filter
    function setQuickFilter(filter) {
        currentQuickFilter = filter;
        
        document.querySelectorAll('.quick-filter-btn').forEach(btn => {
            if (btn.dataset.filter === filter) {
                btn.classList.remove('bg-secondary-100', 'text-secondary-700');
                btn.classList.add('bg-primary', 'text-white');
            } else {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-secondary-100', 'text-secondary-700');
            }
        });
        
        filterNotifications();
    }

    // Switch tabs
    function switchTab(tab) {
        const receivedBtn = document.getElementById('tab-received');
        const sentBtn = document.getElementById('tab-sent');
        const receivedSection = document.getElementById('received-section');
        const sentSection = document.getElementById('sent-section');
        
        if (tab === 'received') {
            receivedBtn.classList.add('bg-primary', 'text-white');
            receivedBtn.classList.remove('bg-secondary-100', 'text-secondary-700');
            sentBtn.classList.remove('bg-primary', 'text-white');
            sentBtn.classList.add('bg-secondary-100', 'text-secondary-700');
            receivedSection.classList.remove('hidden');
            sentSection.classList.add('hidden');
        } else {
            sentBtn.classList.add('bg-primary', 'text-white');
            sentBtn.classList.remove('bg-secondary-100', 'text-secondary-700');
            receivedBtn.classList.remove('bg-primary', 'text-white');
            receivedBtn.classList.add('bg-secondary-100', 'text-secondary-700');
            sentSection.classList.remove('hidden');
            receivedSection.classList.add('hidden');
            loadSentNotifications();
        }
    }

    // Toggle read status
    async function toggleRead(id, currentlyRead) {
        try {
            const response = await fetch(API_URL, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, is_read: !currentlyRead })
            });
            
            const result = await response.json();
            if (result.success) {
                loadNotifications();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Mark all as read
    async function markAllAsRead() {
        try {
            const response = await fetch(API_URL, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ markAllRead: true })
            });
            
            const result = await response.json();
            if (result.success) {
                showToast('All notifications marked as read', 'success');
                loadNotifications();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Delete notification
    async function deleteNotification(id) {
        if (!confirm('Are you sure you want to delete this notification?')) return;
        
        try {
            const response = await fetch(`${API_URL}?id=${id}`, { method: 'DELETE' });
            const result = await response.json();
            
            if (result.success) {
                showToast('Notification deleted', 'success');
                loadNotifications();
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Send notification
    async function sendNotification(e) {
        e.preventDefault();
        
        const data = {
            recipient: document.getElementById('notif-recipient').value,
            type: document.getElementById('notif-type').value,
            priority: document.getElementById('notif-priority').value,
            subject: document.getElementById('notif-subject').value,
            content: document.getElementById('notif-content').value
        };
        
        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                closeSendNotificationModal();
                showToast('Notification sent successfully', 'success');
                document.getElementById('send-notification-form').reset();
            } else {
                showToast(result.error || 'Failed to send notification', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error sending notification', 'error');
        }
        
        return false;
    }

    // Modal functions
    function openSendNotificationModal() {
        document.getElementById('send-notification-modal').classList.remove('hidden');
    }

    function closeSendNotificationModal() {
        document.getElementById('send-notification-modal').classList.add('hidden');
    }

    // Helper functions
    function getPriorityClass(priority) {
        const classes = {
            high: 'bg-error-100 text-error',
            medium: 'bg-warning-100 text-warning',
            low: 'bg-secondary-100 text-secondary-700'
        };
        return classes[priority] || classes.medium;
    }

    function getTypeClass(type) {
        const classes = {
            deadline: 'bg-warning-100 text-warning',
            approval: 'bg-success-100 text-success',
            meeting: 'bg-accent-100 text-accent',
            system: 'bg-secondary-100 text-secondary-700',
            report: 'bg-primary-100 text-primary',
            general: 'bg-secondary-100 text-secondary-700'
        };
        return classes[type] || classes.general;
    }

    function getTypeIcon(type) {
        const icons = {
            deadline: 'fa-clock',
            approval: 'fa-check-circle',
            meeting: 'fa-users',
            system: 'fa-cog',
            report: 'fa-file-alt',
            general: 'fa-bell'
        };
        return icons[type] || icons.general;
    }

    function getRecipientLabel(recipient) {
        const labels = {
            all: 'All Users',
            representatives: 'Representatives',
            secretariat: 'Secretariat'
        };
        return labels[recipient] || recipient;
    }

    function getTimeAgo(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);
        
        if (diff < 60) return 'Just now';
        if (diff < 3600) return `${Math.floor(diff / 60)} min ago`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
        if (diff < 604800) return `${Math.floor(diff / 86400)} days ago`;
        return formatDate(dateStr);
    }

    function formatDate(dateStr) {
        return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function showError(message) {
        document.getElementById('notifications-list').innerHTML = `
            <div class="p-8 text-center text-error">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p>${message}</p>
                <button onclick="loadNotifications()" class="mt-2 text-primary hover:text-primary-700">Retry</button>
            </div>
        `;
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-success' : type === 'error' ? 'bg-error' : 'bg-primary'} text-white`;
        toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
</script>

<?php include '../../includes/footer.php'; ?>
