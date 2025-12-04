<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../database/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_role(['representative', 'secretariat']);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $pdo = $GLOBALS['pdo'];
    
    switch ($method) {
        case 'GET':
            if (isset($_GET['sent'])) {
                handleGetSentNotifications($pdo);
            } else {
                handleGetNotifications($pdo);
            }
            break;
        case 'POST':
            handleCreateNotification($pdo, $input);
            break;
        case 'PUT':
            handleUpdateNotification($pdo, $input);
            break;
        case 'DELETE':
            handleDeleteNotification($pdo);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

// Get notifications received by current user
function handleGetNotifications($pdo) {
    $userId = $_SESSION['user_id'];
    $isSecretariat = is_secretariat();
    
    // Determine which recipient types this user should see
    // - 'all' = everyone
    // - 'representatives' = users with is_representative = 1
    // - 'secretariat' = users with is_secretariat = 1
    $recipientType = $isSecretariat ? 'secretariat' : 'representatives';
    
    // Get notifications where:
    // 1. Notification is for 'all' users
    // 2. Notification is for user's role type
    // 3. User is explicitly in NotificationRecipient table
    // 4. Exclude notifications created by self
    $sql = "SELECT DISTINCT n.*, u.firstName, u.lastName, u.position,
                   COALESCE(nr.is_read, 0) as is_read,
                   nr.read_at
            FROM EmailNotification n
            LEFT JOIN User u ON n.created_by = u.UserID
            LEFT JOIN NotificationRecipient nr ON n.NotificationID = nr.NotificationID AND nr.UserID = ?
            WHERE n.created_by != ?
              AND (n.recipient = 'all' 
                   OR n.recipient = ?
                   OR EXISTS (SELECT 1 FROM NotificationRecipient WHERE NotificationID = n.NotificationID AND UserID = ?))
            ORDER BY n.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $userId, $recipientType, $userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate stats
    $total = count($notifications);
    $unread = count(array_filter($notifications, fn($n) => !$n['is_read']));
    $highPriority = count(array_filter($notifications, fn($n) => $n['priority'] === 'high'));
    
    // This week
    $weekAgo = date('Y-m-d', strtotime('-7 days'));
    $thisWeek = count(array_filter($notifications, fn($n) => $n['created_at'] >= $weekAgo));
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'stats' => [
            'total' => $total,
            'unread' => $unread,
            'highPriority' => $highPriority,
            'thisWeek' => $thisWeek
        ]
    ]);
}

// Get notifications sent by current user
function handleGetSentNotifications($pdo) {
    $userId = $_SESSION['user_id'];
    
    $sql = "SELECT n.*, 
                   (SELECT COUNT(*) FROM NotificationRecipient WHERE NotificationID = n.NotificationID) as recipient_count,
                   (SELECT COUNT(*) FROM NotificationRecipient WHERE NotificationID = n.NotificationID AND is_read = 1) as read_count
            FROM EmailNotification n
            WHERE n.created_by = ?
            ORDER BY n.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);
}

// Create new notification
function handleCreateNotification($pdo, $input) {
    if (!$input || empty($input['subject']) || empty($input['content'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Subject and content are required']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    $recipient = $input['recipient'] ?? 'all';
    $type = $input['type'] ?? 'general';
    $priority = $input['priority'] ?? 'medium';
    
    // Insert notification
    $sql = "INSERT INTO EmailNotification (created_by, recipient, subject, content, type, priority, sent_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $recipient, $input['subject'], $input['content'], $type, $priority]);
    
    $notificationId = $pdo->lastInsertId();
    
    // Add recipients based on recipient type
    $recipientSql = "";
    $recipientParams = [];
    
    switch ($recipient) {
        case 'all':
            $recipientSql = "INSERT INTO NotificationRecipient (NotificationID, UserID, received_at) 
                            SELECT ?, UserID, NOW() FROM User WHERE UserID != ?";
            $recipientParams = [$notificationId, $userId];
            break;
        case 'representatives':
            $recipientSql = "INSERT INTO NotificationRecipient (NotificationID, UserID, received_at) 
                            SELECT ?, UserID, NOW() FROM User WHERE is_representative = 1 AND UserID != ?";
            $recipientParams = [$notificationId, $userId];
            break;
        case 'secretariat':
            $recipientSql = "INSERT INTO NotificationRecipient (NotificationID, UserID, received_at) 
                            SELECT ?, UserID, NOW() FROM User WHERE is_secretariat = 1 AND UserID != ?";
            $recipientParams = [$notificationId, $userId];
            break;
    }
    
    if ($recipientSql) {
        $stmt = $pdo->prepare($recipientSql);
        $stmt->execute($recipientParams);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Notification sent successfully',
        'notification_id' => $notificationId
    ]);
}

// Update notification (mark as read/unread)
function handleUpdateNotification($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Notification ID is required']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    $notificationId = $input['id'];
    
    // Mark as read
    if (isset($input['is_read'])) {
        // Check if recipient record exists
        $checkSql = "SELECT * FROM NotificationRecipient WHERE NotificationID = ? AND UserID = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$notificationId, $userId]);
        
        if ($checkStmt->rowCount() == 0) {
            // Create recipient record
            $insertSql = "INSERT INTO NotificationRecipient (NotificationID, UserID, is_read, read_at, received_at) VALUES (?, ?, ?, ?, NOW())";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([$notificationId, $userId, $input['is_read'] ? 1 : 0, $input['is_read'] ? date('Y-m-d H:i:s') : null]);
        } else {
            // Update existing record
            $updateSql = "UPDATE NotificationRecipient SET is_read = ?, read_at = ? WHERE NotificationID = ? AND UserID = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([$input['is_read'] ? 1 : 0, $input['is_read'] ? date('Y-m-d H:i:s') : null, $notificationId, $userId]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Notification updated']);
        return;
    }
    
    // Mark all as read
    if (isset($input['markAllRead']) && $input['markAllRead']) {
        $sql = "UPDATE NotificationRecipient SET is_read = 1, read_at = NOW() WHERE UserID = ? AND is_read = 0";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        
        echo json_encode(['success' => true, 'message' => 'All notifications marked as read']);
        return;
    }
    
    echo json_encode(['success' => true]);
}

// Delete notification
function handleDeleteNotification($pdo) {
    $notificationId = $_GET['id'] ?? null;
    
    if (!$notificationId) {
        http_response_code(400);
        echo json_encode(['error' => 'Notification ID is required']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    
    // Check if user is the creator (can delete entirely) or just a recipient (remove from their view)
    $checkSql = "SELECT created_by FROM EmailNotification WHERE NotificationID = ?";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([$notificationId]);
    $notification = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($notification && $notification['created_by'] == $userId) {
        // Delete entire notification (cascade will remove recipients)
        $sql = "DELETE FROM EmailNotification WHERE NotificationID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$notificationId]);
    } else {
        // Just remove from recipient's view
        $sql = "DELETE FROM NotificationRecipient WHERE NotificationID = ? AND UserID = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$notificationId, $userId]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Notification deleted']);
}
?>
