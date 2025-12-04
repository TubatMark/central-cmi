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
                Showing <span id="results-count" class="font-medium text-text-primary">0</span> of 
                <span id="total-count" class="font-medium text-text-primary">0</span> activities
            </div>
            <div class="flex flex-wrap gap-4 text-sm">
                <span class="flex items-center">
                    <div class="w-3 h-3 bg-success rounded-full mr-2"></div>
                    <span class="text-text-secondary">Completed: <span id="count-completed" class="font-medium text-text-primary">0</span></span>
                </span>
                <span class="flex items-center">
                    <div class="w-3 h-3 bg-warning rounded-full mr-2"></div>
                    <span class="text-text-secondary">In Progress: <span id="count-in-progress" class="font-medium text-text-primary">0</span></span>
                </span>
                <span class="flex items-center">
                    <div class="w-3 h-3 bg-secondary-400 rounded-full mr-2"></div>
                    <span class="text-text-secondary">Not Started: <span id="count-not-started" class="font-medium text-text-primary">0</span></span>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="activities-table-body" class="bg-surface divide-y divide-secondary-200">
                    <!-- Loading state -->
                    <tr id="loading-row">
                        <td colspan="6" class="px-6 py-8 text-center text-text-secondary">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading activities...
                        </td>
                    </tr>
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

