<?php
$pageTitle = "Secretariat Dashboard - Central CMI";
$bodyClass = "bg-background min-h-screen";
require_once __DIR__ . '/../../database/auth.php';
require_role(['secretariat']);
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Dashboard Header -->
        <section class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-text-primary">Secretariat Dashboard</h1>
                    <p class="text-text-secondary mt-2">System-wide oversight and administrative management</p>
                </div>
            </div>
        </section>

        <!-- System Statistics Cards -->
        <section class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Activities Card -->
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-text-secondary">Total Activities</p>
                            <p id="stat-total-activities" class="text-3xl font-bold text-text-primary mt-2">--</p>
                            <div class="flex items-center mt-2">
                                <span id="stat-completed" class="text-success text-sm font-medium">0 completed</span>
                            </div>
                        </div>
                        <div class="bg-primary-100 p-3 rounded-full">
                            <i class="fas fa-tasks text-primary text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Users Card -->
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-text-secondary">Total Users</p>
                            <p id="stat-total-users" class="text-3xl font-bold text-text-primary mt-2">--</p>
                            <div class="flex items-center mt-2">
                                <span class="text-text-secondary text-sm">Registered users</span>
                            </div>
                        </div>
                        <div class="bg-accent-100 p-3 rounded-full">
                            <i class="fas fa-users text-accent text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending Reports Card -->
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-text-secondary">Pending Activities</p>
                            <p id="stat-pending" class="text-3xl font-bold text-text-primary mt-2">--</p>
                            <div class="flex items-center mt-2">
                                <span id="stat-overdue" class="text-warning text-sm font-medium">0 overdue</span>
                            </div>
                        </div>
                        <div class="bg-warning-100 p-3 rounded-full">
                            <i class="fas fa-file-alt text-warning text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Notification Delivery Rate Card -->
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-text-secondary">Delivery Rate</p>
                            <p id="stat-delivery-rate" class="text-3xl font-bold text-text-primary mt-2">--%</p>
                            <div class="flex items-center mt-2">
                                <span class="text-text-secondary text-sm">Notifications sent</span>
                            </div>
                        </div>
                        <div class="bg-success-100 p-3 rounded-full">
                            <i class="fas fa-bell text-success text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Dashboard Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Activities Table Section -->
            <div class="lg:col-span-2">
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200">
                    <!-- Table Header with Search and Filters -->
                    <div class="p-6 border-b border-secondary-200">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                            <h2 class="text-xl font-semibold text-text-primary">System-wide Activities</h2>
                            <div class="flex items-center space-x-2 mt-4 lg:mt-0">
                                <span class="text-sm text-text-secondary">Showing</span>
                                <span class="font-medium text-text-primary">247</span>
                                <span class="text-sm text-text-secondary">activities</span>
                            </div>
                        </div>
                        
                        <!-- Search and Filter Controls -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="relative">
                                <input type="text" placeholder="Search activities..." class="form-input pl-10" id="activity-search" oninput="filterActivitiesLocal()" />
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-secondary-400"></i>
                            </div>
                            <select class="form-input" id="position-filter" onchange="filterActivitiesLocal()">
                                <option value="">All Clusters</option>
                                <option value="ICTC">ICTC - ICT Cluster</option>
                                <option value="RDC">RDC - R&D Cluster</option>
                                <option value="SCC">SCC - Science Communication</option>
                                <option value="TTC">TTC - Technology Transfer</option>
                            </select>
                            <select class="form-input" id="status-filter" onchange="filterActivitiesLocal()">
                                <option value="">All Status</option>
                                <option value="active">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Not Started</option>
                                <option value="overdue">Overdue</option>
                            </select>
                            <input type="date" class="form-input" id="date-filter" onchange="filterActivitiesLocal()" />
                        </div>
                    </div>

                    <!-- Activities Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-secondary-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                        Activity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                        Cluster
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                        Representative
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                        Deadline
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="activities-table-body" class="bg-surface divide-y divide-secondary-200">
                                <!-- Activities will be loaded dynamically -->
                                <tr id="activities-loading">
                                    <td colspan="6" class="px-6 py-8 text-center text-text-secondary">
                                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading activities...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Table Pagination -->
                    <div class="px-6 py-4 border-t border-secondary-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-text-secondary">
                                Showing <span id="showing-count">0</span> of <span id="total-activities-count">0</span> activities
                            </div>
                            <div id="pagination-controls" class="flex space-x-2">
                                <!-- Pagination will be added dynamically if needed -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">
                <!-- Recent System Activity -->
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4 flex items-center">
                        <i class="fas fa-history text-primary mr-2"></i>
                        Recent System Activity
                    </h3>
                    <div id="recent-activity-container" class="space-y-4">
                        <div class="text-center py-4 text-text-secondary">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                        </div>
                    </div>
                    <button class="w-full mt-4 text-sm text-primary hover:text-primary-700 font-medium transition-micro" onclick="window.location.href='activity_management.php'">
                        View All Activity
                    </button>
                </div>

                <!-- Notification Management -->
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4 flex items-center">
                        <i class="fas fa-bell text-primary mr-2"></i>
                        Notification Management
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-secondary-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-text-primary">Pending Notifications</p>
                                <p class="text-xs text-text-secondary">Awaiting delivery</p>
                            </div>
                            <span id="notif-pending" class="bg-warning text-white text-xs px-2 py-1 rounded-full">0</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-secondary-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-text-primary">Sent Today</p>
                                <p class="text-xs text-text-secondary">Successfully delivered</p>
                            </div>
                            <span id="notif-sent-today" class="bg-success text-white text-xs px-2 py-1 rounded-full">0</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-secondary-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-text-primary">Failed Deliveries</p>
                                <p class="text-xs text-text-secondary">Requires attention</p>
                            </div>
                            <span id="notif-failed" class="bg-error text-white text-xs px-2 py-1 rounded-full">0</span>
                        </div>
                    </div>
                    <button class="w-full mt-4 btn-secondary text-sm" onclick="window.location.href='notification_center.php'">
                        <i class="fas fa-cog mr-2"></i>
                        Manage Notifications
                    </button>
                </div>

                <!-- Quick Actions -->
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4 flex items-center">
                        <i class="fas fa-bolt text-primary mr-2"></i>
                        Quick Actions
                    </h3>
                    <div class="space-y-3">
                        <button class="w-full text-left p-3 rounded-lg hover:bg-secondary-50 transition-micro" onclick="exportSystemReport()">
                            <div class="flex items-center">
                                <i class="fas fa-download text-primary mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-text-primary">Export System Report</p>
                                    <p class="text-xs text-text-secondary">Download comprehensive data</p>
                                </div>
                            </div>
                        </button>
                        <button class="w-full text-left p-3 rounded-lg hover:bg-secondary-50 transition-micro" onclick="scheduleSystemMaintenance()">
                            <div class="flex items-center">
                                <i class="fas fa-tools text-primary mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-text-primary">Schedule Maintenance</p>
                                    <p class="text-xs text-text-secondary">Plan system downtime</p>
                                </div>
                            </div>
                        </button>
                        <button class="w-full text-left p-3 rounded-lg hover:bg-secondary-50 transition-micro" onclick="viewSystemLogs()">
                            <div class="flex items-center">
                                <i class="fas fa-file-alt text-primary mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-text-primary">View System Logs</p>
                                    <p class="text-xs text-text-secondary">Audit trail and errors</p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script>
        // Global data store
        let dashboardData = null;
        let allActivities = [];
        const API_URL = '/central-cmi/api/secretariat-dashboard.php';

        // Load dashboard data from API
        async function loadDashboardData() {
            try {
                const response = await fetch(API_URL);
                const data = await response.json();
                
                if (data.success) {
                    dashboardData = data.data;
                    allActivities = data.data.activities;
                    updateDashboardUI(data.data);
                } else {
                    console.error('Failed to load dashboard:', data.error);
                    showError('Failed to load dashboard data');
                }
            } catch (error) {
                console.error('Error loading dashboard:', error);
                showError('Error connecting to server');
            }
        }

        // Update all dashboard UI elements
        function updateDashboardUI(data) {
            // Update stats cards
            document.getElementById('stat-total-activities').textContent = data.stats.totalActivities;
            document.getElementById('stat-completed').textContent = data.stats.completedActivities + ' completed';
            document.getElementById('stat-total-users').textContent = data.stats.totalUsers;
            document.getElementById('stat-pending').textContent = data.stats.pendingReports;
            document.getElementById('stat-overdue').textContent = data.stats.overdueActivities + ' overdue';
            document.getElementById('stat-delivery-rate').textContent = data.stats.deliveryRate + '%';
            
            // Update notification stats
            document.getElementById('notif-pending').textContent = data.notificationStats.pending;
            document.getElementById('notif-sent-today').textContent = data.notificationStats.sentToday;
            document.getElementById('notif-failed').textContent = data.notificationStats.failed;
            
            // Render activities table
            renderActivitiesTable(data.activities);
            
            // Render recent activity
            renderRecentActivity(data.recentActivity);
        }

        // Render activities table
        function renderActivitiesTable(activities) {
            const tbody = document.getElementById('activities-table-body');
            
            if (activities.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-text-secondary">
                            <i class="fas fa-inbox text-2xl mb-2"></i>
                            <p>No activities found</p>
                        </td>
                    </tr>
                `;
                document.getElementById('showing-count').textContent = '0';
                document.getElementById('total-activities-count').textContent = '0';
                return;
            }
            
            tbody.innerHTML = activities.map(activity => {
                const statusClass = getStatusClass(activity.status);
                const statusLabel = getStatusLabel(activity.status);
                const endDate = new Date(activity.endDate);
                const formattedDate = endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                
                return `
                    <tr class="hover:bg-secondary-50 transition-micro" data-status="${activity.status}" data-position="${activity.representative.position || ''}">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-text-primary">${activity.title}</div>
                            <div class="text-sm text-text-secondary">${(activity.description || '').substring(0, 50)}${activity.description && activity.description.length > 50 ? '...' : ''}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-text-secondary">${activity.representative.position || 'N/A'}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-6 w-6 rounded-full bg-primary-100 flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-primary text-xs"></i>
                                </div>
                                <span class="text-sm text-text-primary">${activity.representative.name || 'Unknown'}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="status-badge ${statusClass}">${statusLabel}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-text-secondary">${formattedDate}</td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <button class="text-primary hover:text-primary-700 text-sm" onclick="viewActivity(${activity.id})" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-secondary-400 hover:text-secondary-600 text-sm" onclick="editActivity(${activity.id})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            
            document.getElementById('showing-count').textContent = activities.length;
            document.getElementById('total-activities-count').textContent = allActivities.length;
        }

        // Render recent activity sidebar
        function renderRecentActivity(recentActivity) {
            const container = document.getElementById('recent-activity-container');
            
            if (!recentActivity || recentActivity.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-4 text-text-secondary">
                        <i class="fas fa-info-circle mr-2"></i> No recent activity
                    </div>
                `;
                return;
            }
            
            container.innerHTML = recentActivity.map(item => {
                const iconBgClass = `bg-${item.iconBg}-100`;
                const iconClass = `text-${item.iconBg}`;
                
                return `
                    <div class="flex items-start space-x-3">
                        <div class="${iconBgClass} p-1 rounded-full mt-1">
                            <i class="fas fa-${item.icon} ${iconClass} text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-text-primary">${item.title}</p>
                            <p class="text-xs text-text-secondary">${item.subtitle}</p>
                            <p class="text-xs text-text-secondary">${item.time}</p>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Get status CSS class
        function getStatusClass(status) {
            const classes = {
                'active': 'status-success',
                'completed': 'bg-primary-100 text-primary-700',
                'pending': 'status-warning',
                'overdue': 'status-error'
            };
            return classes[status] || 'bg-secondary-100 text-secondary-700';
        }

        // Get status label
        function getStatusLabel(status) {
            const labels = {
                'active': 'In Progress',
                'completed': 'Completed',
                'pending': 'Not Started',
                'overdue': 'Overdue'
            };
            return labels[status] || status;
        }

        // Filter activities locally
        function filterActivitiesLocal() {
            const searchTerm = document.getElementById('activity-search').value.toLowerCase();
            const positionFilter = document.getElementById('position-filter').value;
            const statusFilter = document.getElementById('status-filter').value;
            const dateFilter = document.getElementById('date-filter').value;
            
            let filtered = allActivities.filter(activity => {
                let match = true;
                
                // Search filter
                if (searchTerm) {
                    const titleMatch = activity.title.toLowerCase().includes(searchTerm);
                    const descMatch = (activity.description || '').toLowerCase().includes(searchTerm);
                    const nameMatch = (activity.representative.name || '').toLowerCase().includes(searchTerm);
                    match = match && (titleMatch || descMatch || nameMatch);
                }
                
                // Position/cluster filter
                if (positionFilter) {
                    match = match && activity.representative.position === positionFilter;
                }
                
                // Status filter
                if (statusFilter) {
                    match = match && activity.status === statusFilter;
                }
                
                // Date filter
                if (dateFilter) {
                    const filterDate = new Date(dateFilter).toDateString();
                    const activityDate = new Date(activity.endDate).toDateString();
                    match = match && activityDate === filterDate;
                }
                
                return match;
            });
            
            renderActivitiesTable(filtered);
        }

        // Activity actions
        function viewActivity(id) {
            window.location.href = `activity_management.php?view=${id}`;
        }

        function editActivity(id) {
            window.location.href = `activity_management.php?edit=${id}`;
        }

        // Quick action functions
        function exportSystemReport() {
            window.location.href = 'report_generation.php';
        }

        function scheduleSystemMaintenance() {
            alert('System Maintenance Scheduler - Feature coming soon');
        }

        function viewSystemLogs() {
            alert('System Logs - Feature coming soon');
        }

        // Show error message
        function showError(message) {
            const tbody = document.getElementById('activities-table-body');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-error">
                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                        <p>${message}</p>
                        <button onclick="loadDashboardData()" class="mt-2 text-primary hover:text-primary-700">
                            <i class="fas fa-redo mr-1"></i> Retry
                        </button>
                    </td>
                </tr>
            `;
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardData();
            
            // Auto-refresh every 60 seconds
            setInterval(loadDashboardData, 60000);
        });
    </script>

<?php include '../../includes/footer.php'; ?>