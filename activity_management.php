<?php
$pageTitle = "Activity Management";
$bodyClass = "bg-background text-text-primary";
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- Main Content -->
<main class="flex-1 overflow-x-hidden overflow-y-auto bg-background">
    <div class="container mx-auto px-6 py-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-text-primary">Activity Management</h1>
                <p class="mt-2 text-text-secondary">Manage and track all organizational activities</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <button onclick="exportActivities()" class="btn-secondary">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
                <button onclick="openActivityForm()" class="btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    New Activity
                </button>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-surface rounded-lg shadow-sm border border-secondary-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label for="search-activities" class="block text-sm font-medium text-text-primary mb-2">Search</label>
                    <input type="text" id="search-activities" placeholder="Search activities..." 
                           class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-text-primary mb-2">Status</label>
                    <select id="status-filter" class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">All Statuses</option>
                        <option value="not-started">Not Started</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="on-hold">On Hold</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label for="category-filter" class="block text-sm font-medium text-text-primary mb-2">Category</label>
                    <select id="category-filter" class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">All Categories</option>
                        <option value="policy">Policy</option>
                        <option value="research">Research</option>
                        <option value="training">Training</option>
                        <option value="outreach">Outreach</option>
                        <option value="compliance">Compliance</option>
                        <option value="infrastructure">Infrastructure</option>
                    </select>
                </div>
                <div>
                    <label for="date-from" class="block text-sm font-medium text-text-primary mb-2">From Date</label>
                    <input type="date" id="date-from" 
                           class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
                <div>
                    <label for="date-to" class="block text-sm font-medium text-text-primary mb-2">To Date</label>
                    <input type="date" id="date-to" 
                           class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>
            </div>
            <div class="mt-4 flex justify-between items-center">
                <button onclick="clearFilters()" class="text-primary hover:text-primary-700 text-sm font-medium">
                    Clear Filters
                </button>
                <span id="activity-count" class="text-sm text-text-secondary">0 activities</span>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div id="bulk-actions" class="hidden bg-primary-50 border border-primary-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="text-sm font-medium text-primary-700">
                        <span id="selected-count">0</span> activities selected
                    </span>
                </div>
                <div class="flex space-x-2">
                    <button onclick="applyBulkAction('mark-completed')" class="btn-sm btn-primary">
                        Mark Completed
                    </button>
                    <button onclick="applyBulkAction('export')" class="btn-sm btn-secondary">
                        Export Selected
                    </button>
                    <button onclick="applyBulkAction('delete')" class="btn-sm btn-danger">
                        Delete
                    </button>
                    <button onclick="clearSelection()" class="btn-sm btn-outline">
                        Clear Selection
                    </button>
                </div>
            </div>
        </div>

        <!-- Activities Table (Desktop) -->
        <div id="activities-table" class="hidden md:block bg-surface rounded-lg shadow-sm border border-secondary-200 overflow-hidden">
            <table class="min-w-full divide-y divide-secondary-200">
                <thead class="bg-secondary-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            <input type="checkbox" class="w-4 h-4 text-primary bg-surface border-secondary-300 rounded focus:ring-primary-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer hover:text-text-primary" onclick="sortTable('title')">
                            Activity
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer hover:text-text-primary" onclick="sortTable('category')">
                            Category
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer hover:text-text-primary" onclick="sortTable('status')">
                            Status
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer hover:text-text-primary" onclick="sortTable('progress')">
                            Progress
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider cursor-pointer hover:text-text-primary" onclick="sortTable('deadline')">
                            Deadline
                            <i class="fas fa-sort ml-1"></i>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Assigned To
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="activities-tbody" class="bg-surface divide-y divide-secondary-200">
                    <!-- Activities will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Activities Cards (Mobile) -->
        <div id="mobile-activities" class="md:hidden space-y-4">
            <!-- Mobile activity cards will be populated by JavaScript -->
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden text-center py-12">
            <div class="mx-auto w-24 h-24 bg-secondary-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-tasks text-2xl text-secondary-400"></i>
            </div>
            <h3 class="text-lg font-medium text-text-primary mb-2">No activities found</h3>
            <p class="text-text-secondary mb-6">Get started by creating your first activity or adjust your filters.</p>
            <button onclick="openActivityForm()" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Create Activity
            </button>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex items-center justify-between">
            <div class="flex items-center text-sm text-text-secondary">
                <span>Showing 1 to 10 of 25 results</span>
            </div>
            <div class="flex items-center space-x-2">
                <button class="px-3 py-2 text-sm font-medium text-text-secondary bg-surface border border-secondary-300 rounded-md hover:bg-secondary-50 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    Previous
                </button>
                <button class="px-3 py-2 text-sm font-medium text-white bg-primary border border-primary rounded-md">
                    1
                </button>
                <button class="px-3 py-2 text-sm font-medium text-text-secondary bg-surface border border-secondary-300 rounded-md hover:bg-secondary-50">
                    2
                </button>
                <button class="px-3 py-2 text-sm font-medium text-text-secondary bg-surface border border-secondary-300 rounded-md hover:bg-secondary-50">
                    3
                </button>
                <button class="px-3 py-2 text-sm font-medium text-text-secondary bg-surface border border-secondary-300 rounded-md hover:bg-secondary-50">
                    Next
                </button>
            </div>
        </div>
    </div>
