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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
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

            <!-- Type Filter -->
            <div>
                <label for="type-filter" class="block text-sm font-medium text-text-primary mb-2">Type of Event</label>
                <select id="type-filter" class="form-input" onchange="filterActivities()">
                    <option value="">All Types</option>
                    <option value="Seminar">Seminar</option>
                    <option value="Training">Training</option>
                    <option value="Workshop">Workshop</option>
                    <option value="Conference">Conference</option>
                    <option value="Meeting">Meeting</option>
                    <option value="Exhibit">Exhibit</option>
                    <option value="Field Day">Field Day</option>
                    <option value="Technology Forum">Technology Forum</option>
                    <option value="Lakbay-Aral">Lakbay-Aral</option>
                    <option value="Others">Others</option>
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
        </div>
    </section>

    <!-- Activity Form Modal -->
    <div id="activity-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-surface rounded-xl shadow-modal max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-secondary-200">
                    <div class="flex items-center justify-between">
                        <h3 id="modal-title" class="text-xl font-semibold text-text-primary">Create New Activity</h3>
                        <button type="button" class="text-text-secondary hover:text-text-primary" onclick="closeActivityForm()">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    <form id="activity-form" onsubmit="submitActivity(event)" enctype="multipart/form-data">
                        <!-- Title -->
                        <div class="mb-4">
                            <label for="activity-title" class="block text-sm font-medium text-text-primary mb-2">Title of Activity <span class="text-red-500">*</span></label>
                            <input type="text" id="activity-title" name="title" class="form-input" placeholder="Enter activity title" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="activity-description" class="block text-sm font-medium text-text-primary mb-2">Description <span class="text-red-500">*</span></label>
                            <textarea id="activity-description" name="description" rows="3" class="form-input" placeholder="Describe the activity..." required></textarea>
                        </div>

                        <!-- Date Range -->
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label for="activity-start-date" class="block text-sm font-medium text-text-primary mb-2">Start Date <span class="text-red-500">*</span></label>
                                <input type="date" id="activity-start-date" name="startDate" class="form-input" required>
                            </div>
                            <div>
                                <label for="activity-end-date" class="block text-sm font-medium text-text-primary mb-2">End Date <span class="text-red-500">*</span></label>
                                <input type="date" id="activity-end-date" name="endDate" class="form-input" required>
                            </div>
                        </div>

                        <!-- Venue -->
                        <div class="mb-4">
                            <label for="activity-venue" class="block text-sm font-medium text-text-primary mb-2">Venue <span class="text-red-500">*</span></label>
                            <input type="text" id="activity-venue" name="venue" class="form-input" placeholder="Enter venue/location" required>
                        </div>

                        <!-- Implementing Agency -->
                        <div class="mb-4">
                            <label for="implementing-agency" class="block text-sm font-medium text-text-primary mb-2">Implementing Agency <span class="text-red-500">*</span></label>
                            <select id="implementing-agency" name="implementingAgency" class="form-input" required>
                                <option value="">Select agency</option>
                                <option value="PCAARRD">PCAARRD</option>
                                <option value="DOST-IX">DOST-IX</option>
                                <option value="DA-RFO IX">DA-RFO IX</option>
                                <option value="WMSU">WMSU</option>
                                <option value="JHCSC">JHCSC</option>
                                <option value="DTI-IX">DTI-IX</option>
                                <option value="BFAR-IX">BFAR-IX</option>
                                <option value="NEDA-IX">NEDA-IX</option>
                                <option value="PRRI-IX">PRRI-IX</option>
                                <option value="PhilFIDA-IX">PhilFIDA-IX</option>
                                <option value="DA-BAR">DA-BAR</option>
                                <option value="PCA-ZRC">PCA-ZRC</option>
                            </select>
                        </div>

                        <!-- Collaborating Agency (Optional) -->
                        <div class="mb-4">
                            <label for="collaborating-agency" class="block text-sm font-medium text-text-primary mb-2">Collaborating Agency <span class="text-text-secondary text-xs">(Optional)</span></label>
                            <select id="collaborating-agency" name="collaboratingAgency" class="form-input">
                                <option value="">Select agency (optional)</option>
                                <option value="PCAARRD">PCAARRD</option>
                                <option value="DOST-IX">DOST-IX</option>
                                <option value="DA-RFO IX">DA-RFO IX</option>
                                <option value="WMSU">WMSU</option>
                                <option value="JHCSC">JHCSC</option>
                                <option value="DTI-IX">DTI-IX</option>
                                <option value="BFAR-IX">BFAR-IX</option>
                                <option value="NEDA-IX">NEDA-IX</option>
                                <option value="PRRI-IX">PRRI-IX</option>
                                <option value="PhilFIDA-IX">PhilFIDA-IX</option>
                                <option value="DA-BAR">DA-BAR</option>
                                <option value="PCA-ZRC">PCA-ZRC</option>
                            </select>
                        </div>

                        <!-- Participants Count and Budget Amount -->
                        <div class="mb-4 grid grid-cols-2 gap-4">
                            <div>
                                <label for="participants-count" class="block text-sm font-medium text-text-primary mb-2">Participants Count <span class="text-text-secondary text-xs">(Optional)</span></label>
                                <input type="number" id="participants-count" name="participantsCount" class="form-input" placeholder="Enter number" min="0">
                            </div>
                            <div>
                                <label for="budget-amount" class="block text-sm font-medium text-text-primary mb-2">Budget Amount <span class="text-text-secondary text-xs">(Optional)</span></label>
                                <input type="number" id="budget-amount" name="budgetAmount" class="form-input" placeholder="Enter amount" min="0" step="0.01">
                            </div>
                        </div>

                        <!-- Type of Event -->
                        <div class="mb-4">
                            <label for="activity-type" class="block text-sm font-medium text-text-primary mb-2">Type of Event <span class="text-red-500">*</span></label>
                            <select id="activity-type" name="type" class="form-input" required>
                                <option value="">Select event type</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Training">Training</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Conference">Conference</option>
                                <option value="Meeting">Meeting</option>
                                <option value="Exhibit">Exhibit</option>
                                <option value="Field Day">Field Day</option>
                                <option value="Technology Forum">Technology Forum</option>
                                <option value="Lakbay-Aral">Lakbay-Aral</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>

                        <!-- Other Type Field (shows when Others is selected) -->
                        <div id="other-type-wrapper" class="mb-4 hidden">
                            <label for="other-type" class="block text-sm font-medium text-text-primary mb-2">Specify Type <span class="text-red-500">*</span></label>
                            <input type="text" id="other-type" name="otherType" class="form-input" placeholder="Please specify the event type">
                        </div>

                        <!-- Photo Documentation -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-text-primary mb-2">Photo Documentation <span class="text-text-secondary text-xs">(Optional)</span></label>
                            <div class="border-2 border-dashed border-secondary-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                                <input type="file" id="activity-photos" name="images[]" multiple accept="image/*" class="hidden" onchange="handlePhotoSelection(event)">
                                <label for="activity-photos" class="cursor-pointer">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-secondary-400 mb-2"></i>
                                        <p class="text-text-secondary text-sm">Click to upload or drag and drop</p>
                                        <p class="text-text-secondary text-xs mt-1">PNG, JPG, GIF up to 5MB each (multiple allowed)</p>
                                    </div>
                                </label>
                            </div>
                            <!-- Photo Preview -->
                            <div id="photo-preview" class="grid grid-cols-3 gap-2 mt-4"></div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end space-x-3 pt-6 border-t border-secondary-200">
                            <button type="button" class="btn-secondary" onclick="closeActivityForm()">Cancel</button>
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save mr-2"></i><span id="submit-btn-text">Create Activity</span>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Venue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Implementing Agency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Actions</th>
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
let activities = [];
let selectedPhotos = [];

// Handle photo selection and preview
function handlePhotoSelection(event) {
    const files = event.target.files;
    const previewContainer = document.getElementById('photo-preview');
    
    // Store selected files
    selectedPhotos = Array.from(files);
    
    // Clear previous previews
    previewContainer.innerHTML = '';
    
    // Create previews
    selectedPhotos.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-24 object-cover rounded-lg border border-secondary-200">
                <button type="button" onclick="removePhoto(${index})" class="absolute -top-2 -right-2 bg-error text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-error-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
            previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// Remove a photo from selection
function removePhoto(index) {
    selectedPhotos.splice(index, 1);
    
    // Update the file input
    const dataTransfer = new DataTransfer();
    selectedPhotos.forEach(file => dataTransfer.items.add(file));
    document.getElementById('activity-photos').files = dataTransfer.files;
    
    // Re-render previews
    const previewContainer = document.getElementById('photo-preview');
    previewContainer.innerHTML = '';
    selectedPhotos.forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${idx + 1}" class="w-full h-24 object-cover rounded-lg border border-secondary-200">
                <button type="button" onclick="removePhoto(${idx})" class="absolute -top-2 -right-2 bg-error text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-error-700">
                    <i class="fas fa-times"></i>
                </button>
            `;
            previewContainer.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// Open activity form modal
function openActivityForm() {
    const modal = document.getElementById('activity-modal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Reset modal title and button text for create mode
    document.getElementById('modal-title').textContent = 'Create New Activity';
    document.getElementById('submit-btn-text').textContent = 'Create Activity';
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
    
    // Clear photo previews
    document.getElementById('photo-preview').innerHTML = '';
    selectedPhotos = [];
    
    // Reset modal title and button
    document.getElementById('modal-title').textContent = 'Create New Activity';
    document.getElementById('submit-btn-text').textContent = 'Create Activity';
}

// Submit activity form
async function submitActivity(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    const editId = form.dataset.editId;
    const isEdit = editId && editId !== '';
    
    // For edit mode with FormData, add method override
    if (isEdit) {
        formData.append('_method', 'PUT');
        formData.append('id', editId);
    }
    
    try {
        const response = await fetch(BASE_URL + 'api/activities.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Create calendar event for new activities
            if (!isEdit) {
                await createCalendarEvent({
                    title: formData.get('title'),
                    startDate: formData.get('startDate'),
                    endDate: formData.get('endDate'),
                    details: formData.get('description')
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
        const response = await fetch(BASE_URL + 'api/activities.php');
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
        const response = await fetch(BASE_URL + 'api/calendar.php', {
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
            <td class="px-6 py-4 text-sm text-text-primary font-medium">${activity.title}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${activity.type || '-'}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${activity.reported_period_start ? new Date(activity.reported_period_start).toLocaleDateString() : '-'}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${activity.location || '-'}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${activity.implementing_agency || '-'}</td>
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
                    <p class="text-xs text-text-secondary mt-1">${activity.type || 'No type'} | ${activity.location || 'No venue'}</p>
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
                <span class="text-text-secondary">${activity.implementing_agency || '-'}</span>
                <span class="text-text-secondary">${activity.reported_period_start ? new Date(activity.reported_period_start).toLocaleDateString() : '-'}</span>
            </div>
        </div>
    `).join('');
    
    // Update counts
    updateActivityCounts();
}

// Update activity counts
function updateActivityCounts(filteredCount = null) {
    const total = activities.length;
    document.getElementById('results-count').textContent = filteredCount !== null ? filteredCount : total;
    document.getElementById('total-count').textContent = total;
}

// Activity management functions
function editActivity(id) {
    const activity = activities.find(a => a.ActivityID == id);
    if (activity) {
        // Fill form with activity data
        document.getElementById('activity-title').value = activity.title || '';
        document.getElementById('activity-description').value = activity.description || '';
        document.getElementById('activity-start-date').value = activity.reported_period_start || '';
        document.getElementById('activity-end-date').value = activity.reported_period_end || '';
        document.getElementById('activity-venue').value = activity.location || '';
        document.getElementById('implementing-agency').value = activity.implementing_agency || '';
        document.getElementById('collaborating-agency').value = activity.collaborating_agency || '';
        document.getElementById('participants-count').value = activity.participants_count || '';
        document.getElementById('budget-amount').value = activity.budget_amount || '';
        document.getElementById('activity-type').value = activity.type || '';
        
        // Handle other type field
        if (activity.type === 'Others') {
            document.getElementById('other-type-wrapper').classList.remove('hidden');
            document.getElementById('other-type').value = activity.otherType || '';
        }
        
        // Update modal title and button
        document.getElementById('modal-title').textContent = 'Edit Activity';
        document.getElementById('submit-btn-text').textContent = 'Update Activity';
        
        // Open modal
        const modal = document.getElementById('activity-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
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
    const search = document.getElementById('search').value.toLowerCase();
    const typeFilter = document.getElementById('type-filter').value;
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;
    
    let filtered = activities.filter(activity => {
        // Search filter
        if (search) {
            const title = (activity.title || '').toLowerCase();
            const description = (activity.description || '').toLowerCase();
            const venue = (activity.location || '').toLowerCase();
            if (!title.includes(search) && !description.includes(search) && !venue.includes(search)) {
                return false;
            }
        }
        
        // Type filter
        if (typeFilter && activity.type !== typeFilter) {
            return false;
        }
        
        // Date range filter
        if (dateFrom && activity.reported_period_start < dateFrom) {
            return false;
        }
        if (dateTo && activity.reported_period_start > dateTo) {
            return false;
        }
        
        return true;
    });
    
    renderFilteredActivities(filtered);
    updateActivityCounts(filtered.length);
}

// Render filtered activities
function renderFilteredActivities(filteredActivities) {
    const tbody = document.getElementById('activities-table-body');
    const mobileView = document.getElementById('activities-mobile-view');
    
    if (filteredActivities.length === 0) {
        document.getElementById('empty-state').classList.remove('hidden');
        tbody.innerHTML = '';
        mobileView.innerHTML = '';
        return;
    }
    
    document.getElementById('empty-state').classList.add('hidden');
    
    // Desktop table
    tbody.innerHTML = filteredActivities.map(activity => `
        <tr class="hover:bg-secondary-50">
            <td class="px-6 py-4 text-sm text-text-primary font-medium">${activity.title}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${activity.type || '-'}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${activity.reported_period_start ? new Date(activity.reported_period_start).toLocaleDateString() : '-'}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${activity.location || '-'}</td>
            <td class="px-6 py-4 text-sm text-text-secondary">${activity.implementing_agency || '-'}</td>
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
    
    // Mobile cards
    mobileView.innerHTML = filteredActivities.map(activity => `
        <div class="bg-surface border border-secondary-200 rounded-lg p-4 mb-4">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="text-sm font-medium text-text-primary">${activity.title}</h3>
                    <p class="text-xs text-text-secondary mt-1">${activity.type || 'No type'} | ${activity.location || 'No venue'}</p>
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
                <span class="text-text-secondary">${activity.implementing_agency || '-'}</span>
                <span class="text-text-secondary">${activity.reported_period_start ? new Date(activity.reported_period_start).toLocaleDateString() : '-'}</span>
            </div>
        </div>
    `).join('');
}

// Clear filters
function clearFilters() {
    document.getElementById('search').value = '';
    document.getElementById('type-filter').value = '';
    document.getElementById('date-from').value = '';
    document.getElementById('date-to').value = '';
    renderActivities();
    updateActivityCounts();
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Toggle other type field
    const typeSelect = document.getElementById('activity-type');
    const otherWrapper = document.getElementById('other-type-wrapper');
    if (typeSelect && otherWrapper) {
        typeSelect.addEventListener('change', function() {
            if (this.value === 'Others') {
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