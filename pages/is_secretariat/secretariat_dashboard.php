<?php
$pageTitle = "Secretariat Dashboard - Central CMI";
$bodyClass = "bg-background min-h-screen";
require_once __DIR__ . '/../../database/auth.php';
require_role(['secretariat']);
require_once __DIR__ . '/../../database/config.php';

// Compute simple dashboard stats server-side for initial render
$statTotalActivities = 0;
$statTotalUsers = 0;

try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM Activity");
        $statTotalActivities = (int)$stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM User");
        $statTotalUsers = (int)$stmt->fetchColumn();
    }
} catch (Throwable $e) {
    // Fail silently for UI; JS/API can still try later
}

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
                <div class="flex space-x-3 mt-4 lg:mt-0">
                    <button type="button" class="btn-primary" onclick="openEventModal()">
                        <i class="fas fa-plus mr-2"></i>
                        New Schedule
                    </button>
                </div>
            </div>
        </section>

        <!-- Statistics Cards -->
        <section class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Total Activities Card -->
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-text-secondary">Total Activities</p>
                            <p id="stat-total-activities" class="text-3xl font-bold text-text-primary mt-2">
                                <?php echo htmlspecialchars((string)$statTotalActivities, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            
                        </div>
                        <div class="bg-primary-100 p-3 rounded-full">
                            <i class="fas fa-tasks text-primary text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Users Card -->
                <div class="bg-surface rounded-lg shadow-card border border-secondary-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-text-secondary">Total Users</p>
                            <p id="stat-total-users" class="text-3xl font-bold text-text-primary mt-2">
                                <?php echo htmlspecialchars((string)$statTotalUsers, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                        <div class="bg-accent-100 p-3 rounded-full">
                            <i class="fas fa-users text-accent text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Calendar Section -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Calendar -->
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
                                December 2025
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
                    <div>
                        <label for="event-title" class="block text-sm font-medium text-text-primary mb-2">
                            Schedule Title <span class="text-error">*</span>
                        </label>
                        <input type="text" id="event-title" name="title" required class="form-input" placeholder="Enter schedule title" />
                    </div>

                    <div>
                        <label for="event-type" class="block text-sm font-medium text-text-primary mb-2">
                            Schedule Type <span class="text-error">*</span>
                        </label>
                        <select id="event-type" name="type" required class="form-input">
                            <option value="">Select schedule type</option>
                            <option value="event">Event</option>
                            <option value="training">Training</option>
                            <option value="travel">Travel</option>
                            <option value="meeting">Meeting</option>
                        </select>
                    </div>

                    <div>
                        <label for="event-date-start" class="block text-sm font-medium text-text-primary mb-2">
                            Date Start <span class="text-error">*</span>
                        </label>
                        <input type="date" id="event-date-start" name="date_start" required class="form-input" />
                    </div>

                    <div>
                        <label for="event-date-end" class="block text-sm font-medium text-text-primary mb-2">
                            Date End <span class="text-error">*</span>
                        </label>
                        <input type="date" id="event-date-end" name="date_end" required class="form-input" />
                    </div>

                    <div>
                        <label for="event-description" class="block text-sm font-medium text-text-primary mb-2">
                            Description
                        </label>
                        <textarea id="event-description" name="description" rows="3" class="form-input" placeholder="Enter schedule description (optional)"></textarea>
                    </div>

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
                <div id="schedule-details-content" class="space-y-4"></div>
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

    <!-- Conflict Modal -->
    <div id="conflict-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-surface rounded-lg shadow-modal max-w-md w-full mx-4 p-6">
            <div class="text-center">
                <div class="flex justify-center mb-4">
                    <div class="bg-warning-100 p-3 rounded-full">
                        <i class="fas fa-exclamation-triangle text-2xl text-warning"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-text-primary mb-2">Schedule Conflict Detected</h3>
                <p id="conflict-message" class="text-text-secondary mb-6"></p>
                <div class="flex space-x-3">
                    <button type="button" class="flex-1 btn-secondary" onclick="closeConflictModal()">Cancel</button>
                    <button type="button" class="flex-1 btn-primary" onclick="resolveConflict()">Continue Anyway</button>
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
                <div id="conflicts-content" class="space-y-4 max-h-96 overflow-y-auto"></div>
                <div class="flex gap-3 pt-4 border-t border-secondary-200 mt-6">
                    <button type="button" class="flex-1 btn-secondary" onclick="closeConflictsOverviewModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load dashboard stats
        async function loadDashboardStats() {
            try {
                const response = await fetch(BASE_URL + 'api/secretariat-dashboard.php');
                const data = await response.json();
                
                if (data.success) {
                    // Total activities from Activity table
                    const totalActivitiesEl = document.getElementById('stat-total-activities');
                    if (totalActivitiesEl) {
                        totalActivitiesEl.textContent = data.data.stats.totalActivities;
                    }

                    // Total users from User table
                    const totalUsersEl = document.getElementById('stat-total-users');
                    if (totalUsersEl) {
                        totalUsersEl.textContent = data.data.stats.totalUsers;
                    }
                }
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        // Calendar functionality
        let calendarEvents = [];
        let currentView = 'month';
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();

        function loadCalendarEvents() {
            const stored = localStorage.getItem('secretariatCalendarEvents');
            calendarEvents = stored ? JSON.parse(stored) : [];
            renderCalendarEvents();
        }

        function saveCalendarEvents() {
            localStorage.setItem('secretariatCalendarEvents', JSON.stringify(calendarEvents));
        }

        function renderCalendarEvents() {
            document.querySelectorAll('.event-item').forEach(event => event.remove());
            
            calendarEvents.forEach(event => {
                const startDate = new Date(event.date_start);
                const endDate = new Date(event.date_end);
                
                document.querySelectorAll('.calendar-day').forEach(dayElement => {
                    const dayDate = new Date(dayElement.dataset.date);
                    
                    if (dayDate >= startDate && dayDate <= endDate) {
                        const existingEvent = dayElement.querySelector(`[data-event-id="${event.id}"]`);
                        if (!existingEvent) {
                            const eventElement = document.createElement('div');
                            eventElement.className = `event-item mt-1 p-1 text-xs rounded truncate cursor-pointer ${
                                event.type === 'meeting' ? 'bg-primary-100 text-primary-700' :
                                event.type === 'event' ? 'bg-warning-100 text-warning-700' :
                                event.type === 'training' ? 'bg-success-100 text-success-700' :
                                event.type === 'travel' ? 'bg-accent-100 text-accent-700' :
                                'bg-error-100 text-error-700'
                            }`;
                            eventElement.textContent = event.title;
                            eventElement.dataset.eventId = event.id;
                            eventElement.addEventListener('click', (e) => {
                                e.stopPropagation();
                                viewEvent(event.id);
                            });
                            dayElement.appendChild(eventElement);
                        }
                    }
                });
            });
            
            updateUpcomingEventsSidebar();
        }

        function updateUpcomingEventsSidebar() {
            const container = document.getElementById('upcoming-schedules');
            if (!container) return;
            
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
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
                container.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-plus text-4xl text-secondary-300 mb-3"></i>
                        <p class="text-text-secondary text-sm">No upcoming schedules</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = sortedSchedules.map(schedule => {
                const colorClass = schedule.type === 'meeting' ? 'border-primary' :
                                 schedule.type === 'event' ? 'border-warning' :
                                 schedule.type === 'training' ? 'border-success' :
                                 schedule.type === 'travel' ? 'border-accent' : 'border-error';
                
                let dateText = new Date(schedule.date_start).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                if (schedule.date_start !== schedule.date_end) {
                    dateText += ` - ${new Date(schedule.date_end).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
                }
                
                return `
                    <div class="border-l-4 ${colorClass} pl-3 py-2 cursor-pointer hover:bg-secondary-50" onclick="viewEvent('${schedule.id}')">
                        <h4 class="text-sm font-medium text-text-primary">${schedule.title}</h4>
                        <p class="text-xs text-text-secondary mt-1">${dateText} • ${schedule.type.charAt(0).toUpperCase() + schedule.type.slice(1)}</p>
                    </div>
                `;
            }).join('');
        }

        function switchView(view) {
            document.querySelectorAll('[id$="-view"]').forEach(btn => {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('text-secondary-600', 'hover:text-primary', 'hover:bg-secondary-50');
            });
            
            document.getElementById(view + '-view').classList.add('bg-primary', 'text-white');
            document.getElementById(view + '-view').classList.remove('text-secondary-600', 'hover:text-primary', 'hover:bg-secondary-50');
            
            currentView = view;
            renderCalendar();
        }

        function navigateCalendar(direction) {
            if (direction === 'prev') {
                currentMonth--;
                if (currentMonth < 0) { currentMonth = 11; currentYear--; }
            } else {
                currentMonth++;
                if (currentMonth > 11) { currentMonth = 0; currentYear++; }
            }
            updateCalendarHeader();
            renderCalendar();
        }

        function updateCalendarHeader() {
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            document.getElementById('current-date').textContent = `${monthNames[currentMonth]} ${currentYear}`;
        }

        function renderCalendar() {
            const calendarGrid = document.getElementById('calendar-grid');
            if (!calendarGrid) return;
            
            calendarGrid.innerHTML = '';
            
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDayOfWeek = firstDay.getDay();
            
            const prevMonth = currentMonth === 0 ? 11 : currentMonth - 1;
            const prevYear = currentMonth === 0 ? currentYear - 1 : currentYear;
            const prevMonthLastDay = new Date(prevYear, prevMonth + 1, 0).getDate();
            
            const nextMonth = currentMonth === 11 ? 0 : currentMonth + 1;
            const nextYear = currentMonth === 11 ? currentYear + 1 : currentYear;
            
            const today = new Date();
            const isToday = (day, month, year) => day === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            
            for (let i = 0; i < 42; i++) {
                const dayElement = document.createElement('div');
                let dayNumber, isCurrentMonth, dayYear, dayMonth;
                
                if (i < startingDayOfWeek) {
                    dayNumber = prevMonthLastDay - startingDayOfWeek + i + 1;
                    dayYear = prevYear;
                    dayMonth = prevMonth;
                    isCurrentMonth = false;
                } else if (i < startingDayOfWeek + daysInMonth) {
                    dayNumber = i - startingDayOfWeek + 1;
                    dayYear = currentYear;
                    dayMonth = currentMonth;
                    isCurrentMonth = true;
                } else {
                    dayNumber = i - startingDayOfWeek - daysInMonth + 1;
                    dayYear = nextYear;
                    dayMonth = nextMonth;
                    isCurrentMonth = false;
                }
                
                const dayDate = `${dayYear}-${String(dayMonth + 1).padStart(2, '0')}-${String(dayNumber).padStart(2, '0')}`;
                
                dayElement.className = `calendar-day p-2 min-h-[80px] border border-secondary-100 rounded cursor-pointer hover:bg-secondary-50 transition-micro`;
                dayElement.dataset.date = dayDate;
                
                const daySpan = document.createElement('span');
                daySpan.textContent = dayNumber;
                daySpan.className = isCurrentMonth ? 'text-sm text-text-primary' : 'text-sm text-secondary-400';
                
                if (isToday(dayNumber, dayMonth, dayYear)) {
                    dayElement.classList.add('bg-primary-50');
                    daySpan.classList.add('text-primary', 'font-semibold');
                }
                
                dayElement.appendChild(daySpan);
                calendarGrid.appendChild(dayElement);
            }
            
            renderCalendarEvents();
        }

        function goToToday() {
            const today = new Date();
            currentMonth = today.getMonth();
            currentYear = today.getFullYear();
            updateCalendarHeader();
            renderCalendar();
        }

        // Modal functions
        function openEventModal() {
            document.getElementById('event-modal').classList.remove('hidden');
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('event-date-start').value = today;
            document.getElementById('event-date-end').value = today;
        }

        function closeEventModal() {
            document.getElementById('event-modal').classList.add('hidden');
            document.getElementById('event-form').reset();
            document.querySelector('#event-modal h3').textContent = 'New Schedule';
            document.querySelector('#event-form button[type="submit"]').innerHTML = '<i class="fas fa-save mr-2"></i>Create Schedule';
            window.editingScheduleId = null;
        }

        document.getElementById('event-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const isEditing = window.editingScheduleId;
            
            let eventData;
            
            if (isEditing) {
                const existingSchedule = calendarEvents.find(e => e.id === window.editingScheduleId);
                eventData = {
                    ...existingSchedule,
                    title: formData.get('title'),
                    type: formData.get('type'),
                    date: formData.get('date_start'),
                    date_start: formData.get('date_start'),
                    date_end: formData.get('date_end'),
                    description: formData.get('description'),
                    updated_at: new Date().toISOString()
                };
                const index = calendarEvents.findIndex(e => e.id === window.editingScheduleId);
                calendarEvents[index] = eventData;
                window.editingScheduleId = null;
            } else {
                eventData = {
                    id: 'event-' + Date.now(),
                    title: formData.get('title'),
                    type: formData.get('type'),
                    date: formData.get('date_start'),
                    date_start: formData.get('date_start'),
                    date_end: formData.get('date_end'),
                    description: formData.get('description'),
                    status: 'scheduled',
                    created_at: new Date().toISOString()
                };
                
                const conflicts = checkForConflicts(eventData);
                if (conflicts.length > 0) {
                    showConflictDetails(conflicts, eventData);
                    return;
                }
                
                calendarEvents.push(eventData);
            }
            
            saveCalendarEvents();
            renderCalendarEvents();
            closeEventModal();
            showNotification(isEditing ? 'Schedule updated!' : 'Schedule created!', 'success');
        });

        function viewEvent(eventId) {
            const schedule = calendarEvents.find(e => e.id === eventId);
            if (schedule) showScheduleDetails(schedule);
        }

        function showScheduleDetails(schedule) {
            const modal = document.getElementById('schedule-details-modal');
            const content = document.getElementById('schedule-details-content');
            
            const startDate = new Date(schedule.date_start);
            const endDate = new Date(schedule.date_end);
            
            let dateText = startDate.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            if (schedule.date_start !== schedule.date_end) {
                dateText += ` - ${endDate.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}`;
            }
            
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
                    </div>` : ''}
                </div>
            `;
            
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
                document.getElementById('event-modal').classList.remove('hidden');
                document.getElementById('event-title').value = schedule.title;
                document.getElementById('event-type').value = schedule.type;
                document.getElementById('event-date-start').value = schedule.date_start;
                document.getElementById('event-date-end').value = schedule.date_end;
                document.getElementById('event-description').value = schedule.description || '';
                
                window.editingScheduleId = eventId;
                document.querySelector('#event-modal h3').textContent = 'Edit Schedule';
                document.querySelector('#event-form button[type="submit"]').innerHTML = '<i class="fas fa-save mr-2"></i>Update Schedule';
            }
        }

        function checkForConflicts(newEvent) {
            const conflicts = [];
            const newStart = new Date(newEvent.date_start);
            const newEnd = new Date(newEvent.date_end);
            
            calendarEvents.forEach(existingEvent => {
                if (existingEvent.id === newEvent.id) return;
                
                const existingStart = new Date(existingEvent.date_start);
                const existingEnd = new Date(existingEvent.date_end);
                
                if (newStart <= existingEnd && newEnd >= existingStart) {
                    conflicts.push(existingEvent);
                }
            });
            
            return conflicts;
        }

        function showConflictDetails(conflicts, eventData) {
            window.pendingEventData = eventData;
            const conflictModal = document.getElementById('conflict-modal');
            document.getElementById('conflict-message').textContent = conflicts.length === 1
                ? `This schedule conflicts with "${conflicts[0].title}". Would you like to continue?`
                : `This schedule conflicts with ${conflicts.length} existing schedules. Would you like to continue?`;
            conflictModal.classList.remove('hidden');
        }

        function closeConflictModal() {
            document.getElementById('conflict-modal').classList.add('hidden');
        }

        function resolveConflict() {
            closeConflictModal();
            if (window.pendingEventData) {
                calendarEvents.push(window.pendingEventData);
                saveCalendarEvents();
                renderCalendarEvents();
                closeEventModal();
                showNotification('Schedule created with conflict noted!', 'warning');
                window.pendingEventData = null;
            }
        }

        function viewConflicts() {
            const allConflicts = [];
            calendarEvents.forEach(event => {
                const conflicts = checkForConflicts(event);
                if (conflicts.length > 0) {
                    allConflicts.push({ schedule: event, conflicts: conflicts });
                }
            });
            
            if (allConflicts.length === 0) {
                showNotification('No schedule conflicts found!', 'success');
                return;
            }
            
            const modal = document.getElementById('conflicts-overview-modal');
            const content = document.getElementById('conflicts-content');
            
            content.innerHTML = allConflicts.map(conflictGroup => {
                return `
                    <div class="bg-warning-50 border border-warning-200 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-text-primary mb-2">${conflictGroup.schedule.title}</h4>
                        <p class="text-xs text-text-secondary mb-2">Conflicts with: ${conflictGroup.conflicts.map(c => c.title).join(', ')}</p>
                    </div>
                `;
            }).join('');
            
            modal.classList.remove('hidden');
        }

        function closeConflictsOverviewModal() {
            document.getElementById('conflicts-overview-modal').classList.add('hidden');
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const colors = {
                success: 'bg-success-100 text-success-700 border-success-200',
                error: 'bg-error-100 text-error-700 border-error-200',
                warning: 'bg-warning-100 text-warning-700 border-warning-200',
                info: 'bg-primary-100 text-primary-700 border-primary-200'
            };
            
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg border ${colors[type]}`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            updateCalendarHeader();
            renderCalendar();
            loadCalendarEvents();
        });
    </script>

<?php include '../../includes/footer.php'; ?>