</main>

<!-- Activity Form Modal -->
<div id="activity-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-surface">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-secondary-200">
            <h3 id="modal-title" class="text-lg font-semibold text-text-primary">New Activity</h3>
            <button onclick="closeActivityForm()" class="text-text-secondary hover:text-text-primary">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="activity-form" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h4 class="text-md font-medium text-text-primary mb-4">Basic Information</h4>
                </div>
                
                <div>
                    <label for="activity-title" class="block text-sm font-medium text-text-primary mb-2">Activity Title *</label>
                    <input type="text" id="activity-title" name="title" required 
                           class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Enter activity title">
                    <div id="activity-title-error" class="hidden text-sm text-error mt-1"></div>
                </div>
                
                <div>
                    <label for="activity-category" class="block text-sm font-medium text-text-primary mb-2">Category</label>
                    <select id="activity-category" name="category" 
                            class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="policy">Policy</option>
                        <option value="research">Research</option>
                        <option value="training">Training</option>
                        <option value="outreach">Outreach</option>
                        <option value="compliance">Compliance</option>
                        <option value="infrastructure">Infrastructure</option>
                    </select>
                </div>
                
                <div>
                    <label for="activity-priority" class="block text-sm font-medium text-text-primary mb-2">Priority</label>
                    <select id="activity-priority" name="priority" 
                            class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                
                <div>
                    <label for="activity-assigned-to" class="block text-sm font-medium text-text-primary mb-2">Assigned To</label>
                    <input type="text" id="activity-assigned-to" name="assignedTo" 
                           class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                           placeholder="Enter assignee name">
                </div>
                
                <div class="md:col-span-2">
                    <label for="activity-description" class="block text-sm font-medium text-text-primary mb-2">Description *</label>
                    <textarea id="activity-description" name="description" rows="4" required 
                              class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                              placeholder="Enter activity description"></textarea>
                    <div id="activity-description-error" class="hidden text-sm text-error mt-1"></div>
                </div>
                
                <!-- Dates and Progress -->
                <div class="md:col-span-2">
                    <h4 class="text-md font-medium text-text-primary mb-4 mt-6">Timeline & Progress</h4>
                </div>
                
                <div>
                    <label for="activity-start-date" class="block text-sm font-medium text-text-primary mb-2">Start Date</label>
                    <input type="date" id="activity-start-date" name="startDate" 
                           class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <div id="activity-start-date-error" class="hidden text-sm text-error mt-1"></div>
                </div>
                
                <div>
                    <label for="activity-deadline" class="block text-sm font-medium text-text-primary mb-2">Deadline</label>
                    <input type="date" id="activity-deadline" name="deadline" 
                           class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <div id="activity-deadline-error" class="hidden text-sm text-error mt-1"></div>
                </div>
                
                <div>
                    <label for="activity-status" class="block text-sm font-medium text-text-primary mb-2">Status</label>
                    <select id="activity-status" name="status" 
                            class="w-full px-3 py-2 border border-secondary-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="not-started">Not Started</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="on-hold">On Hold</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label for="activity-progress" class="block text-sm font-medium text-text-primary mb-2">
                        Progress: <span id="progress-value">0%</span>
                    </label>
                    <input type="range" id="activity-progress" name="progress" min="0" max="100" value="0" 
                           class="w-full h-2 bg-secondary-200 rounded-lg appearance-none cursor-pointer"
                           oninput="updateProgressValue()">
                </div>
                
                <!-- Milestones -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-medium text-text-primary">Milestones</h4>
                        <button type="button" onclick="addMilestone()" class="text-primary hover:text-primary-700 text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i> Add Milestone
                        </button>
                    </div>
                    <div id="milestones-container" class="space-y-2">
                        <!-- Milestones will be added dynamically -->
                    </div>
                </div>
            </div>
            
            <!-- Auto-save indicator -->
            <div id="autosave-indicator" class="hidden text-sm text-success mt-4">
                <i class="fas fa-check mr-1"></i> Draft saved
            </div>
            
            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-secondary-200">
                <button type="button" onclick="closeActivityForm()" class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    Save Activity
                </button>
            </div>
        </form>
    </div>
