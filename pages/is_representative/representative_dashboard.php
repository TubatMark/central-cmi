<?php
$pageTitle = "Representative Home - Calendar";
$bodyClass = "bg-background";
require_once __DIR__ . '/../../database/auth.php';
require_role(['representative', 'secretariat']);
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-text-primary">Calendar</h1>
                <p class="text-text-secondary mt-2">Manage schedules, deadlines, and activity timelines</p>
            </div>
            <div class="flex space-x-3 mt-4 sm:mt-0">
                <button type="button" class="btn-primary" onclick="openEventModal()">
                    <i class="fas fa-plus mr-2"></i>
                    New Schedule
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Calendar Section -->
            <div class="lg:col-span-3">
                <!-- Calendar Controls -->
                <div class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- View Toggle -->
                        <div class="flex bg-secondary-100 rounded-lg p-1 w-full sm:w-auto order-3 sm:order-1">
                            <button id="month-view" type="button" class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-md bg-primary text-white transition-micro" onclick="switchView('month')">
                                Month
                            </button>
                            <button id="week-view" type="button" class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-md text-secondary-600 hover:text-primary hover:bg-secondary-50 transition-micro" onclick="switchView('week')">
                                Week
                            </button>
                            <button id="day-view" type="button" class="flex-1 sm:flex-none px-4 py-2 text-sm font-medium rounded-md text-secondary-600 hover:text-primary hover:bg-secondary-50 transition-micro" onclick="switchView('day')">
                                Day
                            </button>
                        </div>

                        <!-- Date Navigation -->
                        <div class="flex items-center justify-center sm:justify-start space-x-4 order-1 sm:order-2">
                            <button type="button" class="p-2 text-secondary-600 hover:text-primary transition-micro" onclick="navigateCalendar('prev')">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h2 id="current-date" class="text-lg font-semibold text-text-primary min-w-[140px] text-center">
                                September 2025
                            </h2>
                            <button type="button" class="p-2 text-secondary-600 hover:text-primary transition-micro" onclick="navigateCalendar('next')">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Today Button -->
                        <button type="button" class="btn-secondary order-2 sm:order-3 w-full sm:w-auto" onclick="goToToday()">
                            Today
                        </button>
                    </div>
                </div>

                <!-- Calendar Widget -->
                <div class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6">
                    <div class="grid grid-cols-7 gap-1 mb-4">
                        <!-- Day Headers -->
                        <div class="p-3 text-center text-sm font-medium text-text-secondary">Sun</div>
                        <div class="p-3 text-center text-sm font-medium text-text-secondary">Mon</div>
                        <div class="p-3 text-center text-sm font-medium text-text-secondary">Tue</div>
                        <div class="p-3 text-center text-sm font-medium text-text-secondary">Wed</div>
                        <div class="p-3 text-center text-sm font-medium text-text-secondary">Thu</div>
                        <div class="p-3 text-center text-sm font-medium text-text-secondary">Fri</div>
                        <div class="p-3 text-center text-sm font-medium text-text-secondary">Sat</div>
                    </div>

                    <div id="calendar-grid" class="grid grid-cols-7 gap-1">
                        <!-- Calendar days will be dynamically generated here -->
                        </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">
                <!-- Upcoming Schedules -->
                <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">Upcoming Schedules</h3>
                    <div class="space-y-4" id="upcoming-schedules">
                        <!-- Schedules will be dynamically loaded here -->
                    </div>
                </section>

                <!-- Schedule Legend -->
                <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">Schedule Types</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-primary-100 border-l-4 border-primary rounded-sm mr-3"></div>
                            <span class="text-sm text-text-secondary">Meetings</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-warning-100 border-l-4 border-warning rounded-sm mr-3"></div>
                            <span class="text-sm text-text-secondary">Events</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-success-100 border-l-4 border-success rounded-sm mr-3"></div>
                            <span class="text-sm text-text-secondary">Training</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-accent-100 border-l-4 border-accent rounded-sm mr-3"></div>
                            <span class="text-sm text-text-secondary">Travel</span>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6">
                    <h3 class="text-lg font-semibold text-text-primary mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button type="button" class="w-full btn-primary text-left" onclick="openEventModal()">
                            <i class="fas fa-plus mr-2"></i>
                            Create Schedule
                        </button>
                        <button type="button" class="w-full btn-secondary text-left" onclick="syncActivities()">
                            <i class="fas fa-sync mr-2"></i>
                            Sync Activities
                        </button>
                        <button type="button" class="w-full btn-secondary text-left" onclick="viewConflicts()">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Check Conflicts
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Schedule Modal -->
    <div id="event-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-surface rounded-lg shadow-modal max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-text-primary">New Schedule</h3>
                    <button type="button" class="text-secondary-400 hover:text-secondary-600" onclick="closeEventModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="event-form" class="space-y-4">
                    <!-- Schedule Title -->
                    <div>
                        <label for="event-title" class="block text-sm font-medium text-text-primary mb-2">
                            Schedule Title <span class="text-error">*</span>
                        </label>
                        <input type="text" id="event-title" name="title" required class="form-input" placeholder="Enter schedule title" />
                    </div>

                    <!-- Schedule Type -->
                    <div>
                        <label for="event-type" class="block text-sm font-medium text-text-primary mb-2">
                            Schedule Type <span class="text-error">*</span>
                        </label>
                        <select id="event-type" name="type" required class="form-input">
                            <option value>Select schedule type</option>
                            <option value="event">Event</option>
                            <option value="training">Training</option>
                            <option value="travel">Travel</option>
                            <option value="meeting">Meeting</option>
                        </select>
                    </div>

                    <!-- Date Start -->
                        <div>
                        <label for="event-date-start" class="block text-sm font-medium text-text-primary mb-2">
                            Date Start <span class="text-error">*</span>
                            </label>
                        <input type="date" id="event-date-start" name="date_start" required class="form-input" />
                        </div>

                    <!-- Date End -->
                        <div>
                        <label for="event-date-end" class="block text-sm font-medium text-text-primary mb-2">
                            Date End <span class="text-error">*</span>
                            </label>
                        <input type="date" id="event-date-end" name="date_end" required class="form-input" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="event-description" class="block text-sm font-medium text-text-primary mb-2">
                            Description
                        </label>
                        <textarea id="event-description" name="description" rows="3" class="form-input" placeholder="Enter schedule description (optional)"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex gap-3 pt-4 border-t border-secondary-200">
                        <button type="submit" class="flex-1 btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Create Schedule
                        </button>
                        <button type="button" class="flex-1 btn-secondary" onclick="closeEventModal()">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
                        </div>
                    </div>

    <!-- Schedule Details Modal -->
    <div id="schedule-details-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-surface rounded-lg shadow-modal max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-text-primary">Schedule Details</h3>
                    <button type="button" class="text-secondary-400 hover:text-secondary-600" onclick="closeScheduleDetailsModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    </div>

                <div id="schedule-details-content" class="space-y-4">
                    <!-- Schedule details will be populated here -->
                </div>
                
                <div class="flex gap-3 pt-4 border-t border-secondary-200 mt-6">
                    <button type="button" class="flex-1 btn-primary" onclick="editScheduleFromDetails()">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Schedule
                        </button>
                    <button type="button" class="flex-1 btn-secondary" onclick="closeScheduleDetailsModal()">
                        Close
                        </button>
                    </div>
            </div>
        </div>
    </div>

    <!-- Conflict Detection Modal -->
    <div id="conflict-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-surface rounded-lg shadow-modal max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="bg-warning-100 p-3 rounded-full">
                        <i class="fas fa-exclamation-triangle text-2xl text-warning"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Schedule Conflict Detected</h3>
                <p id="conflict-message" class="text-text-secondary mb-6">
                    This schedule conflicts with an existing schedule at the same time. Would you like to continue?
                </p>
                <div class="flex space-x-3">
                    <button type="button" class="flex-1 btn-secondary" onclick="closeConflictModal()">
                        Cancel
                    </button>
                    <button type="button" class="flex-1 btn-primary" onclick="resolveConflict()">
                        Continue Anyway
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Conflicts Overview Modal -->
    <div id="conflicts-overview-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-surface rounded-lg shadow-modal max-w-2xl w-full mx-4">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="bg-warning-100 p-3 rounded-full mr-4">
                            <i class="fas fa-exclamation-triangle text-warning text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-text-primary">Schedule Conflicts</h3>
                            <p class="text-sm text-text-secondary">Review and manage conflicting schedules</p>
                        </div>
                    </div>
                    <button type="button" class="text-secondary-400 hover:text-secondary-600" onclick="closeConflictsOverviewModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="conflicts-content" class="space-y-4 max-h-96 overflow-y-auto">
                    <!-- Conflicts will be populated here -->
                </div>
                
                <div class="flex gap-3 pt-4 border-t border-secondary-200 mt-6">
                    <button type="button" class="flex-1 btn-secondary" onclick="closeConflictsOverviewModal()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let calendarEvents = [];
        
        // Calendar event functions
        function loadCalendarEvents() {
            const stored = localStorage.getItem('calendarEvents');
            calendarEvents = stored ? JSON.parse(stored) : [];
            renderCalendarEvents();
        }
        
        function saveCalendarEvents() {
            localStorage.setItem('calendarEvents', JSON.stringify(calendarEvents));
        }
        
        function renderCalendarEvents() {
            // Clear existing events from calendar
            document.querySelectorAll('.event-item').forEach(event => event.remove());
            
            // Add events to calendar
            calendarEvents.forEach(event => {
                const startDate = new Date(event.date_start);
                const endDate = new Date(event.date_end);
                
                // Get all calendar day elements
                const allDays = document.querySelectorAll('.calendar-day');
                
                allDays.forEach(dayElement => {
                    const dayDate = new Date(dayElement.dataset.date);
                    
                    // Check if this day falls within the schedule's date range
                    // For same-day events, only add once
                    if (dayDate >= startDate && dayDate <= endDate) {
                        // Check if this event is already added to this day
                        const existingEvent = dayElement.querySelector(`[data-event-id="${event.id}"]`);
                        if (!existingEvent) {
                            const eventElement = document.createElement('div');
                            eventElement.className = `event-item mt-1 p-1 text-xs rounded truncate draggable cursor-pointer ${
                                event.type === 'meeting' ? 'bg-primary-100 text-primary-700' :
                                event.type === 'event' ? 'bg-warning-100 text-warning-700' :
                                event.type === 'training' ? 'bg-success-100 text-success-700' :
                                event.type === 'travel' ? 'bg-accent-100 text-accent-700' :
                                'bg-error-100 text-error-700'
                            }`;
                            eventElement.textContent = event.title;
                            eventElement.draggable = true;
                            eventElement.dataset.eventId = event.id;
                            
                            // Add click handler
                            eventElement.addEventListener('click', (e) => {
                                e.stopPropagation();
                                viewEvent(event.id);
                            });
                            
                            dayElement.appendChild(eventElement);
                        }
                    }
                });
            });
            
            // Update upcoming events sidebar
            updateUpcomingEventsSidebar();
            
            // Make events draggable
            makeEventsDraggable();
        }
        
        function updateUpcomingEventsSidebar() {
            const upcomingSchedulesContainer = document.getElementById('upcoming-schedules');
            if (!upcomingSchedulesContainer) return;
            
            // Get next 5 schedules
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Set to start of day for comparison
            
            // Remove duplicates based on schedule ID
            const uniqueSchedules = calendarEvents.filter((event, index, self) => 
                index === self.findIndex(e => e.id === event.id)
            );
            
            const sortedSchedules = uniqueSchedules
                .filter(event => {
                    const eventStart = new Date(event.date_start);
                    eventStart.setHours(0, 0, 0, 0);
                    return eventStart >= today;
                })
                .sort((a, b) => new Date(a.date_start) - new Date(b.date_start))
                .slice(0, 5);
            
            if (sortedSchedules.length === 0) {
                upcomingSchedulesContainer.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-plus text-4xl text-secondary-300 mb-3"></i>
                        <p class="text-text-secondary text-sm">No upcoming schedules</p>
                        <p class="text-text-secondary text-xs mt-1">Create your first schedule to get started</p>
                    </div>
                `;
                return;
            }
            
            upcomingSchedulesContainer.innerHTML = sortedSchedules.map(schedule => {
                const scheduleDate = new Date(schedule.date);
                const colorClass = schedule.type === 'meeting' ? 'border-primary' :
                                 schedule.type === 'event' ? 'border-warning' :
                                 schedule.type === 'training' ? 'border-success' :
                                 schedule.type === 'travel' ? 'border-accent' : 'border-error';
                
                const statusClass = schedule.status === 'confirmed' ? 'status-success' :
                                  schedule.status === 'due-soon' ? 'status-warning' :
                                  'bg-accent-100 text-accent-700';
                
                const statusText = schedule.status === 'confirmed' ? 'Confirmed' :
                                 schedule.status === 'due-soon' ? 'Due Soon' :
                                 'Scheduled';
                
                // Format date range if different start/end dates
                let dateText = scheduleDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                if (schedule.date_start && schedule.date_end && schedule.date_start !== schedule.date_end) {
                    const endDate = new Date(schedule.date_end);
                    dateText += ` - ${endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
                }
                
                return `
                    <div class="border-l-4 ${colorClass} pl-3 py-2">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-text-primary">${schedule.title}</h4>
                                <p class="text-xs text-text-secondary mt-1">${dateText} • ${schedule.type.charAt(0).toUpperCase() + schedule.type.slice(1)}</p>
                                ${schedule.description ? `<p class="text-xs text-text-secondary mt-1 line-clamp-2">${schedule.description}</p>` : ''}
                                <div class="flex items-center mt-2">
                                    <span class="status-badge ${statusClass}">
                                        <i class="fas fa-${schedule.type === 'meeting' ? 'users' : schedule.type === 'event' ? 'calendar' : schedule.type === 'training' ? 'graduation-cap' : schedule.type === 'travel' ? 'plane' : 'clock'} text-xs mr-1"></i>
                                        ${statusText}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Calendar view switching
        let currentView = 'month';
        
        function switchView(view) {
            // Update active view button
            document.querySelectorAll('[id$="-view"]').forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('text-secondary-600', 'hover:text-primary', 'hover:bg-secondary-50');
            });
            
            document.getElementById(view + '-view').classList.add('bg-primary', 'text-white');
            document.getElementById(view + '-view').classList.remove('text-secondary-600', 'hover:text-primary', 'hover:bg-secondary-50');
            
            // Update current view
            currentView = view;
            
            // Re-render calendar based on view
            renderCalendar();
            
            console.log('Switched to', view, 'view');
        }

        // Calendar navigation
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        
        function navigateCalendar(direction) {
            if (direction === 'prev') {
                currentMonth--;
                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }
            } else if (direction === 'next') {
                currentMonth++;
                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }
            }
            
            updateCalendarHeader();
            renderCalendar();
        }
        
        function updateCalendarHeader() {
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
            document.getElementById('current-date').textContent = `${monthNames[currentMonth]} ${currentYear}`;
        }
        
        function renderCalendar() {
            // Get calendar grid container
            const calendarGrid = document.getElementById('calendar-grid');
            if (!calendarGrid) return;
            
            // Clear existing calendar
            calendarGrid.innerHTML = '';
            
            // Get first day of month and number of days
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();
            
            // Get previous month info
            const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
            const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
            const prevMonthLastDay = new Date(prevYear, prevMonth + 1, 0).getDate();
            
            // Get next month info
            const nextMonth = currentMonth === 11 ? 0 : currentMonth + 1;
            const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;
            
            // Get today's date for highlighting
            const today = new Date();
            const isToday = (day, month, year) => {
                return day === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            };
            
            // Generate 42 days (6 weeks) to fill the calendar grid
            for (let i = 0; i < 42; i++) {
                const dayElement = document.createElement('div');
                let dayNumber, dayDate, isCurrentMonth, dayYear, dayMonth;
                
                if (i < startingDayOfWeek) {
                    // Previous month days
                    dayNumber = prevMonthLastDay - startingDayOfWeek + i + 1;
                    dayYear = prevYear;
                    dayMonth = prevMonth;
                    isCurrentMonth = false;
                } else if (i < startingDayOfWeek + daysInMonth) {
                    // Current month days
                    dayNumber = i - startingDayOfWeek + 1;
                    dayYear = currentYear;
                    dayMonth = currentMonth;
                    isCurrentMonth = true;
                } else {
                    // Next month days
                    dayNumber = i - startingDayOfWeek - daysInMonth + 1;
                    dayYear = nextYear;
                    dayMonth = nextMonth;
                    isCurrentMonth = false;
                }
                
                // Create date string
                dayDate = `${dayYear}-${String(dayMonth + 1).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;
                
                // Set up day element
                dayElement.className = `calendar-day p-2 min-h-[80px] border border-secondary-100 rounded cursor-pointer hover:bg-secondary-50 transition-micro`;
                if (isCurrentMonth) {
                    dayElement.classList.add('current-month');
                }
                dayElement.dataset.date = dayDate;
                
                // Create day number span
                const daySpan = document.createElement('span');
                daySpan.textContent = dayNumber;
                daySpan.className = isCurrentMonth ? 'text-sm text-text-primary' : 'text-sm text-secondary-400';
                
                // Highlight today
                if (isToday(dayNumber, dayMonth, dayYear)) {
                    dayElement.classList.add('today', 'bg-primary-50');
                    daySpan.classList.add('text-primary', 'font-semibold');
                }
                
                dayElement.appendChild(daySpan);
                calendarGrid.appendChild(dayElement);
            }
            
            // Add event listeners to new calendar days
            addCalendarDayListeners();
            
            // Re-render events
            renderCalendarEvents();
        }
        
        function addCalendarDayListeners() {
            document.querySelectorAll('.calendar-day').forEach(day => {
                // Remove existing listeners to prevent duplicates
                day.replaceWith(day.cloneNode(true));
            });
            
            // Re-add listeners to all calendar days
            document.querySelectorAll('.calendar-day').forEach(day => {
                day.addEventListener('click', function() {
                    const date = this.dataset.date;
                    console.log('Clicked date:', date);
                    
                    // Remove previous selection
                    document.querySelectorAll('.calendar-day').forEach(d => {
                        d.classList.remove('ring-2', 'ring-primary');
                    });
                    
                    // Add selection to clicked day
                    this.classList.add('ring-2', 'ring-primary');
                });
                
                // Enable drag and drop for events
                day.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('bg-primary-50');
                });
                
                day.addEventListener('dragleave', function(e) {
                    this.classList.remove('bg-primary-50');
                });
                
                day.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('bg-primary-50');
                    console.log('Event dropped on:', this.dataset.date);
                });
            });
        }

        // Go to today
        function goToToday() {
            const today = new Date();
            currentMonth = today.getMonth();
            currentYear = today.getFullYear();
            updateCalendarHeader();
            renderCalendar();
        }

        // Event modal functions
        function openEventModal() {
            document.getElementById('event-modal').classList.remove('hidden');
            // Set default dates to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('event-date-start').value = today;
            document.getElementById('event-date-end').value = today;
        }

        function closeEventModal() {
            document.getElementById('event-modal').classList.add('hidden');
            document.getElementById('event-form').reset();
            
            // Reset modal to create mode
            document.querySelector('#event-modal h3').textContent = 'New Schedule';
            document.querySelector('#event-form button[type="submit"]').innerHTML = '<i class="fas fa-save mr-2"></i>Create Schedule';
            window.editingScheduleId = null;
        }

        // Event form submission
        document.getElementById('event-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const isEditing = window.editingScheduleId;
            
            let eventData;
            
            if (isEditing) {
                // Update existing schedule
                const existingSchedule = calendarEvents.find(e => e.id === window.editingScheduleId);
                eventData = {
                    ...existingSchedule,
                    title: formData.get('title'),
                    type: formData.get('type'),
                    date: formData.get('date_start'), // Use start date for calendar display
                    date_start: formData.get('date_start'),
                    date_end: formData.get('date_end'),
                    description: formData.get('description'),
                    updated_at: new Date().toISOString()
                };
                
                // Update the schedule in the array
                const index = calendarEvents.findIndex(e => e.id === window.editingScheduleId);
                calendarEvents[index] = eventData;
                
                // Clear editing state
                window.editingScheduleId = null;
            } else {
                // Create new schedule
                eventData = {
                    id: 'event-' + Date.now(),
                    title: formData.get('title'),
                    type: formData.get('type'),
                    date: formData.get('date_start'), // Use start date for calendar display
                    date_start: formData.get('date_start'),
                    date_end: formData.get('date_end'),
                    description: formData.get('description'),
                    status: 'scheduled',
                    created_at: new Date().toISOString()
                };
                
                // Add new schedule to array
                calendarEvents.push(eventData);
            }
            
            // Check for conflicts
            const conflicts = checkForConflicts(eventData);
            if (conflicts.length > 0) {
                showConflictDetails(conflicts, eventData);
                return;
            }
            
            // Save to localStorage
            saveCalendarEvents();
            renderCalendarEvents();
            
            // Close modal and show success
            closeEventModal();
            showNotification(isEditing ? 'Schedule updated successfully!' : 'Schedule created successfully!', 'success');
        });

        // Conflict modal functions
        function closeConflictModal() {
            document.getElementById('conflict-modal').classList.add('hidden');
        }

        function resolveConflict() {
            closeConflictModal();
            
            // Get the event data that was stored for conflict resolution
            const eventData = window.pendingEventData;
            if (eventData) {
                // Save the event despite conflicts
                calendarEvents.push(eventData);
                saveCalendarEvents();
                renderCalendarEvents();
            closeEventModal();
                showNotification('Schedule created with conflict noted!', 'warning');
                window.pendingEventData = null;
            }
        }
        
        function checkForConflicts(newEvent) {
            const conflicts = [];
            const newStart = new Date(newEvent.date_start);
            const newEnd = new Date(newEvent.date_end);
            
            // Remove duplicates from calendarEvents first
            const uniqueEvents = calendarEvents.filter((event, index, self) => 
                index === self.findIndex(e => e.id === event.id)
            );
            
            uniqueEvents.forEach(existingEvent => {
                // Skip if it's the same event (for editing)
                if (existingEvent.id === newEvent.id) return;
                
                const existingStart = new Date(existingEvent.date_start);
                const existingEnd = new Date(existingEvent.date_end);
                
                // Check for date overlap
                if ((newStart <= existingEnd && newEnd >= existingStart)) {
                    // Check if this conflict is already added
                    const conflictExists = conflicts.some(conflict => conflict.id === existingEvent.id);
                    if (!conflictExists) {
                        conflicts.push({
                            id: existingEvent.id,
                            title: existingEvent.title,
                            type: existingEvent.type,
                            date_start: existingEvent.date_start,
                            date_end: existingEvent.date_end
                        });
                    }
                }
            });
            
            return conflicts;
        }
        
        function showConflictDetails(conflicts, eventData) {
            const conflictModal = document.getElementById('conflict-modal');
            const conflictContent = document.getElementById('conflict-message');
            
            // Store the event data for potential resolution
            window.pendingEventData = eventData;
            
            if (conflicts.length === 1) {
                conflictContent.textContent = `This schedule conflicts with "${conflicts[0].title}" scheduled from ${new Date(conflicts[0].date_start).toLocaleDateString()} to ${new Date(conflicts[0].date_end).toLocaleDateString()}. Would you like to continue?`;
            } else {
                const conflictTitles = conflicts.map(c => c.title).join(', ');
                conflictContent.textContent = `This schedule conflicts with ${conflicts.length} existing schedules: ${conflictTitles}. Would you like to continue?`;
            }
            
            conflictModal.classList.remove('hidden');
        }

        // Event actions
        function viewEvent(eventId) {
            const schedule = calendarEvents.find(e => e.id === eventId);
            if (schedule) {
                showScheduleDetails(schedule);
            }
        }
        
        function showScheduleDetails(schedule) {
            const modal = document.getElementById('schedule-details-modal');
            const content = document.getElementById('schedule-details-content');
            
            // Format dates
            const startDate = new Date(schedule.date_start);
            const endDate = new Date(schedule.date_end);
            const createdDate = new Date(schedule.created_at);
            
            // Format date range
            let dateText = startDate.toLocaleDateString('en-US', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            if (schedule.date_start !== schedule.date_end) {
                dateText += ` - ${endDate.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                })}`;
            }
            
            // Get type icon and color
            const typeConfig = {
                'meeting': { icon: 'users', color: 'primary', label: 'Meeting' },
                'event': { icon: 'calendar', color: 'warning', label: 'Event' },
                'training': { icon: 'graduation-cap', color: 'success', label: 'Training' },
                'travel': { icon: 'plane', color: 'accent', label: 'Travel' }
            };
            
            const typeInfo = typeConfig[schedule.type] || { icon: 'clock', color: 'error', label: 'Other' };
            
            content.innerHTML = `
                <div class="flex items-center mb-4">
                    <div class="bg-${typeInfo.color}-100 p-3 rounded-full mr-4">
                        <i class="fas fa-${typeInfo.icon} text-${typeInfo.color} text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-text-primary">${schedule.title}</h4>
                        <p class="text-text-secondary">${typeInfo.label}</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <i class="fas fa-calendar-alt text-secondary-400 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-text-primary">Date Range</p>
                            <p class="text-sm text-text-secondary">${dateText}</p>
                        </div>
                    </div>
                    
                    ${schedule.description ? `
                    <div class="flex items-start">
                        <i class="fas fa-align-left text-secondary-400 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-text-primary">Description</p>
                            <p class="text-sm text-text-secondary">${schedule.description}</p>
                        </div>
                    </div>
                    ` : ''}
                    
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-secondary-400 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-text-primary">Status</p>
                            <span class="status-badge ${schedule.status === 'confirmed' ? 'status-success' : schedule.status === 'due-soon' ? 'status-warning' : 'bg-accent-100 text-accent-700'}">
                                <i class="fas fa-${schedule.status === 'confirmed' ? 'check-circle' : schedule.status === 'due-soon' ? 'exclamation-triangle' : 'clock'} text-xs mr-1"></i>
                                ${schedule.status === 'confirmed' ? 'Confirmed' : schedule.status === 'due-soon' ? 'Due Soon' : 'Scheduled'}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <i class="fas fa-clock text-secondary-400 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm font-medium text-text-primary">Created</p>
                            <p class="text-sm text-text-secondary">${createdDate.toLocaleDateString('en-US', { 
                                year: 'numeric', 
                                month: 'short', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</p>
                        </div>
                    </div>
                </div>
            `;
            
            // Store current schedule ID for editing
            window.currentScheduleId = schedule.id;
            
            modal.classList.remove('hidden');
        }
        
        function closeScheduleDetailsModal() {
            document.getElementById('schedule-details-modal').classList.add('hidden');
            window.currentScheduleId = null;
        }
        
        function editScheduleFromDetails() {
            if (window.currentScheduleId) {
                closeScheduleDetailsModal();
                editEvent(window.currentScheduleId);
            }
        }

        function editEvent(eventId) {
            const schedule = calendarEvents.find(e => e.id === eventId);
            if (schedule) {
                // Open modal
                document.getElementById('event-modal').classList.remove('hidden');
                
                // Populate form with existing data
                document.getElementById('event-title').value = schedule.title;
                document.getElementById('event-type').value = schedule.type;
                document.getElementById('event-date-start').value = schedule.date_start;
                document.getElementById('event-date-end').value = schedule.date_end;
                document.getElementById('event-description').value = schedule.description || '';
                
                // Store the schedule ID for updating
                window.editingScheduleId = eventId;
                
                // Update modal title and button
                document.querySelector('#event-modal h3').textContent = 'Edit Schedule';
                document.querySelector('#event-form button[type="submit"]').innerHTML = '<i class="fas fa-save mr-2"></i>Update Schedule';
            }
        }

        function deleteEvent(eventId) {
            if (confirm('Are you sure you want to delete this schedule?')) {
                // Remove from calendar events
                calendarEvents = calendarEvents.filter(e => e.id !== eventId);
                saveCalendarEvents();
                renderCalendarEvents();
                
                showNotification('Schedule deleted successfully!', 'success');
            }
        }

        // Quick actions
        function viewAllEvents() {
            console.log('Viewing all events');
        }

        function syncActivities() {
            console.log('Syncing activities');
            showNotification('Activities synced successfully!', 'success');
        }

        function viewConflicts() {
            const allConflicts = [];
            
            // Remove duplicates based on schedule ID first
            const uniqueSchedules = calendarEvents.filter((event, index, self) => 
                index === self.findIndex(e => e.id === event.id)
            );
            
            // Check all unique schedules for conflicts
            uniqueSchedules.forEach((event, index) => {
                const conflicts = checkForConflicts(event);
                if (conflicts.length > 0) {
                    allConflicts.push({
                        schedule: event,
                        conflicts: conflicts
                    });
                }
            });
            
            if (allConflicts.length === 0) {
                showNotification('No schedule conflicts found!', 'success');
                return;
            }
            
            // Show conflicts in the modal
            showConflictsOverview(allConflicts);
        }
        
        function showConflictsOverview(allConflicts) {
            const modal = document.getElementById('conflicts-overview-modal');
            const content = document.getElementById('conflicts-content');
            
            content.innerHTML = allConflicts.map((conflictGroup, index) => {
                const schedule = conflictGroup.schedule;
                const scheduleStart = new Date(schedule.date_start);
                const scheduleEnd = new Date(schedule.date_end);
                
                // Get schedule type info
                const typeConfig = {
                    'meeting': { icon: 'users', color: 'primary', label: 'Meeting' },
                    'event': { icon: 'calendar', color: 'warning', label: 'Event' },
                    'training': { icon: 'graduation-cap', color: 'success', label: 'Training' },
                    'travel': { icon: 'plane', color: 'accent', label: 'Travel' }
                };
                
                const typeInfo = typeConfig[schedule.type] || { icon: 'clock', color: 'error', label: 'Other' };
                
                // Format date range
                let dateText = scheduleStart.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: 'numeric', 
                    year: 'numeric' 
                });
                
                if (schedule.date_start !== schedule.date_end) {
                    dateText += ` - ${scheduleEnd.toLocaleDateString('en-US', { 
                        month: 'short', 
                        day: 'numeric', 
                        year: 'numeric' 
                    })}`;
                }
                
                return `
                    <div class="bg-warning-50 border border-warning-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <div class="bg-${typeInfo.color}-100 p-2 rounded-full mr-3 mt-1">
                                <i class="fas fa-${typeInfo.icon} text-${typeInfo.color} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-sm font-semibold text-text-primary">${schedule.title}</h4>
                                    <span class="text-xs text-warning-600 bg-warning-100 px-2 py-1 rounded-full">
                                        ${conflictGroup.conflicts.length} conflict${conflictGroup.conflicts.length > 1 ? 's' : ''}
                                    </span>
                                </div>
                                <p class="text-xs text-text-secondary mb-3">${typeInfo.label} • ${dateText}</p>
                                
                                <div class="space-y-2">
                                    <p class="text-xs font-medium text-text-primary">Conflicts with:</p>
                                    ${conflictGroup.conflicts.map(conflict => {
                                        const conflictStart = new Date(conflict.date_start);
                                        const conflictEnd = new Date(conflict.date_end);
                                        
                                        let conflictDateText = conflictStart.toLocaleDateString('en-US', { 
                                            month: 'short', 
                                            day: 'numeric', 
                                            year: 'numeric' 
                                        });
                                        
                                        if (conflict.date_start !== conflict.date_end) {
                                            conflictDateText += ` - ${conflictEnd.toLocaleDateString('en-US', { 
                                                month: 'short', 
                                                day: 'numeric', 
                                                year: 'numeric' 
                                            })}`;
                                        }
                                        
                                        return `
                                            <div class="flex items-center bg-white border border-secondary-200 rounded p-2">
                                                <i class="fas fa-exclamation-circle text-warning text-xs mr-2"></i>
                                                <div class="flex-1">
                                                    <p class="text-xs font-medium text-text-primary">${conflict.title}</p>
                                                    <p class="text-xs text-text-secondary">${conflictDateText}</p>
                                                </div>
                                                <button type="button" class="text-secondary-400 hover:text-primary text-xs" onclick="viewScheduleDetails('${conflict.id}')">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        `;
                                    }).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            modal.classList.remove('hidden');
        }
        
        function closeConflictsOverviewModal() {
            document.getElementById('conflicts-overview-modal').classList.add('hidden');
        }
        
        function viewScheduleDetails(scheduleId) {
            const schedule = calendarEvents.find(e => e.id === scheduleId);
            if (schedule) {
                closeConflictsOverviewModal();
                showScheduleDetails(schedule);
            }
        }
            
            // Make events draggable
        function makeEventsDraggable() {
            document.querySelectorAll('.event-item').forEach(event => {
                event.draggable = true;
                event.addEventListener('dragstart', function(e) {
                    e.dataTransfer.setData('text/plain', this.textContent);
                    this.classList.add('opacity-50');
                });
                
                event.addEventListener('dragend', function(e) {
                    this.classList.remove('opacity-50');
                });
            });
        }

        // Notification system
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
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // Initialize calendar on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize calendar
            updateCalendarHeader();
            renderCalendar();
            
            // Load calendar events
            loadCalendarEvents();
        });
    </script>

<?php include '../../includes/footer.php'; ?>