<!-- Add/Edit Activity Modal -->
<div id="add-activity-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-surface rounded-xl shadow-xl border border-secondary-200 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modal-title" class="text-lg font-semibold text-text-primary">Add Activity</h3>
                <button type="button" onclick="closeActivityForm()" class="text-text-secondary hover:text-primary">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="add-activity-form" onsubmit="return submitActivity(event)">
                <input type="hidden" id="edit-activity-id" value="" />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-text-primary mb-2">Title <span class="text-error">*</span></label>
                        <input type="text" id="activity-title" name="title" class="form-input" placeholder="Enter activity title" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">Type <span class="text-error">*</span></label>
                        <select id="activity-type" name="type" class="form-input" required>
                            <option value="">Choose type</option>
                            <option value="event">Event</option>
                            <option value="training">Training</option>
                            <option value="fiesta">Fiesta</option>
                            <option value="exhibit">Exhibit</option>
                            <option value="meeting">Meeting</option>
                            <option value="others">Others</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">Status <span class="text-error">*</span></label>
                        <select id="activity-status" name="status" class="form-input" required>
                            <option value="not_started">Not Started</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">Start Date <span class="text-error">*</span></label>
                        <input type="date" id="date-start" name="dateStart" class="form-input" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">End Date <span class="text-error">*</span></label>
                        <input type="date" id="date-end" name="dateEnd" class="form-input" required />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-text-primary mb-2">Description</label>
                        <textarea id="activity-description" name="description" rows="3" class="form-input" placeholder="Describe the activity"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">Location</label>
                        <input type="text" id="activity-location" name="location" class="form-input" placeholder="Enter location" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-text-primary mb-2">Participants Count</label>
                        <input type="number" id="activity-participants" name="participants_count" class="form-input" placeholder="0" min="0" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-text-primary mb-2">Attachments (Images)</label>
                        <div class="border-2 border-dashed border-secondary-300 rounded-lg p-4 text-center hover:border-primary transition-colors">
                            <input type="file" id="activity-images" name="images" multiple accept="image/*" class="hidden" onchange="previewImages(this)" />
                            <label for="activity-images" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-3xl text-secondary-400 mb-2"></i>
                                <p class="text-sm text-text-secondary">Click to upload images or drag and drop</p>
                                <p class="text-xs text-text-secondary mt-1">PNG, JPG, GIF up to 5MB each</p>
                            </label>
                        </div>
                        <div id="image-preview" class="grid grid-cols-4 gap-2 mt-3"></div>
                        <div id="existing-attachments" class="mt-3"></div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" class="btn-secondary" onclick="closeActivityForm()">Cancel</button>
                    <button type="submit" id="submit-btn" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Save Activity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Global data store
    let allActivities = [];
    const API_URL = '/central-cmi/api/activities.php';

    // Load activities from database
    async function loadActivities() {
        try {
            const response = await fetch(API_URL);
            const data = await response.json();
            
            if (data.success) {
                allActivities = data.activities || [];
                renderActivities(allActivities);
                updateStatusCounts();
            } else {
                showError('Failed to load activities: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error loading activities:', error);
            showError('Error connecting to server');
        }
    }

    // Render activities table
    function renderActivities(activities) {
        const tbody = document.getElementById('activities-table-body');
        const mobile = document.getElementById('activities-mobile-view');
        const emptyState = document.getElementById('empty-state');

        if (activities.length === 0) {
            tbody.innerHTML = '';
            mobile.innerHTML = '';
            emptyState.classList.remove('hidden');
            document.getElementById('results-count').textContent = '0';
            document.getElementById('total-count').textContent = '0';
            return;
        }

        emptyState.classList.add('hidden');

        // Desktop table
        tbody.innerHTML = activities.map(activity => {
            const statusClass = getStatusClass(activity.status);
            const statusLabel = getStatusLabel(activity.status);
            const startDate = formatDate(activity.reported_period_start);
            const endDate = formatDate(activity.reported_period_end);

            return `
                <tr class="hover:bg-secondary-50 transition-micro" data-id="${activity.ActivityID}">
                    <td class="px-6 py-4">
                        <input type="checkbox" class="activity-checkbox w-4 h-4 text-primary bg-surface border-secondary-300 rounded" data-id="${activity.ActivityID}" onchange="updateBulkActions()" />
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-text-primary">${escapeHtml(activity.title)}</div>
                        <div class="text-xs text-text-secondary">${escapeHtml((activity.description || '').substring(0, 60))}${activity.description && activity.description.length > 60 ? '...' : ''}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="status-badge ${statusClass}">${statusLabel}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-text-secondary">${startDate}</td>
                    <td class="px-6 py-4 text-sm text-text-secondary">${endDate}</td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button class="text-secondary-600 hover:text-secondary-800 text-sm" title="View" onclick="viewActivity(${activity.ActivityID})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="text-primary hover:text-primary-700 text-sm" title="Edit" onclick="editActivity(${activity.ActivityID})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-error hover:text-error-700 text-sm" title="Delete" onclick="deleteActivity(${activity.ActivityID})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        // Mobile cards
        mobile.innerHTML = activities.map(activity => {
            const statusClass = getStatusClass(activity.status);
            const statusLabel = getStatusLabel(activity.status);
            const startDate = formatDate(activity.reported_period_start);
            const endDate = formatDate(activity.reported_period_end);

            return `
                <div class="border-b border-secondary-200 p-4 hover:bg-secondary-50" data-id="${activity.ActivityID}">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-text-primary">${escapeHtml(activity.title)}</h3>
                            <p class="text-xs text-text-secondary mt-1">${startDate} - ${endDate}</p>
                        </div>
                        <span class="status-badge ${statusClass} ml-2">${statusLabel}</span>
                    </div>
                    <div class="flex justify-end space-x-3 mt-2">
                        <button class="text-secondary-600 hover:text-secondary-800 text-sm" onclick="viewActivity(${activity.ActivityID})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="text-primary hover:text-primary-700 text-sm" onclick="editActivity(${activity.ActivityID})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="text-error hover:text-error-700 text-sm" onclick="deleteActivity(${activity.ActivityID})">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        document.getElementById('results-count').textContent = activities.length;
        document.getElementById('total-count').textContent = allActivities.length;
    }

    // Update status counts
    function updateStatusCounts() {
        const completed = allActivities.filter(a => a.status === 'completed').length;
        const inProgress = allActivities.filter(a => a.status === 'in_progress').length;
        const notStarted = allActivities.filter(a => a.status === 'not_started').length;

        document.getElementById('count-completed').textContent = completed;
        document.getElementById('count-in-progress').textContent = inProgress;
        document.getElementById('count-not-started').textContent = notStarted;
    }

    // Get status CSS class
    function getStatusClass(status) {
        const classes = {
            'completed': 'bg-success-100 text-success-700',
            'in_progress': 'bg-warning-100 text-warning-700',
            'not_started': 'bg-secondary-100 text-secondary-700'
        };
        return classes[status] || 'bg-secondary-100 text-secondary-700';
    }

    // Get status label
    function getStatusLabel(status) {
        const labels = {
            'completed': 'Completed',
            'in_progress': 'In Progress',
            'not_started': 'Not Started'
        };
        return labels[status] || status;
    }

    // Open activity form (add/edit)
    function openActivityForm(activityId = null) {
        const modal = document.getElementById('add-activity-modal');
        const form = document.getElementById('add-activity-form');
        const title = document.getElementById('modal-title');
        
        form.reset();
        document.getElementById('edit-activity-id').value = '';
        document.getElementById('image-preview').innerHTML = '';
        document.getElementById('existing-attachments').innerHTML = '';
        
        if (activityId) {
            const activity = allActivities.find(a => a.ActivityID == activityId);
            if (activity) {
                title.textContent = 'Edit Activity';
                document.getElementById('edit-activity-id').value = activityId;
                document.getElementById('activity-title').value = activity.title || '';
                document.getElementById('activity-type').value = activity.type || '';
                document.getElementById('activity-status').value = activity.status || 'not_started';
                document.getElementById('date-start').value = activity.reported_period_start || '';
                document.getElementById('date-end').value = activity.reported_period_end || '';
                document.getElementById('activity-description').value = activity.description || '';
                document.getElementById('activity-location').value = activity.location || '';
                document.getElementById('activity-participants').value = activity.participants_count || '';
                
                // Show existing attachments
                if (activity.attachments && activity.attachments.length > 0) {
                    const existingDiv = document.getElementById('existing-attachments');
                    existingDiv.innerHTML = '<p class="text-sm text-text-secondary mb-2">Existing attachments:</p><div class="grid grid-cols-4 gap-2">' +
                        activity.attachments.map(att => `
                            <div class="relative group">
                                <img src="/central-cmi/uploads/activities/${att.filename}" alt="${att.original_name}" class="w-full h-20 object-cover rounded border border-secondary-200" />
                                <p class="text-xs text-text-secondary truncate mt-1">${att.original_name}</p>
                            </div>
                        `).join('') + '</div>';
                }
            }
        } else {
            title.textContent = 'Add Activity';
        }
        
        modal.classList.remove('hidden');
    }

    function closeActivityForm() {
        document.getElementById('add-activity-modal').classList.add('hidden');
        document.getElementById('add-activity-form').reset();
        document.getElementById('edit-activity-id').value = '';
        document.getElementById('image-preview').innerHTML = '';
        document.getElementById('existing-attachments').innerHTML = '';
    }

    // Preview selected images
    function previewImages(input) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        
        if (input.files) {
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}" class="w-full h-20 object-cover rounded border border-secondary-200" />
                        <button type="button" onclick="removePreviewImage(${index})" class="absolute -top-2 -right-2 bg-error text-white rounded-full w-5 h-5 text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-times"></i>
                        </button>
                        <p class="text-xs text-text-secondary truncate mt-1">${file.name}</p>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    // Submit activity form
    async function submitActivity(e) {
        e.preventDefault();
        
        const editId = document.getElementById('edit-activity-id').value;
        const isEditing = editId && editId !== '';
        
        const title = document.getElementById('activity-title').value.trim();
        const type = document.getElementById('activity-type').value;
        const status = document.getElementById('activity-status').value;
        const startDate = document.getElementById('date-start').value;
        const endDate = document.getElementById('date-end').value;

        // Validation
        if (!title || !type || !startDate || !endDate) {
            showToast('Please fill in all required fields', 'error');
            return false;
        }

        // Use FormData for file uploads
        const formData = new FormData();
        formData.append('title', title);
        formData.append('type', type);
        formData.append('status', status);
        formData.append('startDate', startDate);
        formData.append('endDate', endDate);
        formData.append('description', document.getElementById('activity-description').value.trim());
        formData.append('location', document.getElementById('activity-location').value.trim());
        formData.append('participants_count', document.getElementById('activity-participants').value || '');
        
        // Add images
        const imageInput = document.getElementById('activity-images');
        if (imageInput.files.length > 0) {
            Array.from(imageInput.files).forEach(file => {
                formData.append('images[]', file);
            });
        }

        try {
            let response;
            if (isEditing) {
                formData.append('id', editId);
                formData.append('_method', 'PUT');
            }
            
            response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                closeActivityForm();
                await loadActivities();
                showToast(isEditing ? 'Activity updated successfully' : 'Activity created successfully', 'success');
            } else {
                showToast(result.error || 'Failed to save activity', 'error');
            }
        } catch (error) {
            console.error('Error saving activity:', error);
            showToast('Error saving activity', 'error');
        }

        return false;
    }

    // Edit activity
    function editActivity(id) {
        openActivityForm(id);
    }

    // Delete activity
    async function deleteActivity(id) {
        if (!confirm('Are you sure you want to delete this activity? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch(`${API_URL}?id=${id}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                await loadActivities();
                showToast('Activity deleted successfully', 'success');
            } else {
                showToast(result.error || 'Failed to delete activity', 'error');
            }
        } catch (error) {
            console.error('Error deleting activity:', error);
            showToast('Error deleting activity', 'error');
        }
    }

    // Filter activities
    function filterActivities() {
        const search = document.getElementById('search').value.toLowerCase();
        const statusFilter = document.getElementById('status-filter').value;
        const dateFrom = document.getElementById('date-from').value;
        const dateTo = document.getElementById('date-to').value;

        let filtered = allActivities.filter(activity => {
            let match = true;

            // Search filter
            if (search) {
                const titleMatch = (activity.title || '').toLowerCase().includes(search);
                const descMatch = (activity.description || '').toLowerCase().includes(search);
                match = match && (titleMatch || descMatch);
            }

            // Status filter
            if (statusFilter) {
                const statusMap = {
                    'not-started': 'not_started',
                    'in-progress': 'in_progress',
                    'completed': 'completed'
                };
                match = match && activity.status === statusMap[statusFilter];
            }

            // Date range filter
            if (dateFrom) {
                match = match && activity.reported_period_start >= dateFrom;
            }
            if (dateTo) {
                match = match && activity.reported_period_end <= dateTo;
            }

            return match;
        });

        renderActivities(filtered);
    }

    // Clear filters
    function clearFilters() {
        document.getElementById('search').value = '';
        document.getElementById('status-filter').value = '';
        document.getElementById('category-filter').value = '';
        document.getElementById('date-from').value = '';
        document.getElementById('date-to').value = '';
        renderActivities(allActivities);
    }

    // Bulk actions
    function toggleSelectAll() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.activity-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checked = document.querySelectorAll('.activity-checkbox:checked');
        const bulkActions = document.getElementById('bulk-actions');
        const selectedCount = document.getElementById('selected-count');
        
        if (checked.length > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = checked.length;
        } else {
            bulkActions.style.display = 'none';
        }
    }

    async function applyBulkAction() {
        const status = document.getElementById('bulk-status').value;
        if (!status) {
            showToast('Please select a status', 'warning');
            return;
        }

        const checked = document.querySelectorAll('.activity-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.dataset.id);

        if (ids.length === 0) {
            showToast('No activities selected', 'warning');
            return;
        }

        const statusMap = {
            'not-started': 'not_started',
            'in-progress': 'in_progress',
            'completed': 'completed'
        };

        try {
            for (const id of ids) {
                await fetch(API_URL, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: parseInt(id), status: statusMap[status] })
                });
            }
            
            await loadActivities();
            clearSelection();
            showToast(`${ids.length} activities updated`, 'success');
        } catch (error) {
            console.error('Error updating activities:', error);
            showToast('Error updating activities', 'error');
        }
    }

    function clearSelection() {
        document.getElementById('select-all').checked = false;
        document.querySelectorAll('.activity-checkbox').forEach(cb => cb.checked = false);
        updateBulkActions();
    }

    // Export activities
    function exportActivities() {
        showToast('Export feature coming soon', 'info');
    }

    // Helper functions
    function formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showError(message) {
        const tbody = document.getElementById('activities-table-body');
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-error">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <p>${message}</p>
                    <button onclick="loadActivities()" class="mt-2 text-primary hover:text-primary-700">
                        <i class="fas fa-redo mr-1"></i> Retry
                    </button>
                </td>
            </tr>
        `;
    }

    // View activity details modal
    function viewActivity(activityId) {
        const activity = allActivities.find(a => a.ActivityID == activityId);
        if (!activity) {
            showToast('Activity not found', 'error');
            return;
        }

        const statusClass = getStatusClass(activity.status);
        const statusLabel = getStatusLabel(activity.status);
        const startDate = formatDate(activity.reported_period_start);
        const endDate = formatDate(activity.reported_period_end);
        const attachments = activity.attachments || [];

        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
        modal.id = 'view-activity-modal';
        modal.innerHTML = `
            <div class="bg-surface rounded-xl shadow-xl border border-secondary-200 max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-text-primary">${escapeHtml(activity.title)}</h3>
                            <p class="text-sm text-text-secondary mt-1">Activity Details</p>
                        </div>
                        <button type="button" onclick="document.getElementById('view-activity-modal').remove()" class="text-text-secondary hover:text-primary p-2">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-medium text-text-secondary uppercase mb-1">Status</label>
                            <span class="status-badge ${statusClass}">${statusLabel}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary uppercase mb-1">Type</label>
                            <p class="text-sm text-text-primary">${escapeHtml(activity.type || 'N/A')}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary uppercase mb-1">Start Date</label>
                            <p class="text-sm text-text-primary">${startDate}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary uppercase mb-1">End Date</label>
                            <p class="text-sm text-text-primary">${endDate}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary uppercase mb-1">Location</label>
                            <p class="text-sm text-text-primary">${escapeHtml(activity.location || 'N/A')}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-text-secondary uppercase mb-1">Participants</label>
                            <p class="text-sm text-text-primary">${activity.participants_count || 'N/A'}</p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-medium text-text-secondary uppercase mb-2">Description</label>
                        <p class="text-sm text-text-primary bg-secondary-50 rounded-lg p-4">${escapeHtml(activity.description || 'No description provided.')}</p>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-text-secondary uppercase mb-2">Attachments (${attachments.length})</label>
                        ${attachments.length > 0 ? `
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                ${attachments.map(att => `
                                    <a href="/central-cmi/uploads/activities/${att.filename}" target="_blank" class="group block border border-secondary-200 rounded-lg overflow-hidden hover:border-primary transition-colors">
                                        <img src="/central-cmi/uploads/activities/${att.filename}" alt="${escapeHtml(att.original_name)}" class="w-full h-32 object-cover" />
                                        <div class="p-2 bg-secondary-50 group-hover:bg-primary-50 transition-colors">
                                            <p class="text-xs text-text-secondary truncate">${escapeHtml(att.original_name)}</p>
                                        </div>
                                    </a>
                                `).join('')}
                            </div>
                        ` : `
                            <p class="text-sm text-text-secondary italic">No attachments uploaded.</p>
                        `}
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-secondary-200 flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('view-activity-modal').remove()" class="btn-secondary">
                            Close
                        </button>
                        <button type="button" onclick="document.getElementById('view-activity-modal').remove(); editActivity(${activity.ActivityID})" class="btn-primary">
                            <i class="fas fa-edit mr-2"></i>Edit Activity
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.remove();
        });
    }

    // View attachments modal
    function viewAttachments(activityId) {
        const activity = allActivities.find(a => a.ActivityID == activityId);
        if (!activity || !activity.attachments || activity.attachments.length === 0) {
            showToast('No attachments found', 'info');
            return;
        }

        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
        modal.id = 'attachments-modal';
        modal.innerHTML = `
            <div class="bg-surface rounded-xl shadow-xl border border-secondary-200 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-text-primary">Attachments - ${escapeHtml(activity.title)}</h3>
                        <button type="button" onclick="document.getElementById('attachments-modal').remove()" class="text-text-secondary hover:text-primary">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        ${activity.attachments.map(att => `
                            <div class="border border-secondary-200 rounded-lg overflow-hidden">
                                <a href="/central-cmi/uploads/activities/${att.filename}" target="_blank">
                                    <img src="/central-cmi/uploads/activities/${att.filename}" alt="${att.original_name}" class="w-full h-32 object-cover" />
                                </a>
                                <div class="p-2">
                                    <p class="text-xs text-text-secondary truncate">${att.original_name}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.remove();
        });
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;
        
        const colors = {
            success: 'bg-success-100 text-success-700 border border-success-200',
            error: 'bg-error-100 text-error-700 border border-error-200',
            warning: 'bg-warning-100 text-warning-700 border border-warning-200',
            info: 'bg-primary-100 text-primary-700 border border-primary-200'
        };
        
        toast.className += ` ${colors[type] || colors.info}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 opacity-70 hover:opacity-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-x-full'), 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadActivities();
    });
</script>

<?php include '../../includes/footer.php'; ?>