</div>

<script>
        // Sample activity data
        let activities = [
            {
                id: 1,
                title: "Policy Review and Update",
                category: "policy",
                status: "in-progress",
                progress: 65,
                deadline: "2024-03-15",
                description: "Comprehensive review of existing policies and implementation of updates based on recent regulatory changes.",
                assignedTo: "Policy Team",
                priority: "high",
                createdDate: "2024-01-15",
                lastModified: "2024-02-20"
            },
            {
                id: 2,
                title: "Research Data Analysis",
                category: "research",
                status: "completed",
                progress: 100,
                deadline: "2024-02-28",
                description: "Analysis of collected research data from Q4 2023 studies.",
                assignedTo: "Research Team",
                priority: "medium",
                createdDate: "2024-01-10",
                lastModified: "2024-02-28"
            },
            {
                id: 3,
                title: "Staff Training Program",
                category: "training",
                status: "not-started",
                progress: 0,
                deadline: "2024-04-30",
                description: "Quarterly training program for new staff members.",
                assignedTo: "HR Department",
                priority: "medium",
                createdDate: "2024-02-01",
                lastModified: "2024-02-01"
            },
            {
                id: 4,
                title: "Community Outreach Initiative",
                category: "outreach",
                status: "in-progress",
                progress: 30,
                deadline: "2024-05-15",
                description: "Engagement with local communities to promote awareness.",
                assignedTo: "Outreach Team",
                priority: "high",
                createdDate: "2024-01-20",
                lastModified: "2024-02-15"
            },
            {
                id: 5,
                title: "Compliance Audit",
                category: "compliance",
                status: "on-hold",
                progress: 45,
                deadline: "2024-03-30",
                description: "Internal audit to ensure compliance with regulatory requirements.",
                assignedTo: "Compliance Team",
                priority: "high",
                createdDate: "2024-01-05",
                lastModified: "2024-02-10"
            }
        ];

        let filteredActivities = [...activities];
        let selectedActivities = new Set();
        let currentEditingId = null;
        let autosaveTimeout = null;

        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            renderActivities();
            updateActivityCount();
            
            // Add event listeners
            const searchInput = document.getElementById('search-activities');
            if (searchInput) {
                searchInput.addEventListener('input', filterActivities);
            }

            const statusFilter = document.getElementById('status-filter');
            if (statusFilter) {
                statusFilter.addEventListener('change', filterActivities);
            }

            const categoryFilter = document.getElementById('category-filter');
            if (categoryFilter) {
                categoryFilter.addEventListener('change', filterActivities);
            }

            const dateFromFilter = document.getElementById('date-from');
            if (dateFromFilter) {
                dateFromFilter.addEventListener('change', filterActivities);
            }

            const dateToFilter = document.getElementById('date-to');
            if (dateToFilter) {
                dateToFilter.addEventListener('change', filterActivities);
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                const dropdowns = document.querySelectorAll('.dropdown-menu');
                dropdowns.forEach(dropdown => {
                    if (!dropdown.contains(event.target) && !dropdown.previousElementSibling.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            });
        });

        // Render activities
        function renderActivities() {
            renderDesktopActivities();
            renderMobileActivities();
        }

        function renderDesktopActivities() {
            const tbody = document.getElementById('activities-tbody');
            if (!tbody) return;

            if (filteredActivities.length === 0) {
                tbody.innerHTML = '';
                document.getElementById('empty-state').classList.remove('hidden');
                document.getElementById('activities-table').classList.add('hidden');
                return;
            }

            document.getElementById('empty-state').classList.add('hidden');
            document.getElementById('activities-table').classList.remove('hidden');

            tbody.innerHTML = filteredActivities.map(activity => `
                <tr class="hover:bg-surface-hover transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" 
                               class="w-4 h-4 text-primary bg-surface border-secondary-300 rounded focus:ring-primary-500" 
                               onchange="toggleActivitySelection(${activity.id}, this.checked)">
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-text-primary">${activity.title}</div>
                        <div class="text-sm text-text-secondary">${activity.description.substring(0, 60)}...</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getCategoryBadgeClass(activity.category)}">
                            ${getCategoryLabel(activity.category)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusBadgeClass(activity.status)}">
                            ${getStatusLabel(activity.status)}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-16 bg-secondary-200 rounded-full h-2 mr-2">
                                <div class="bg-primary h-2 rounded-full" style="width: ${activity.progress}%"></div>
                            </div>
                            <span class="text-sm text-text-secondary">${activity.progress}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm ${getDeadlineClass(activity.deadline)}">
                        <div>${formatDate(activity.deadline)}</div>
                        <div class="text-xs">${getDeadlineText(activity.deadline)}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-text-secondary">
                        ${activity.assignedTo}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <button onclick="editActivity(${activity.id})" 
                                    class="text-primary hover:text-primary-700 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteActivity(${activity.id})" 
                                    class="text-error hover:text-error-700 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button onclick="linkToReport(${activity.id})" 
                                    class="text-accent hover:text-accent-700 transition-colors">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        function renderMobileActivities() {
            const container = document.getElementById('mobile-activities');
            if (!container) return;

            if (filteredActivities.length === 0) {
                container.innerHTML = '';
                return;
            }

            container.innerHTML = filteredActivities.map(activity => `
                <div class="bg-surface rounded-lg border border-secondary-200 p-4 space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" 
                                   class="w-4 h-4 text-primary bg-surface border-secondary-300 rounded focus:ring-primary-500 mt-1" 
                                   onchange="toggleActivitySelection(${activity.id}, this.checked)">
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-text-primary">${activity.title}</h3>
                                <p class="text-xs text-text-secondary mt-1">${activity.description.substring(0, 80)}...</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="editActivity(${activity.id})" 
                                    class="text-primary hover:text-primary-700">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button onclick="deleteActivity(${activity.id})" 
                                    class="text-error hover:text-error-700">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getCategoryBadgeClass(activity.category)}">
                                ${getCategoryLabel(activity.category)}
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getStatusBadgeClass(activity.status)}">
                                ${getStatusLabel(activity.status)}
                            </span>
                        </div>
                        <span class="text-xs text-text-secondary">${activity.progress}%</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex-1 bg-secondary-200 rounded-full h-2 mr-3">
                            <div class="bg-primary h-2 rounded-full" style="width: ${activity.progress}%"></div>
                        </div>
                        <div class="text-xs ${getDeadlineClass(activity.deadline)}">
                            ${formatDate(activity.deadline)}
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-xs text-text-secondary">
                        <span>Assigned to: ${activity.assignedTo}</span>
                        <button onclick="linkToReport(${activity.id})" 
                                class="text-accent hover:text-accent-700">
                            <i class="fas fa-chart-bar"></i> Report
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Filter activities
        function filterActivities() {
            const searchTerm = document.getElementById('search-activities')?.value.toLowerCase() || '';
            const statusFilter = document.getElementById('status-filter')?.value || '';
            const categoryFilter = document.getElementById('category-filter')?.value || '';
            const dateFrom = document.getElementById('date-from')?.value || '';
            const dateTo = document.getElementById('date-to')?.value || '';

            filteredActivities = activities.filter(activity => {
                const matchesSearch = activity.title.toLowerCase().includes(searchTerm) || 
                                    activity.description.toLowerCase().includes(searchTerm) ||
                                    activity.assignedTo.toLowerCase().includes(searchTerm);
                
                const matchesStatus = !statusFilter || activity.status === statusFilter;
                const matchesCategory = !categoryFilter || activity.category === categoryFilter;
                
                let matchesDateRange = true;
                if (dateFrom || dateTo) {
                    const activityDate = new Date(activity.deadline);
                    if (dateFrom) {
                        matchesDateRange = matchesDateRange && activityDate >= new Date(dateFrom);
                    }
                    if (dateTo) {
                        matchesDateRange = matchesDateRange && activityDate <= new Date(dateTo);
                    }
                }
                
                return matchesSearch && matchesStatus && matchesCategory && matchesDateRange;
            });

            renderActivities();
            updateActivityCount();
            clearSelection();
        }

        // Clear filters
        function clearFilters() {
            document.getElementById('search-activities').value = '';
            document.getElementById('status-filter').value = '';
            document.getElementById('category-filter').value = '';
            document.getElementById('date-from').value = '';
            document.getElementById('date-to').value = '';
            
            filteredActivities = [...activities];
            renderActivities();
            updateActivityCount();
            clearSelection();
        }

        // Update activity count
        function updateActivityCount() {
            const countElement = document.getElementById('activity-count');
            if (countElement) {
                countElement.textContent = `${filteredActivities.length} activities`;
            }
        }

        // Activity selection
        function toggleActivitySelection(activityId, isSelected) {
            if (isSelected) {
                selectedActivities.add(activityId);
            } else {
                selectedActivities.delete(activityId);
            }
            updateBulkActions();
        }

        function updateBulkActions() {
            const bulkActions = document.getElementById('bulk-actions');
            const selectedCount = document.getElementById('selected-count');
            
            if (selectedActivities.size > 0) {
                bulkActions.classList.remove('hidden');
                selectedCount.textContent = selectedActivities.size;
            } else {
                bulkActions.classList.add('hidden');
            }
        }

        function clearSelection() {
            selectedActivities.clear();
            updateBulkActions();
            
            // Uncheck all checkboxes
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.onchange && checkbox.onchange.toString().includes('toggleActivitySelection')) {
                    checkbox.checked = false;
                }
            });
        }

        function applyBulkAction(action) {
            if (selectedActivities.size === 0) return;
            
            const selectedIds = Array.from(selectedActivities);
            
            switch(action) {
                case 'delete':
                    if (confirm(`Are you sure you want to delete ${selectedIds.length} activities?`)) {
                        activities = activities.filter(activity => !selectedIds.includes(activity.id));
                        filteredActivities = filteredActivities.filter(activity => !selectedIds.includes(activity.id));
                        renderActivities();
                        updateActivityCount();
                        clearSelection();
                        showNotification(`${selectedIds.length} activities deleted successfully`, 'success');
                    }
                    break;
                case 'export':
                    exportActivities();
                    break;
                case 'mark-completed':
                    selectedIds.forEach(id => {
                        const activity = activities.find(a => a.id === id);
                        if (activity) {
                            activity.status = 'completed';
                            activity.progress = 100;
                        }
                    });
                    renderActivities();
                    clearSelection();
                    showNotification(`${selectedIds.length} activities marked as completed`, 'success');
                    break;
            }
        }

        // Activity form management
        function openActivityForm(activityId = null) {
            currentEditingId = activityId;
            const modal = document.getElementById('activity-modal');
            const form = document.getElementById('activity-form');
            const title = document.getElementById('modal-title');
            
            if (activityId) {
                title.textContent = 'Edit Activity';
                populateForm(activityId);
            } else {
                title.textContent = 'New Activity';
                form.reset();
                // Reset milestones
                const milestonesContainer = document.getElementById('milestones-container');
                if (milestonesContainer) {
                    milestonesContainer.innerHTML = '';
                }
            }
            
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeActivityForm() {
            const modal = document.getElementById('activity-modal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentEditingId = null;
            
            if (autosaveTimeout) {
                clearTimeout(autosaveTimeout);
                autosaveTimeout = null;
            }
        }

        function populateForm(activityId) {
            const activity = activities.find(a => a.id === activityId);
            if (!activity) return;
            
            document.getElementById('activity-title').value = activity.title;
            document.getElementById('activity-category').value = activity.category;
            document.getElementById('activity-priority').value = activity.priority;
            document.getElementById('activity-description').value = activity.description;
            document.getElementById('activity-start-date').value = activity.createdDate;
            document.getElementById('activity-deadline').value = activity.deadline;
            document.getElementById('activity-assigned-to').value = activity.assignedTo;
            document.getElementById('activity-status').value = activity.status;
            document.getElementById('activity-progress').value = activity.progress;
            
            updateProgressValue();
        }

        function editActivity(activityId) {
            openActivityForm(activityId);
        }

        function deleteActivity(activityId) {
            if (confirm('Are you sure you want to delete this activity?')) {
                activities = activities.filter(activity => activity.id !== activityId);
                filteredActivities = filteredActivities.filter(activity => activity.id !== activityId);
                renderActivities();
                updateActivityCount();
                showNotification('Activity deleted successfully', 'success');
            }
        }

        // Form submission
        document.getElementById('activity-form')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate required fields
            const isValid = validateField('activity-title') && 
                          validateField('activity-description') && 
                          validateDates();
            
            if (!isValid) {
                showNotification('Please fix the errors before submitting', 'error');
                return;
            }
            
            const formData = new FormData(this);
            const activityData = {
                title: formData.get('title'),
                category: formData.get('category'),
                priority: formData.get('priority'),
                description: formData.get('description'),
                deadline: formData.get('deadline'),
                assignedTo: formData.get('assignedTo'),
                status: formData.get('status'),
                progress: parseInt(formData.get('progress')) || 0
            };
            
            if (currentEditingId) {
                // Update existing activity
                const activityIndex = activities.findIndex(a => a.id === currentEditingId);
                if (activityIndex !== -1) {
                    activities[activityIndex] = {
                        ...activities[activityIndex],
                        ...activityData,
                        lastModified: new Date().toISOString().split('T')[0]
                    };
                    showNotification('Activity updated successfully', 'success');
                }
            } else {
                // Create new activity
                const newActivity = {
                    id: Math.max(...activities.map(a => a.id)) + 1,
                    ...activityData,
                    createdDate: new Date().toISOString().split('T')[0],
                    lastModified: new Date().toISOString().split('T')[0]
                };
                activities.push(newActivity);
                showNotification('Activity created successfully', 'success');
            }
            
            filterActivities();
            closeActivityForm();
        });

        // Progress slider
        function updateProgressValue() {
            const slider = document.getElementById('activity-progress');
            const display = document.getElementById('progress-value');
            if (slider && display) {
                display.textContent = slider.value + '%';
            }
        }

        // Auto-save functionality
        function scheduleAutosave() {
            if (autosaveTimeout) {
                clearTimeout(autosaveTimeout);
            }
            
            autosaveTimeout = setTimeout(() => {
                const indicator = document.getElementById('autosave-indicator');
                if (indicator) {
                    indicator.textContent = 'Draft saved';
                    indicator.classList.remove('hidden');
                    
                    setTimeout(() => {
                        indicator.classList.add('hidden');
                    }, 2000);
                }
            }, 2000);
        }

        // Field validation
        function validateField(fieldId) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + '-error');
            
            if (!field) return true;

            let isValid = true;
            let errorMessage = '';

            if (fieldId === 'activity-title' && !field.value.trim()) {
                errorMessage = 'Activity title is required';
                isValid = false;
            } else if (fieldId === 'activity-description' && !field.value.trim()) {
                errorMessage = 'Activity description is required';
                isValid = false;
            }

            if (errorDiv) {
                if (!isValid) {
                    errorDiv.textContent = errorMessage;
                    errorDiv.classList.remove('hidden');
                    field.classList.add('border-error');
                } else {
                    errorDiv.classList.add('hidden');
                    field.classList.remove('border-error');
                }
            }

            return isValid;
        }

        function validateDates() {
            const startDateEl = document.getElementById('activity-start-date');
            const deadlineEl = document.getElementById('activity-deadline');
            const startError = document.getElementById('activity-start-date-error');
            const deadlineError = document.getElementById('activity-deadline-error');

            if (!startDateEl || !deadlineEl) return true;

            const startDate = startDateEl.value;
            const deadline = deadlineEl.value;

            if (startDate && deadline && startDate > deadline) {
                if (deadlineError) {
                    deadlineError.textContent = 'Deadline must be after start date';
                    deadlineError.classList.remove('hidden');
                }
                return false;
            } else {
                if (deadlineError) deadlineError.classList.add('hidden');
                return true;
            }
        }

        // Milestone management
        function addMilestone() {
            const container = document.getElementById('milestones-container');
            if (!container) return;

            const milestoneCount = container.children.length + 1;
            
            const milestoneDiv = document.createElement('div');
            milestoneDiv.className = 'flex items-center';
            milestoneDiv.innerHTML = `
                <input type="checkbox" id="milestone-${milestoneCount}" class="w-4 h-4 text-primary bg-surface border-secondary-300 rounded focus:ring-primary-500">
                <input type="text" class="ml-3 flex-1 text-sm border-none bg-transparent text-text-primary placeholder-text-secondary" 
                    placeholder="Enter milestone description" id="milestone-text-${milestoneCount}">
                <button type="button" class="ml-2 text-error hover:text-error-700" onclick="removeMilestone(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(milestoneDiv);
        }

        function removeMilestone(button) {
            if (button && button.parentElement) {
                button.parentElement.remove();
            }
        }

        // Export functionality
        function exportActivities() {
            const csvContent = "data:text/csv;charset=utf-8," + 
                "Title,Category,Status,Progress,Deadline,Description\n" +
                filteredActivities.map(activity => 
                    `"${activity.title}","${getCategoryLabel(activity.category)}","${getStatusLabel(activity.status)}","${activity.progress}%","${activity.deadline}","${activity.description}"`
                ).join("\n");

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "activities_export.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Link to report
        function linkToReport(activityId = null) {
            if (activityId) {
                window.location.href = `report_generation.html?activity=${activityId}`;
            } else {
                window.location.href = 'report_generation.html';
            }
        }

        // Table sorting
        function sortTable(column) {
            // Implementation for table sorting
            console.log('Sorting by:', column);
        }

        // Utility functions
        function getCategoryBadgeClass(category) {
            const classes = {
                'policy': 'bg-primary-100 text-primary-700',
                'research': 'bg-accent-100 text-accent-700',
                'training': 'bg-warning-100 text-warning-700',
                'outreach': 'bg-success-100 text-success-700',
                'compliance': 'bg-error-100 text-error-700',
                'infrastructure': 'bg-secondary-100 text-secondary-700'
            };
            return classes[category] || 'bg-secondary-100 text-secondary-700';
        }

        function getCategoryLabel(category) {
            const labels = {
                'policy': 'Policy',
                'research': 'Research',
                'training': 'Training',
                'outreach': 'Outreach',
                'compliance': 'Compliance',
                'infrastructure': 'Infrastructure'
            };
            return labels[category] || category;
        }

        function getStatusBadgeClass(status) {
            const classes = {
                'not-started': 'bg-secondary-100 text-secondary-700',
                'in-progress': 'bg-warning-100 text-warning-700',
                'completed': 'bg-success-100 text-success-700',
                'on-hold': 'bg-error-100 text-error-700',
                'cancelled': 'bg-error-100 text-error-700'
            };
            return classes[status] || 'bg-secondary-100 text-secondary-700';
        }

        function getStatusLabel(status) {
            const labels = {
                'not-started': 'Not Started',
                'in-progress': 'In Progress',
                'completed': 'Completed',
                'on-hold': 'On Hold',
                'cancelled': 'Cancelled'
            };
            return labels[status] || status;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            });
        }

        function getDeadlineClass(deadline) {
            const today = new Date();
            const deadlineDate = new Date(deadline);
            const diffTime = deadlineDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays < 0) return 'text-error';
            if (diffDays <= 7) return 'text-warning';
            return 'text-text-secondary';
        }

        function getDeadlineText(deadline) {
            const today = new Date();
            const deadlineDate = new Date(deadline);
            const diffTime = deadlineDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays < 0) return `${Math.abs(diffDays)} days overdue`;
            if (diffDays === 0) return 'Due today';
            if (diffDays === 1) return 'Due tomorrow';
            if (diffDays <= 7) return `Due in ${diffDays} days`;
            return `${diffDays} days remaining`;
        }

        function showNotification(message, type = 'info') {
            // Simple notification implementation
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-success text-white' : 
                type === 'error' ? 'bg-error text-white' : 
                'bg-primary text-white'
            }`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
</script>

<?php include 'includes/footer.php'; ?>