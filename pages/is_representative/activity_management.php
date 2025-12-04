<?php
$pageTitle = "Activity Management - Central CMI";
$bodyClass = "bg-background min-h-screen";
require_once __DIR__ . '/../../database/auth.php';
require_role(['representative', 'secretariat']);
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<!-- Main Content -->
<main class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <section class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-text-primary">Activity Management</h1>
                <p class="text-text-secondary mt-2">Create, track, and manage departmental activities with progress monitoring</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <button type="button" class="btn-secondary" onclick="exportActivities()">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
                <button type="button" class="btn-secondary" onclick="clearAllActivities()" title="Clear all activities">
                    <i class="fas fa-trash mr-2"></i>
                    Clear All
                </button>
                <button type="button" class="btn-primary" onclick="openActivityForm()">
                    <i class="fas fa-plus mr-2"></i>
                    New Activity
                </button>
            </div>
        </div>
    </section>

    <!-- Filters and Search Section -->
    <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-text-primary mb-2">Search Activities</label>
                <div class="relative">
                    <input type="text" id="search" placeholder="Search by title, description..." class="form-input pl-10" oninput="filterActivities()" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-secondary-400"></i>
                    </div>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status-filter" class="block text-sm font-medium text-text-primary mb-2">Status</label>
                <select id="status-filter" class="form-input" onchange="filterActivities()">
                    <option value>All Status</option>
                    <option value="not-started">Not Started</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="on-hold">On Hold</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label for="category-filter" class="block text-sm font-medium text-text-primary mb-2">Category</label>
                <select id="category-filter" class="form-input" onchange="filterActivities()">
                    <option value>All Categories</option>
                    <option value="policy">Policy Development</option>
                    <option value="research">Research</option>
                    <option value="training">Training</option>
                    <option value="outreach">Public Outreach</option>
                    <option value="compliance">Compliance</option>
                    <option value="infrastructure">Infrastructure</option>
                </select>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="date-from" class="block text-sm font-medium text-text-primary mb-2">From Date</label>
                <input type="date" id="date-from" class="form-input" onchange="filterActivities()" />
            </div>
            <div>
                <label for="date-to" class="block text-sm font-medium text-text-primary mb-2">To Date</label>
                <input type="date" id="date-to" class="form-input" onchange="filterActivities()" />
            </div>
            <div class="flex items-end">
                <button type="button" class="btn-secondary w-full" onclick="clearFilters()">
                    <i class="fas fa-times mr-2"></i>
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Results Summary -->
        <div class="flex items-center justify-between pt-4 border-t border-secondary-200">
            <div class="text-sm text-text-secondary">
                Showing <span id="results-count" class="font-medium text-text-primary">12</span> of 
                <span id="total-count" class="font-medium text-text-primary">12</span> activities
            </div>
            <div class="flex space-x-4 text-sm">
                <span class="flex items-center">
                    <div class="w-3 h-3 bg-success rounded-full mr-2"></div>
                    <span class="text-text-secondary">Completed: <span class="font-medium text-text-primary">5</span></span>
                </span>
                <span class="flex items-center">
                    <div class="w-3 h-3 bg-warning rounded-full mr-2"></div>
                    <span class="text-text-secondary">In Progress: <span class="font-medium text-text-primary">4</span></span>
                </span>
                <span class="flex items-center">
                    <div class="w-3 h-3 bg-secondary-400 rounded-full mr-2"></div>
                    <span class="text-text-secondary">Not Started: <span class="font-medium text-text-primary">3</span></span>
                </span>
            </div>
        </div>
    </section>

    <!-- Bulk Actions -->
    <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-4 mb-6" id="bulk-actions" style="display: none;">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-sm text-text-secondary mr-4">
                    <span id="selected-count">0</span> activities selected
                </span>
                <div class="flex space-x-2">
                    <select id="bulk-status" class="form-input text-sm py-1">
                        <option value>Change Status</option>
                        <option value="not-started">Not Started</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="on-hold">On Hold</option>
                    </select>
                    <button type="button" class="btn-primary text-sm py-1" onclick="applyBulkAction()">
                        Apply
                    </button>
                </div>
            </div>
            <button type="button" class="text-text-secondary hover:text-text-primary" onclick="clearSelection()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </section>

    <!-- Activity Form Modal -->
    <div id="activity-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-surface rounded-xl shadow-modal max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-secondary-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-text-primary">Create New Activity</h3>
                        <button type="button" class="text-text-secondary hover:text-text-primary" onclick="closeActivityForm()">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form id="activity-form" onsubmit="submitActivity(event)">
                        <!-- Basic Activity Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="activity-title" class="block text-sm font-medium text-text-primary mb-2">Activity Title *</label>
                                <input type="text" id="activity-title" name="title" class="form-input" required>
                            </div>
                            <div>
                                <label for="activity-type" class="block text-sm font-medium text-text-primary mb-2">Type *</label>
                                <select id="activity-type" name="type" class="form-input" required>
                                    <option value="">Choose type</option>
                                    <option value="event">Event</option>
                                    <option value="training">Training</option>
                                    <option value="fiesta">Fiesta</option>
                                    <option value="exhibit">Exhibit</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="others">Others (specify)</option>
                                </select>
                            </div>
                            <div>
                                <label for="activity-start-date" class="block text-sm font-medium text-text-primary mb-2">Start Date *</label>
                                <input type="date" id="activity-start-date" name="startDate" class="form-input" required>
                            </div>
                            <div>
                                <label for="activity-end-date" class="block text-sm font-medium text-text-primary mb-2">End Date *</label>
                                <input type="date" id="activity-end-date" name="endDate" class="form-input" required>
                            </div>
                        </div>
                        
                        <!-- Other Type Field -->
                        <div id="other-type-wrapper" class="mb-6 hidden">
                            <label for="other-type" class="block text-sm font-medium text-text-primary mb-2">Specify Type *</label>
                            <input type="text" id="other-type" name="otherType" class="form-input" placeholder="Please specify the activity type">
                        </div>
                        
                        <div class="mb-6">
                            <label for="activity-description" class="block text-sm font-medium text-text-primary mb-2">Description</label>
                            <textarea id="activity-description" name="description" rows="3" class="form-input" placeholder="Describe the activity..."></textarea>
                        </div>

                        <!-- Dynamic Accomplishment Template Sections -->
                        <div id="accomplishment-sections" class="mb-6">
                            <h4 class="text-lg font-semibold text-text-primary mb-4">Accomplishment Details</h4>
                            <div id="template-sections">
                                <!-- Template sections will be loaded dynamically here -->
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-secondary-200">
                            <button type="button" class="btn-secondary" onclick="closeActivityForm()">Cancel</button>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save mr-2"></i>Create Activity
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Activities Table -->
    <section class="bg-surface rounded-xl shadow-card border border-secondary-200 overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-secondary-200">
                <thead class="bg-secondary-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select-all" class="w-4 h-4 text-primary bg-surface border-secondary-300 rounded focus:ring-primary-500" onchange="toggleSelectAll()" />
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">End Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="activities-table-body" class="bg-surface divide-y divide-secondary-200">
                    <!-- Activity rows will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden" id="activities-mobile-view">
            <!-- Activity cards will be populated by JavaScript -->
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="text-center py-12 hidden">
            <div class="flex justify-center mb-4">
                <div class="bg-secondary-100 p-4 rounded-full">
                    <i class="fas fa-tasks text-2xl text-secondary-400"></i>
                </div>
            </div>
            <h3 class="text-lg font-medium text-text-primary mb-2">No activities found</h3>
            <p class="text-text-secondary mb-6">Get started by creating your first activity or adjust your filters.</p>
            <button type="button" class="btn-primary" onclick="openActivityForm()">
                <i class="fas fa-plus mr-2"></i>
                Create Activity
            </button>
        </div>
    </section>

    <!-- Pagination -->
    <section class="flex items-center justify-between mt-6">
        <div class="text-sm text-text-secondary">
            Showing 1 to 10 of 12 results
        </div>
        <div class="flex space-x-2">
            <button type="button" class="px-3 py-2 text-sm border border-secondary-300 rounded-md text-text-secondary hover:bg-secondary-50 disabled:opacity-50" disabled>
                <i class="fas fa-chevron-left mr-1"></i>
                Previous
            </button>
            <button type="button" class="px-3 py-2 text-sm bg-primary text-white rounded-md">1</button>
            <button type="button" class="px-3 py-2 text-sm border border-secondary-300 rounded-md text-text-secondary hover:bg-secondary-50">2</button>
            <button type="button" class="px-3 py-2 text-sm border border-secondary-300 rounded-md text-text-secondary hover:bg-secondary-50">
                Next
                <i class="fas fa-chevron-right ml-1"></i>
            </button>
        </div>
    </section>
