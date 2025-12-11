<?php
// Start session for user management
session_start();

// Load configuration
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/database/auth.php';

// Use dynamic base URL
$base_url = defined('BASE_URL') ? BASE_URL : '/';
// Representatives and Secretariat can access the landing page
require_role(['representative', 'secretariat']);
$pageTitle = 'Dashboard';
$bodyClass = 'bg-background min-h-screen';

// Mock user data (in real implementation, this would come from database)
if (!isset($_SESSION['user_id'])) {
    // For demo purposes, set a mock user session
    $_SESSION['user_id'] = 1;
    $_SESSION['user_name'] = 'John Doe';
    $_SESSION['user_email'] = 'john.doe@gov.ph';
    $_SESSION['user_role'] = 'representative';
    $_SESSION['is_representative'] = 1;
    $_SESSION['is_secretariat'] = 0;
}

// Mock data for dashboard
$dashboard_stats = [
    'total_activities' => 24,
    'completed_activities' => 18,
    'pending_activities' => 6,
    'overdue_activities' => 2
];

$recent_activities = [
    [
        'id' => 1,
        'title' => 'Policy Review Meeting',
        'status' => 'completed',
        'deadline' => '2024-01-15',
        'progress' => 100
    ],
    [
        'id' => 2,
        'title' => 'Training Program Development',
        'status' => 'in-progress',
        'deadline' => '2024-01-25',
        'progress' => 75
    ],
    [
        'id' => 3,
        'title' => 'Community Outreach Initiative',
        'status' => 'not-started',
        'deadline' => '2024-02-01',
        'progress' => 0
    ],
    [
        'id' => 4,
        'title' => 'Quarterly Report Preparation',
        'status' => 'in-progress',
        'deadline' => '2024-01-30',
        'progress' => 45
    ]
];

$notifications = [
    [
        'id' => 1,
        'title' => 'New Activity Assigned',
        'message' => 'You have been assigned to the Policy Review Meeting.',
        'type' => 'info',
        'time' => '2 hours ago'
    ],
    [
        'id' => 2,
        'title' => 'Deadline Reminder',
        'message' => 'Training Program Development is due in 3 days.',
        'type' => 'warning',
        'time' => '1 day ago'
    ],
    [
        'id' => 3,
        'title' => 'Activity Completed',
        'message' => 'Policy Review Meeting has been marked as completed.',
        'type' => 'success',
        'time' => '2 days ago'
    ]
];

$unread_notifications = 3;

// Include header
include 'includes/header.php';
?>

<!-- Include Navigation -->
<?php include 'includes/navbar.php'; ?>

