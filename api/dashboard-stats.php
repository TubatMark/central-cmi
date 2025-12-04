<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../database/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
require_role(['representative', 'secretariat']);

try {
    require_once __DIR__ . '/../database/config.php';
    $pdo = $GLOBALS['pdo'];
    
    $userId = $_SESSION['user_id'];
    $isSecretariat = is_secretariat();
    
    // Get activity statistics
    if ($isSecretariat) {
        // Secretariat sees all activities
        $activityStats = getActivityStats($pdo, null);
        $recentActivities = getRecentActivities($pdo, null, 4);
        $upcomingDeadlines = getUpcomingDeadlines($pdo, null, 5);
        $calendarEvents = getUpcomingCalendarEvents($pdo, null, 3);
    } else {
        // Representatives see only their activities
        $activityStats = getActivityStats($pdo, $userId);
        $recentActivities = getRecentActivities($pdo, $userId, 4);
        $upcomingDeadlines = getUpcomingDeadlines($pdo, $userId, 5);
        $calendarEvents = getUpcomingCalendarEvents($pdo, $userId, 3);
    }
    
    // Get notification count
    $notificationCount = getNotificationCount($pdo, $userId);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'activityStats' => $activityStats,
            'recentActivities' => $recentActivities,
            'upcomingDeadlines' => $upcomingDeadlines,
            'calendarEvents' => $calendarEvents,
            'notificationCount' => $notificationCount
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

function getActivityStats($pdo, $userId = null) {
    $whereClause = $userId ? "WHERE created_by = ?" : "";
    $params = $userId ? [$userId] : [];
    
    // Initialize variables
    $total = 0;
    $completed = 0;
    $inProgress = 0;
    $notStarted = 0;
    $overdue = 0;
    
    // Total activities
    $sql = "SELECT COUNT(*) as total FROM Activity $whereClause";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    
    // Completed activities
    $sql = "SELECT COUNT(*) as completed FROM Activity $whereClause AND status = 'completed'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $completed = $stmt->fetchColumn();
    
    // In progress activities
    $sql = "SELECT COUNT(*) as in_progress FROM Activity $whereClause AND status = 'in_progress'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $inProgress = $stmt->fetchColumn();
    
    // Not started activities
    $sql = "SELECT COUNT(*) as not_started FROM Activity $whereClause AND status = 'not_started'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $notStarted = $stmt->fetchColumn();
    
    // Overdue activities (past end date and not completed)
    $sql = "SELECT COUNT(*) as overdue FROM Activity $whereClause AND status != 'completed' AND reported_period_end < CURDATE()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $overdue = $stmt->fetchColumn();
    
    // Calculate completion rate
    $completionRate = $total > 0 ? round(($completed / $total) * 100) : 0;
    
    return [
        'total' => (int)$total,
        'completed' => (int)$completed,
        'in_progress' => (int)$inProgress,
        'not_started' => (int)$notStarted,
        'overdue' => (int)$overdue,
        'completion_rate' => $completionRate
    ];
}

function getRecentActivities($pdo, $userId = null, $limit = 4) {
    $whereClause = $userId ? "WHERE a.created_by = ?" : "";
    $params = $userId ? [$userId] : [];
    $params[] = $limit;
    
    $sql = "SELECT a.ActivityID, a.title, a.description, a.status, a.reported_period_start, a.reported_period_end,
                   u.firstName, u.lastName, u.position
            FROM Activity a 
            LEFT JOIN User u ON a.created_by = u.UserID 
            $whereClause
            ORDER BY a.created_at DESC 
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add progress calculation
    foreach ($activities as &$activity) {
        $activity['progress'] = calculateProgress($activity['status']);
        $activity['is_overdue'] = $activity['status'] !== 'completed' && 
                                 strtotime($activity['reported_period_end']) < time();
    }
    
    return $activities;
}

function getUpcomingDeadlines($pdo, $userId = null, $limit = 5) {
    $whereClause = $userId ? "WHERE a.created_by = ?" : "";
    $params = $userId ? [$userId] : [];
    $params[] = $limit;
    
    $sql = "SELECT a.ActivityID, a.title, a.reported_period_end, a.status
            FROM Activity a 
            $whereClause
            AND a.status != 'completed' 
            AND a.reported_period_end >= CURDATE()
            ORDER BY a.reported_period_end ASC 
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUpcomingCalendarEvents($pdo, $userId = null, $limit = 3) {
    $whereClause = $userId ? "WHERE c.created_by = ?" : "";
    $params = $userId ? [$userId] : [];
    $params[] = $limit;
    
    $sql = "SELECT c.CalendarID, c.details as title, c.date_start, c.date_end
            FROM CalendarActivity c 
            $whereClause
            AND c.date_start >= CURDATE()
            ORDER BY c.date_start ASC 
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getNotificationCount($pdo, $userId) {
    $sql = "SELECT COUNT(*) as count 
            FROM NotificationRecipient nr 
            JOIN EmailNotification en ON nr.NotificationID = en.NotificationID 
            WHERE nr.UserID = ? AND nr.received_at IS NULL";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return (int)$stmt->fetchColumn();
}

function calculateProgress($status) {
    switch ($status) {
        case 'completed':
            return 100;
        case 'in_progress':
            return 75; // Default progress for in-progress
        case 'not_started':
            return 0;
        default:
            return 0;
    }
}
?>
