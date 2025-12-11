<?php
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../database/auth.php';

// Check role for API (return JSON instead of redirect)
$isSecretariat = !empty($_SESSION['is_secretariat']) || (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'secretariat');
if (!$isSecretariat) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized - secretariat access required']);
    exit;
}

try {
    if (!isset($pdo)) {
        throw new RuntimeException('Database connection not available');
    }
    
    // Get dashboard statistics
    $stats = getDashboardStats($pdo);
    
    // Get activities list
    $activities = getActivitiesList($pdo);
    
    // Get recent system activity
    $recentActivity = getRecentSystemActivity($pdo);
    
    // Get notification stats
    $notificationStats = getNotificationStats($pdo);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'stats' => $stats,
            'activities' => $activities,
            'recentActivity' => $recentActivity,
            'notificationStats' => $notificationStats
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

function getDashboardStats($pdo) {
    // Total activities
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM Activity");
    $totalActivities = (int)$stmt->fetchColumn();
    
    // Activities by status (use COALESCE to handle empty tables)
    $stmt = $pdo->query("SELECT 
        COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) as completed,
        COALESCE(SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END), 0) as in_progress,
        COALESCE(SUM(CASE WHEN status = 'not_started' THEN 1 ELSE 0 END), 0) as not_started
    FROM Activity");
    $statusCounts = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Handle case when statusCounts is false (empty result)
    if (!$statusCounts) {
        $statusCounts = ['completed' => 0, 'in_progress' => 0, 'not_started' => 0];
    }
    
    // Overdue activities
    $stmt = $pdo->query("SELECT COUNT(*) FROM Activity WHERE status != 'completed' AND reported_period_end < CURDATE()");
    $overdueCount = (int)$stmt->fetchColumn();
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM User");
    $totalUsers = (int)$stmt->fetchColumn();
    
    // Pending reports (activities not completed)
    $pendingReports = (int)($statusCounts['not_started'] ?? 0) + (int)($statusCounts['in_progress'] ?? 0);
    
    // Notification stats
    $stmt = $pdo->query("SELECT COUNT(*) FROM EmailNotification");
    $totalNotifications = (int)$stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM EmailNotification WHERE sent_at IS NOT NULL");
    $sentNotifications = (int)$stmt->fetchColumn();
    
    $deliveryRate = $totalNotifications > 0 ? round(($sentNotifications / $totalNotifications) * 100, 1) : 100;
    
    return [
        'totalActivities' => $totalActivities,
        'completedActivities' => (int)($statusCounts['completed'] ?? 0),
        'inProgressActivities' => (int)($statusCounts['in_progress'] ?? 0),
        'notStartedActivities' => (int)($statusCounts['not_started'] ?? 0),
        'overdueActivities' => $overdueCount,
        'totalUsers' => $totalUsers,
        'pendingReports' => $pendingReports,
        'deliveryRate' => $deliveryRate
    ];
}

function getActivitiesList($pdo) {
    $sql = "SELECT 
                a.ActivityID,
                a.title,
                a.description,
                a.status,
                a.reported_period_start,
                a.reported_period_end,
                a.created_at,
                u.UserID,
                u.firstName,
                u.lastName,
                u.position,
                u.agency
            FROM Activity a
            LEFT JOIN User u ON a.created_by = u.UserID
            ORDER BY a.created_at DESC
            LIMIT 20";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Transform status for display
    return array_map(function($activity) {
        $isOverdue = $activity['status'] !== 'completed' && 
                     strtotime($activity['reported_period_end']) < time();
        
        $displayStatus = $activity['status'];
        if ($isOverdue) {
            $displayStatus = 'overdue';
        } elseif ($activity['status'] === 'in_progress') {
            $displayStatus = 'active';
        } elseif ($activity['status'] === 'not_started') {
            $displayStatus = 'pending';
        }
        
        return [
            'id' => (int)$activity['ActivityID'],
            'title' => $activity['title'],
            'description' => $activity['description'],
            'status' => $displayStatus,
            'rawStatus' => $activity['status'],
            'startDate' => $activity['reported_period_start'],
            'endDate' => $activity['reported_period_end'],
            'createdAt' => $activity['created_at'],
            'representative' => [
                'id' => (int)$activity['UserID'],
                'name' => trim($activity['firstName'] . ' ' . $activity['lastName']),
                'position' => $activity['position'],
                'agency' => $activity['agency']
            ],
            'isOverdue' => $isOverdue
        ];
    }, $activities);
}