<!-- Main Content -->
<main class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <section class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-text-primary flex items-center gap-2">
                    <i class="fas fa-tachometer-alt"></i>
                    Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
                </h1>
                <p class="text-text-secondary mt-2">Here's an overview of your activities and recent updates.</p>
            </div>
        </div>
    </section>

        <!-- Dashboard Statistics -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-2xl bg-primary">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-text-primary m-0"><?php echo $dashboard_stats['total_activities']; ?></h3>
                        <p class="text-text-secondary text-sm m-0 font-medium">Total Activities</p>
                    </div>
                </div>
                
                <div class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-2xl bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-text-primary m-0"><?php echo $dashboard_stats['completed_activities']; ?></h3>
                        <p class="text-text-secondary text-sm m-0 font-medium">Completed</p>
                    </div>
                </div>
                
                <div class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-2xl bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-text-primary m-0"><?php echo $dashboard_stats['pending_activities']; ?></h3>
                        <p class="text-text-secondary text-sm m-0 font-medium">Pending</p>
                    </div>
                </div>
                
                <div class="bg-surface rounded-xl shadow-card border border-secondary-200 p-6 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-2xl bg-error">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-bold text-text-primary m-0"><?php echo $dashboard_stats['overdue_activities']; ?></h3>
                        <p class="text-text-secondary text-sm m-0 font-medium">Overdue</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="mb-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Activities -->
                <div class="lg:col-span-2">
                    <div class="bg-surface rounded-xl shadow-card border border-secondary-200">
                        <div class="px-6 py-4 border-b border-secondary-200 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-text-primary flex items-center gap-2">
                                <i class="fas fa-list-alt"></i>
                                Recent Activities
                            </h3>
                            <?php $roleDir = (function_exists('is_secretariat') && is_secretariat()) ? 'is_secretariat' : 'is_representative'; ?>
                            <a href="<?php echo $base_url; ?>pages/<?php echo $roleDir; ?>/activity_management.php" class="btn-primary text-sm px-3 py-2 rounded-md">
                                View All
                            </a>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col gap-4">
                                <?php foreach ($recent_activities as $activity): ?>
                                <div class="flex justify-between items-center p-4 bg-secondary-50 rounded-lg border border-secondary-200">
                                    <div>
                                        <h4 class="text-base font-semibold text-text-primary m-0"><?php echo htmlspecialchars($activity['title']); ?></h4>
                                        <p class="text-sm text-text-secondary m-0 flex items-center gap-1">
                                            <i class="fas fa-calendar"></i>
                                            Due: <?php echo date('M j, Y', strtotime($activity['deadline'])); ?>
                                        </p>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        switch ($activity['status']) {
                                            case 'completed':
                                                $status_class = 'bg-success-100 text-success';
                                                $status_text = 'Completed';
                                                break;
                                            case 'in-progress':
                                                $status_class = 'bg-primary-50 text-primary';
                                                $status_text = 'In Progress';
                                                break;
                                            case 'not-started':
                                                $status_class = 'bg-secondary-100 text-secondary-700';
                                                $status_text = 'Not Started';
                                                break;
                                        }
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        <div class="w-24 h-1.5 bg-secondary-200 rounded overflow-hidden">
                                            <div class="h-full bg-primary" style="width: <?php echo $activity['progress']; ?>%"></div>
                                        </div>
                                        <span class="text-xs text-text-secondary font-medium"><?php echo $activity['progress']; ?>%</span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div>
                    <div class="bg-surface rounded-xl shadow-card border border-secondary-200">
                        <div class="px-6 py-4 border-b border-secondary-200 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-text-primary flex items-center gap-2">
                                <i class="fas fa-bell"></i>
                                Recent Notifications
                            </h3>
                            <?php $notifHref = $base_url . 'pages/is_secretariat/notification_center.php'; ?>
                            <a href="<?php echo $notifHref; ?>" class="btn-secondary text-sm px-3 py-2 rounded-md">
                                View All
                            </a>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col gap-4">
                                <?php foreach ($notifications as $notification): ?>
                                <div class="flex gap-3 p-4 bg-secondary-50 rounded-lg border border-secondary-200">
                                    <div class="text-lg mt-1 <?php echo $notification['type'] == 'info' ? 'text-primary' : ($notification['type'] == 'warning' ? 'text-warning' : 'text-success'); ?>">
                                        <i class="fas fa-<?php echo $notification['type'] == 'info' ? 'info-circle' : ($notification['type'] == 'warning' ? 'exclamation-triangle' : 'check-circle'); ?>"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-text-primary m-0"><?php echo htmlspecialchars($notification['title']); ?></h4>
                                        <p class="text-sm text-text-secondary m-0"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        <span class="text-xs text-text-secondary"><?php echo $notification['time']; ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8">
                <div class="bg-surface rounded-xl shadow-card border border-secondary-200">
                    <div class="px-6 py-4 border-b border-secondary-200">
                        <h3 class="text-lg font-semibold text-text-primary flex items-center gap-2">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            <a href="<?php echo $base_url; ?>pages/<?php echo $roleDir; ?>/activity_management.php" class="flex flex-col items-center text-center p-6 bg-surface border-2 border-secondary-200 rounded-xl no-underline text-text-secondary transition-micro hover:border-primary hover:shadow-card">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-2xl bg-primary mb-4">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <h4 class="text-base font-semibold text-text-primary m-0 mb-2">Create Activity</h4>
                                <p class="text-sm text-text-secondary m-0">Start a new activity or project</p>
                            </a>
                            
                            <a href="<?php echo $base_url; ?>pages/is_secretariat/report_generation.php" class="flex flex-col items-center text-center p-6 bg-surface border-2 border-secondary-200 rounded-xl no-underline text-text-secondary transition-micro hover:border-primary hover:shadow-card">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-2xl bg-success mb-4">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <h4 class="text-base font-semibold text-text-primary m-0 mb-2">Generate Report</h4>
                                <p class="text-sm text-text-secondary m-0">Create activity reports</p>
                            </a>
                            
                            <a href="<?php echo $base_url; ?>pages/<?php echo $roleDir; ?>/calendar_management.php" class="flex flex-col items-center text-center p-6 bg-surface border-2 border-secondary-200 rounded-xl no-underline text-text-secondary transition-micro hover:border-primary hover:shadow-card">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-2xl bg-accent mb-4">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <h4 class="text-base font-semibold text-text-primary m-0 mb-2">Schedule Event</h4>
                                <p class="text-sm text-text-secondary m-0">Add new calendar events</p>
                            </a>
                            
                            <a href="<?php echo $base_url; ?>user_profile_settings.php" class="flex flex-col items-center text-center p-6 bg-surface border-2 border-secondary-200 rounded-xl no-underline text-text-secondary transition-micro hover:border-primary hover:shadow-card">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-2xl bg-warning mb-4">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <h4 class="text-base font-semibold text-text-primary m-0 mb-2">Profile Settings</h4>
                                <p class="text-sm text-text-secondary m-0">Update your profile</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</main>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>