</main>

<script>
// Global variables
let currentTemplate = null;
let activityCounter = 0;
let activities = [];

// Load accomplishment template based on user position
async function loadAccomplishmentTemplate() {
    try {
        const response = await fetch('/central-cmi/api/get-accomplishment-template.php');
        const data = await response.json();
        
        if (data.success) {
            currentTemplate = data.template;
            renderTemplateSections(data.template);
        } else {
            console.error('Failed to load template:', data.error);
            showNotification('Failed to load accomplishment template', 'error');
        }
    } catch (error) {
        console.error('Error loading template:', error);
        showNotification('Error loading accomplishment template', 'error');
    }
}

// Render template sections in the form
function renderTemplateSections(template) {
    const container = document.getElementById('template-sections');
    container.innerHTML = '';
    
    template.sections.forEach((section, index) => {
        const sectionDiv = document.createElement('div');
        sectionDiv.className = 'mb-6 p-4 bg-secondary-50 rounded-lg';
        sectionDiv.innerHTML = `
            <h5 class="text-md font-semibold text-text-primary mb-3">${section.title}</h5>
            <div id="section-${section.key}" class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-text-secondary">Add entries for this section</span>
                    <button type="button" class="btn-secondary text-sm" onclick="addTableRow('${section.key}', ${JSON.stringify(section.columns).replace(/"/g, '&quot;')})">
                        <i class="fas fa-plus mr-1"></i>Add Row
                    </button>
                </div>
                <div id="table-${section.key}" class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-surface">
                            <tr>
                                ${section.columns.map(col => `<th class="px-3 py-2 text-left font-medium text-text-secondary">${col.replace(/_/g, ' ').toUpperCase()}</th>`).join('')}
                                <th class="px-3 py-2 text-left font-medium text-text-secondary">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-${section.key}">
                            <!-- Rows will be added dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        container.appendChild(sectionDiv);
    });
}

// Add a new row to a table section
function addTableRow(sectionKey, columns) {
    const tbody = document.getElementById(`tbody-${sectionKey}`);
    const rowId = `row-${sectionKey}-${++activityCounter}`;
    
    const row = document.createElement('tr');
    row.id = rowId;
    row.className = 'border-b border-secondary-200';
    
    let rowHtml = '';
    columns.forEach(column => {
        const inputType = column.includes('date') ? 'date' : 
                         column.includes('amount') || column.includes('quantity') ? 'number' : 'text';
        rowHtml += `
            <td class="px-3 py-2">
                <input type="${inputType}" 
                       name="${sectionKey}[${activityCounter}][${column}]" 
                       class="w-full px-2 py-1 text-sm border border-secondary-300 rounded focus:ring-primary-500 focus:border-primary-500"
                       placeholder="${column.replace(/_/g, ' ')}">
            </td>
        `;
    });
    
    rowHtml += `
        <td class="px-3 py-2">
            <button type="button" class="text-error hover:text-error-700" onclick="removeTableRow('${rowId}')">
                <i class="fas fa-trash text-sm"></i>
            </button>
        </td>
    `;
    
    row.innerHTML = rowHtml;
    tbody.appendChild(row);
}

// Remove a table row
function removeTableRow(rowId) {
    const row = document.getElementById(rowId);
    if (row) {
        row.remove();
    }
}

// Open activity form modal
function openActivityForm() {
    const modal = document.getElementById('activity-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Load template if not already loaded
    if (!currentTemplate) {
        loadAccomplishmentTemplate();
    }
}

// Close activity form modal
function closeActivityForm() {
    const modal = document.getElementById('activity-modal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Reset form
    document.getElementById('activity-form').reset();
    
    // Clear edit ID
    document.getElementById('activity-form').dataset.editId = '';
    
    // Hide other type field
    document.getElementById('other-type-wrapper').classList.add('hidden');
    
    // Clear template sections
    const container = document.getElementById('template-sections');
    container.innerHTML = '<p class="text-text-secondary">Loading template...</p>';
}

// Submit activity form
async function submitActivity(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const activityData = {
        title: formData.get('title'),
        type: formData.get('type'),
        otherType: formData.get('otherType'),
        startDate: formData.get('startDate'),
        endDate: formData.get('endDate'),
        description: formData.get('description'),
        status: 'not_started',
        accomplishments: {}
    };
    
    // Collect accomplishment data
    if (currentTemplate) {
        currentTemplate.sections.forEach(section => {
            const sectionData = [];
            const inputs = document.querySelectorAll(`input[name^="${section.key}["]`);
            
            // Group inputs by row
            const rowGroups = {};
            inputs.forEach(input => {
                const nameMatch = input.name.match(/(\w+)\[(\d+)\]\[(\w+)\]/);
                if (nameMatch) {
                    const [, sectionKey, rowIndex, columnKey] = nameMatch;
                    if (!rowGroups[rowIndex]) {
                        rowGroups[rowIndex] = {};
                    }
                    rowGroups[rowIndex][columnKey] = input.value;
                }
            });
            
            // Convert to array
            Object.values(rowGroups).forEach(rowData => {
                if (Object.values(rowData).some(value => value.trim() !== '')) {
                    sectionData.push(rowData);
                }
            });
            
            if (sectionData.length > 0) {
                activityData.accomplishments[section.key] = {
                    title: section.title,
                    data: sectionData
                };
            }
        });
    }
    
    try {
        const editId = document.getElementById('activity-form').dataset.editId;
        const isEdit = editId && editId !== '';
        
        // Save to database
        const response = await fetch('/central-cmi/api/activities.php', {
            method: isEdit ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                ...activityData,
                id: editId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Create calendar event for new activities
            if (!isEdit) {
                await createCalendarEvent({
                    title: activityData.title,
                    startDate: activityData.startDate,
                    endDate: activityData.endDate,
                    details: activityData.description
                });
            }
            
            showNotification(`Activity ${isEdit ? 'updated' : 'created'} successfully!`, 'success');
            closeActivityForm();
            
            // Refresh the activities list
            await loadActivities();
        } else {
            showNotification(`Error ${isEdit ? 'updating' : 'creating'} activity: ` + result.error, 'error');
        }
    } catch (error) {
        console.error('Error saving activity:', error);
        showNotification('Error saving activity', 'error');
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 ${
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

// Database API functions
async function loadActivities() {
    try {
        const response = await fetch('/central-cmi/api/activities.php');
        const data = await response.json();
        
        if (data.success) {
            activities = data.activities;
            renderActivities();
        } else {
            console.error('Failed to load activities:', data.error);
            showNotification('Failed to load activities', 'error');
        }
    } catch (error) {
        console.error('Error loading activities:', error);
        showNotification('Error loading activities', 'error');
    }
}

// Calendar event functions
async function createCalendarEvent(eventData) {
    try {
        const response = await fetch('/central-cmi/api/calendar.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(eventData)
        });
        
        const result = await response.json();
        
        if (!result.success) {
            console.error('Failed to create calendar event:', result.error);
        }
    } catch (error) {
        console.error('Error creating calendar event:', error);
    }
}

// Render activities in the table
function renderActivities() {
    const tbody = document.getElementById('activities-table-body');
    const mobileView = document.getElementById('activities-mobile-view');
    
    if (activities.length === 0) {
        // Show empty state
        document.getElementById('empty-state').classList.remove('hidden');
        tbody.innerHTML = '';
        mobileView.innerHTML = '';
        return;
    }
    
    // Hide empty state
    document.getElementById('empty-state').classList.add('hidden');
    
    // Render desktop table
    tbody.innerHTML = activities.map(activity => `
        <tr class="hover:bg-secondary-50">
            <td class="px-6 py-4">
                <input type="checkbox" class="w-4 h-4 text-primary bg-surface border-secondary-300 rounded focus:ring-primary-500" onchange="updateBulkActions()" />
            </td>
            <td class="px-6 py-4 text-sm text-text-primary">${activity.title}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${activity.status || 'Not Started'}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${new Date(activity.reported_period_start).toLocaleDateString()}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${new Date(activity.reported_period_end).toLocaleDateString()}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${(activity.description || '').slice(0, 120)}${(activity.description || '').length > 120 ? '…' : ''}</td>
            <td class="px-6 py-4">
                <div class="flex space-x-2">
                    <button type="button" class="text-primary hover:text-primary-700" title="Edit" onclick="editActivity(${activity.ActivityID})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="text-text-secondary hover:text-primary" title="View" onclick="viewActivity(${activity.ActivityID})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="text-error hover:text-error-700" title="Delete" onclick="deleteActivity(${activity.ActivityID})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    // Render mobile cards
    mobileView.innerHTML = activities.map(activity => `
        <div class="bg-surface border border-secondary-200 rounded-lg p-4 mb-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-text-primary">${activity.title}</h3>
                    <p class="text-xs text-text-secondary mt-1">${activity.description || 'No description'}</p>
                </div>
                <div class="flex space-x-2 ml-2">
                    <button type="button" class="text-primary hover:text-primary-700" onclick="editActivity(${activity.ActivityID})">
                        <i class="fas fa-edit text-sm"></i>
                    </button>
                    <button type="button" class="text-error hover:text-error-700" onclick="deleteActivity(${activity.ActivityID})">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
            <div class="flex items-center justify-between text-xs mb-2">
                <span class="text-text-secondary">Status: ${activity.status || 'Not Started'}</span>
                <span class="text-text-secondary">${new Date(activity.reported_period_start).toLocaleDateString()} - ${new Date(activity.reported_period_end).toLocaleDateString()}</span>
            </div>
        </div>
    `).join('');
    
    // Update counts
    updateActivityCounts();
}

// Update activity counts
function updateActivityCounts() {
    const total = activities.length;
    const completed = activities.filter(a => a.status === 'completed').length;
    const inProgress = activities.filter(a => a.status === 'in_progress').length;
    const notStarted = activities.filter(a => a.status === 'not_started').length;
    
    document.getElementById('results-count').textContent = total;
    document.getElementById('total-count').textContent = total;
    
    // Update summary stats
    const summaryStats = document.querySelectorAll('.text-2xl.font-bold');
    if (summaryStats[0]) summaryStats[0].textContent = total;
    if (summaryStats[1]) summaryStats[1].textContent = completed;
    if (summaryStats[2]) summaryStats[2].textContent = inProgress;
    if (summaryStats[3]) summaryStats[3].textContent = notStarted;
}

// Activity management functions
function editActivity(id) {
    const activity = activities.find(a => a.ActivityID == id);
    if (activity) {
        // Fill form with activity data
        document.getElementById('activity-title').value = activity.title;
        document.getElementById('activity-type').value = activity.type || '';
        document.getElementById('activity-start-date').value = activity.reported_period_start;
        document.getElementById('activity-end-date').value = activity.reported_period_end;
        document.getElementById('activity-description').value = activity.description || '';
        
        // Handle other type field
        if (activity.type === 'others') {
            document.getElementById('other-type-wrapper').classList.remove('hidden');
            document.getElementById('other-type').value = activity.otherType || '';
        }
        
        // Open modal
        openActivityForm();
        
        // Store current activity ID for update
        document.getElementById('activity-form').dataset.editId = id;
    }
}

function viewActivity(id) {
    const activity = activities.find(a => a.ActivityID == id);
    if (activity) {
        console.log('Viewing activity:', activity);
        showNotification('Activity details logged to console', 'info');
    }
}

async function deleteActivity(id) {
    if (confirm('Are you sure you want to delete this activity?')) {
        try {
            const response = await fetch(`/central-cmi/api/activities.php?id=${id}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Activity deleted successfully!', 'success');
                await loadActivities();
            } else {
                showNotification('Error deleting activity: ' + result.error, 'error');
            }
        } catch (error) {
            console.error('Error deleting activity:', error);
            showNotification('Error deleting activity', 'error');
        }
    }
}

// Export activities
function exportActivities() {
    showNotification('Exporting activities...', 'info');
    // Implement export functionality
}

// Clear all activities (for testing purposes)
async function clearAllActivities() {
    if (confirm('Are you sure you want to delete ALL activities? This action cannot be undone.')) {
        try {
            // This would need to be implemented in the API
            showNotification('Clear all functionality not yet implemented', 'info');
        } catch (error) {
            console.error('Error clearing activities:', error);
            showNotification('Error clearing activities', 'error');
        }
    }
}

// Filter activities
function filterActivities() {
    // Implement filtering functionality
    console.log('Filtering activities...');
}

// Clear filters
function clearFilters() {
    document.getElementById('search').value = '';
    document.getElementById('status-filter').value = '';
    document.getElementById('category-filter').value = '';
    document.getElementById('date-from').value = '';
    document.getElementById('date-to').value = '';
    filterActivities();
}

// Sort table
function sortTable(column) {
    console.log('Sorting by:', column);
    // Implement sorting functionality
}

// Toggle select all
function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

// Update bulk actions visibility
function updateBulkActions() {
    const selectedCount = document.querySelectorAll('tbody input[type="checkbox"]:checked').length;
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCountSpan = document.getElementById('selected-count');
    
    if (selectedCount > 0) {
        bulkActions.style.display = 'block';
        selectedCountSpan.textContent = selectedCount;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Apply bulk action
function applyBulkAction() {
    const status = document.getElementById('bulk-status').value;
    if (status) {
        showNotification(`Updating ${document.getElementById('selected-count').textContent} activities to ${status}`, 'info');
        // Implement bulk update
    }
}

// Clear selection
function clearSelection() {
    const checkboxes = document.querySelectorAll('tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('select-all').checked = false;
    updateBulkActions();
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox' && e.target.closest('tbody')) {
            updateBulkActions();
        }
    });
    
    // Toggle other type field
    const typeSelect = document.getElementById('activity-type');
    const otherWrapper = document.getElementById('other-type-wrapper');
    if (typeSelect && otherWrapper) {
        typeSelect.addEventListener('change', function() {
            if (this.value === 'others') {
                otherWrapper.classList.remove('hidden');
            } else {
                otherWrapper.classList.add('hidden');
                document.getElementById('other-type').value = '';
            }
        });
    }
    
    // Load initial data
    loadActivities();
});
</script>

<?php include '../../includes/footer.php'; ?>