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

            <!-- CMI Filter -->
            <div>
                <label for="cmi-filter" class="block text-sm font-medium text-text-primary mb-2">CMI / Agency</label>
                <select id="cmi-filter" class="form-input" onchange="filterActivities()">
                    <option value="">All CMIs</option>
                    <!-- Options populated dynamically from activities data -->
                </select>
            </div>

            <!-- Type of Event Filter -->
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

    <!-- Activities Table -->
    <section class="bg-surface rounded-xl shadow-card border border-secondary-200 overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-secondary-200">
                <thead class="bg-secondary-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">CMI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Venue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Implementing Agency</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="activities-table-body" class="bg-surface divide-y divide-secondary-200">
                    <!-- Loading state -->
                    <tr id="loading-row">
                        <td colspan="7" class="px-6 py-8 text-center text-text-secondary">
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
        <div class="p-6 border-b border-secondary-200">
            <div class="flex items-center justify-between">
                <h3 id="modal-title" class="text-xl font-semibold text-text-primary">Create New Activity</h3>
                <button type="button" onclick="closeActivityForm()" class="text-text-secondary hover:text-text-primary">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <form id="add-activity-form" onsubmit="return submitActivity(event)" enctype="multipart/form-data">
                <input type="hidden" id="edit-activity-id" value="" />
                
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
                        <input type="file" id="activity-images" name="images[]" multiple accept="image/*" class="hidden" onchange="handlePhotoSelection(event)">
                        <label for="activity-images" class="cursor-pointer">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-secondary-400 mb-2"></i>
                                <p class="text-text-secondary text-sm">Click to upload or drag and drop</p>
                                <p class="text-text-secondary text-xs mt-1">PNG, JPG, GIF up to 5MB each (multiple allowed)</p>
                            </div>
                        </label>
                    </div>
                    <!-- Photo Preview -->
                    <div id="photo-preview" class="grid grid-cols-3 gap-2 mt-4"></div>
                    <div id="existing-attachments" class="mt-3"></div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-secondary-200">
                    <button type="button" class="btn-secondary" onclick="closeActivityForm()">Cancel</button>
                    <button type="submit" id="submit-btn" class="btn-primary">
                        <i class="fas fa-save mr-2"></i><span id="submit-btn-text">Create Activity</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Global data store
    let allActivities = [];
    const API_URL = BASE_URL + 'api/activities.php';

    // Load activities from database
    async function loadActivities() {
        try {
            const response = await fetch(API_URL);
            const data = await response.json();
            
            if (data.success) {
                allActivities = data.activities || [];
                renderActivities(allActivities);
                updateStatusCounts();
                populateCmiFilter();
            } else {
                showError('Failed to load activities: ' + (data.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error loading activities:', error);
            showError('Error connecting to server');
        }
    }

    // Populate CMI filter dropdown dynamically based on implementing agencies
    function populateCmiFilter() {
        const cmiFilter = document.getElementById('cmi-filter');
        const currentValue = cmiFilter.value;
        
        // Get unique agencies from activities (based on implementing agency)
        const agencies = [...new Set(
            allActivities
                .map(a => a.implementing_agency)
                .filter(agency => agency && agency.trim() !== '')
        )].sort();
        
        // Clear existing options except the first one
        cmiFilter.innerHTML = '<option value="">All CMIs</option>';
        
        // Add agency options
        agencies.forEach(agency => {
            const option = document.createElement('option');
            option.value = agency;
            option.textContent = agency;
            cmiFilter.appendChild(option);
        });
        
        // Restore previous selection if still valid
        if (currentValue && agencies.includes(currentValue)) {
            cmiFilter.value = currentValue;
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
            const activityDate = formatDate(activity.reported_period_start);
            const cmi = activity.implementing_agency || '-';

            return `
                <tr class="hover:bg-secondary-50 transition-micro" data-id="${activity.ActivityID}">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-text-primary">${escapeHtml(activity.title)}</div>
                        <div class="text-xs text-text-secondary">${escapeHtml((activity.description || '').substring(0, 60))}${activity.description && activity.description.length > 60 ? '...' : ''}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">${escapeHtml(cmi)}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-text-secondary">${escapeHtml(activity.type || '-')}</td>
                    <td class="px-6 py-4 text-sm text-text-secondary">${activityDate}</td>
                    <td class="px-6 py-4 text-sm text-text-secondary">${escapeHtml(activity.location || '-')}</td>
                    <td class="px-6 py-4 text-sm text-text-secondary">${escapeHtml(activity.implementing_agency || '-')}</td>
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
            const activityDate = formatDate(activity.reported_period_start);
            const cmi = activity.implementing_agency || '-';

            return `
                <div class="border-b border-secondary-200 p-4 hover:bg-secondary-50" data-id="${activity.ActivityID}">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-text-primary">${escapeHtml(activity.title)}</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 mt-1">${escapeHtml(cmi)}</span>
                            <p class="text-xs text-text-secondary mt-1">${escapeHtml(activity.type || '')} | ${escapeHtml(activity.location || '')}</p>
                        </div>
                        <span class="text-xs text-text-secondary ml-2">${activityDate}</span>
                    </div>
                    <div class="text-xs text-text-secondary mb-2">${escapeHtml(activity.implementing_agency || '-')}</div>
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

    // Open activity form (add/edit)
    function openActivityForm(activityId = null) {
        const modal = document.getElementById('add-activity-modal');
        const form = document.getElementById('add-activity-form');
        const title = document.getElementById('modal-title');
        const submitBtnText = document.getElementById('submit-btn-text');
        
        form.reset();
        document.getElementById('edit-activity-id').value = '';
        document.getElementById('photo-preview').innerHTML = '';
        document.getElementById('existing-attachments').innerHTML = '';
        document.getElementById('other-type-wrapper').classList.add('hidden');
        selectedPhotos = [];
        
        if (activityId) {
            const activity = allActivities.find(a => a.ActivityID == activityId);
            if (activity) {
                title.textContent = 'Edit Activity';
                submitBtnText.textContent = 'Update Activity';
                document.getElementById('edit-activity-id').value = activityId;
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
                
                // Handle "Others" type
                if (activity.type === 'Others') {
                    document.getElementById('other-type-wrapper').classList.remove('hidden');
                    document.getElementById('other-type').value = activity.otherType || '';
                }
                
                // Show existing attachments
                if (activity.attachments && activity.attachments.length > 0) {
                    const existingDiv = document.getElementById('existing-attachments');
                    existingDiv.innerHTML = '<p class="text-sm text-text-secondary mb-2">Existing attachments:</p><div class="grid grid-cols-3 gap-2">' +
                        activity.attachments.map(att => `
                            <div class="relative group">
                                <img src="${BASE_URL}uploads/activities/${att.filename}" alt="${att.original_name}" class="w-full h-20 object-cover rounded border border-secondary-200" />
                                <p class="text-xs text-text-secondary truncate mt-1">${att.original_name}</p>
                            </div>
                        `).join('') + '</div>';
                }
            }
        } else {
            title.textContent = 'Create New Activity';
            submitBtnText.textContent = 'Create Activity';
        }
        
        modal.classList.remove('hidden');
    }

    function closeActivityForm() {
        document.getElementById('add-activity-modal').classList.add('hidden');
        document.getElementById('add-activity-form').reset();
        document.getElementById('edit-activity-id').value = '';
        document.getElementById('photo-preview').innerHTML = '';
        document.getElementById('existing-attachments').innerHTML = '';
        document.getElementById('other-type-wrapper').classList.add('hidden');
        document.getElementById('modal-title').textContent = 'Create New Activity';
        document.getElementById('submit-btn-text').textContent = 'Create Activity';
        selectedPhotos = [];
    }

    // Photo handling
    let selectedPhotos = [];
    
    function handlePhotoSelection(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('photo-preview');
        
        selectedPhotos = Array.from(files);
        previewContainer.innerHTML = '';
        
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
    
    function removePhoto(index) {
        selectedPhotos.splice(index, 1);
        const dataTransfer = new DataTransfer();
        selectedPhotos.forEach(file => dataTransfer.items.add(file));
        document.getElementById('activity-images').files = dataTransfer.files;
        
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

    // Submit activity form
    async function submitActivity(e) {
        e.preventDefault();
        
        const editId = document.getElementById('edit-activity-id').value;
        const isEditing = editId && editId !== '';
        
        const title = document.getElementById('activity-title').value.trim();
        const description = document.getElementById('activity-description').value.trim();
        const startDate = document.getElementById('activity-start-date').value;
        const endDate = document.getElementById('activity-end-date').value;
        const venue = document.getElementById('activity-venue').value.trim();
        const implementingAgency = document.getElementById('implementing-agency').value;
        const collaboratingAgency = document.getElementById('collaborating-agency').value;
        const participantsCount = document.getElementById('participants-count').value;
        const budgetAmount = document.getElementById('budget-amount').value;
        const type = document.getElementById('activity-type').value;
        const otherType = document.getElementById('other-type').value.trim();

        // Validation
        if (!title || !description || !startDate || !endDate || !venue || !implementingAgency || !type) {
            showToast('Please fill in all required fields', 'error');
            return false;
        }

        // Use FormData for file uploads
        const formData = new FormData();
        formData.append('title', title);
        formData.append('description', description);
        formData.append('startDate', startDate);
        formData.append('endDate', endDate);
        formData.append('venue', venue);
        formData.append('implementingAgency', implementingAgency);
        formData.append('collaboratingAgency', collaboratingAgency);
        formData.append('participantsCount', participantsCount);
        formData.append('budgetAmount', budgetAmount);
        formData.append('type', type);
        formData.append('otherType', otherType);
        
        // Add images
        const imageInput = document.getElementById('activity-images');
        if (imageInput.files.length > 0) {
            Array.from(imageInput.files).forEach(file => {
                formData.append('images[]', file);
            });
        }

        try {
            if (isEditing) {
                formData.append('id', editId);
                formData.append('_method', 'PUT');
            }
            
            const response = await fetch(API_URL, {
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
        const cmiFilter = document.getElementById('cmi-filter').value;
        const typeFilter = document.getElementById('type-filter').value;
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

            // CMI filter (based on implementing agency)
            if (cmiFilter) {
                match = match && activity.implementing_agency === cmiFilter;
            }

            // Type of Event filter
            if (typeFilter) {
                match = match && activity.type === typeFilter;
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
        document.getElementById('cmi-filter').value = '';
        document.getElementById('type-filter').value = '';
        document.getElementById('date-from').value = '';
        document.getElementById('date-to').value = '';
        renderActivities(allActivities);
    }

    // View activity details
    function viewActivity(id) {
        const activity = allActivities.find(a => a.ActivityID == id);
        if (!activity) return;
        
        // For now, just open in edit mode for viewing
        openActivityForm(id);
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

    // Map status codes to labels and badge styles
    function getStatusLabel(status) {
        const labels = {
            'not_started': 'Not Started',
            'in_progress': 'In Progress',
            'completed': 'Completed'
        };
        return labels[status] || 'Not Specified';
    }

    function getStatusClass(status) {
        const classes = {
            'not_started': 'bg-secondary-100 text-secondary-800',
            'in_progress': 'bg-warning-100 text-warning-800',
            'completed': 'bg-success-100 text-success-800'
        };
        return classes[status] || 'bg-secondary-100 text-secondary-800';
    }

    // Update summary counters (safe even if the DOM nodes are missing)
    function updateStatusCounts() {
        const counts = allActivities.reduce((acc, activity) => {
            const key = activity.status || 'not_started';
            acc[key] = (acc[key] || 0) + 1;
            return acc;
        }, {});

        const statusElements = {
            not_started: document.getElementById('not-started-count'),
            in_progress: document.getElementById('in-progress-count'),
            completed: document.getElementById('completed-count'),
            total: document.getElementById('total-count')
        };

        Object.entries(statusElements).forEach(([status, el]) => {
            if (!el) return;
            if (status === 'total') {
                el.textContent = allActivities.length;
            } else {
                el.textContent = counts[status] || 0;
            }
        });
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

    // View activity details modal (without status)
    function viewActivity(activityId) {
        const activity = allActivities.find(a => a.ActivityID == activityId);
        if (!activity) {
            showToast('Activity not found', 'error');
            return;
        }

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
                                    <a href="${BASE_URL}uploads/activities/${att.filename}" target="_blank" class="group block border border-secondary-200 rounded-lg overflow-hidden hover:border-primary transition-colors">
                                        <img src="${BASE_URL}uploads/activities/${att.filename}" alt="${escapeHtml(att.original_name)}" class="w-full h-32 object-cover" />
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
                                <a href="${BASE_URL}uploads/activities/${att.filename}" target="_blank">
                                    <img src="${BASE_URL}uploads/activities/${att.filename}" alt="${att.original_name}" class="w-full h-32 object-cover" />
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
        
        // Toggle "Others" type field
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
    });
</script>

<?php include '../../includes/footer.php'; ?>