function getRecentSystemActivity($pdo) {
    $recentActivity = [];
    
    // Recent completed activities (use created_at since Activity table has no updated_at)
    $stmt = $pdo->query("
        SELECT a.title, a.created_at, u.firstName, u.lastName
        FROM Activity a
        LEFT JOIN User u ON a.created_by = u.UserID
        WHERE a.status = 'completed'
        ORDER BY a.created_at DESC
        LIMIT 2
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $recentActivity[] = [
            'type' => 'completed',
            'icon' => 'check',
            'iconBg' => 'success',
            'title' => 'Activity completed by ' . trim($row['firstName'] . ' ' . $row['lastName']),
            'subtitle' => $row['title'],
            'time' => getTimeAgo($row['created_at'])
        ];
    }
    
    // Recent new users
    $stmt = $pdo->query("
        SELECT firstName, lastName, position, created_at
        FROM User
        ORDER BY created_at DESC
        LIMIT 2
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $recentActivity[] = [
            'type' => 'new_user',
            'icon' => 'plus',
            'iconBg' => 'primary',
            'title' => 'New user registered',
            'subtitle' => trim($row['firstName'] . ' ' . $row['lastName']) . ' - ' . $row['position'],
            'time' => getTimeAgo($row['created_at'])
        ];
    }
    
    // Recent activities with approaching deadlines
    $stmt = $pdo->query("
        SELECT title, reported_period_end
        FROM Activity
        WHERE status != 'completed' 
        AND reported_period_end BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ORDER BY reported_period_end ASC
        LIMIT 2
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $recentActivity[] = [
            'type' => 'deadline',
            'icon' => 'exclamation',
            'iconBg' => 'warning',
            'title' => 'Report deadline approaching',
            'subtitle' => $row['title'],
            'time' => 'Due ' . date('M j', strtotime($row['reported_period_end']))
        ];
    }
    
    // Recent notifications sent
    $stmt = $pdo->query("
        SELECT subject, sent_at
        FROM EmailNotification
        WHERE sent_at IS NOT NULL
        ORDER BY sent_at DESC
        LIMIT 2
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $recentActivity[] = [
            'type' => 'notification',
            'icon' => 'bell',
            'iconBg' => 'accent',
            'title' => 'Notification sent',
            'subtitle' => $row['subject'],
            'time' => getTimeAgo($row['sent_at'])
        ];
    }
    
    // Sort by most recent and limit to 5
    usort($recentActivity, function($a, $b) {
        return 0; // Keep original order for now
    });
    
    return array_slice($recentActivity, 0, 5);
}

function getNotificationStats($pdo) {
    // Pending notifications (created but not sent)
    $stmt = $pdo->query("SELECT COUNT(*) FROM EmailNotification WHERE sent_at IS NULL");
    $pending = (int)$stmt->fetchColumn();
    
    // Sent today
    $stmt = $pdo->query("SELECT COUNT(*) FROM EmailNotification WHERE DATE(sent_at) = CURDATE()");
    $sentToday = (int)$stmt->fetchColumn();
    
    // Failed (for now, we'll assume 0 since we don't track failures in current schema)
    $failed = 0;
    
    return [
        'pending' => $pending,
        'sentToday' => $sentToday,
        'failed' => $failed
    ];
}

function getTimeAgo($datetime) {
    if (!$datetime) return 'N/A';
    
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->d > 0) {
        return $diff->d == 1 ? '1 day ago' : $diff->d . ' days ago';
    } elseif ($diff->h > 0) {
        return $diff->h == 1 ? '1 hour ago' : $diff->h . ' hours ago';
    } elseif ($diff->i > 0) {
        return $diff->i == 1 ? '1 minute ago' : $diff->i . ' minutes ago';
    } else {
        return 'Just now';
    }
}
