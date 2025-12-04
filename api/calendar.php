<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../database/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
require_role(['representative', 'secretariat']);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    require_once __DIR__ . '/../database/config.php';
    $pdo = $GLOBALS['pdo'];
    
    switch ($method) {
        case 'GET':
            handleGetCalendarEvents($pdo);
            break;
        case 'POST':
            handleCreateCalendarEvent($pdo, $input);
            break;
        case 'PUT':
            handleUpdateCalendarEvent($pdo, $input);
            break;
        case 'DELETE':
            handleDeleteCalendarEvent($pdo);
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

function handleGetCalendarEvents($pdo) {
    $userId = $_SESSION['user_id'];
    $isSecretariat = is_secretariat();
    
    // Build query based on user role
    if ($isSecretariat) {
        // Secretariat can see all calendar events
        $sql = "SELECT c.*, u.firstName, u.lastName 
                FROM CalendarActivity c 
                LEFT JOIN User u ON c.created_by = u.UserID 
                ORDER BY c.date_start ASC";
        $stmt = $pdo->prepare($sql);
    } else {
        // Representatives can only see their own calendar events
        $sql = "SELECT c.*, u.firstName, u.lastName 
                FROM CalendarActivity c 
                LEFT JOIN User u ON c.created_by = u.UserID 
                WHERE c.created_by = ? 
                ORDER BY c.date_start ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
    }
    
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'events' => $events
    ]);
}

function handleCreateCalendarEvent($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }
    
    // Validate required fields
    $requiredFields = ['title', 'startDate', 'endDate'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing required field: $field"]);
            return;
        }
    }
    
    $userId = $_SESSION['user_id'];
    
    // Prepare calendar event data
    $eventData = [
        'created_by' => $userId,
        'date_start' => $input['startDate'],
        'date_end' => $input['endDate'],
        'details' => $input['details'] ?? null
    ];
    
    // Insert calendar event
    $sql = "INSERT INTO CalendarActivity (created_by, date_start, date_end, details) 
            VALUES (:created_by, :date_start, :date_end, :details)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($eventData);
    
    if ($result) {
        $eventId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Calendar event created successfully',
            'event_id' => $eventId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create calendar event']);
    }
}

function handleUpdateCalendarEvent($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing event ID']);
        return;
    }
    
    $eventId = $input['id'];
    $userId = $_SESSION['user_id'];
    $isSecretariat = is_secretariat();
    
    // Check if user can update this event
    if (!$isSecretariat) {
        $checkSql = "SELECT created_by FROM CalendarActivity WHERE CalendarID = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$eventId]);
        $event = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event || $event['created_by'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'Not authorized to update this event']);
            return;
        }
    }
    
    // Prepare update data
    $updateFields = [];
    $updateData = ['id' => $eventId];
    
    $allowedFields = ['date_start', 'date_end', 'details'];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateFields[] = "$field = :$field";
            $updateData[$field] = $input[$field];
        }
    }
    
    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }
    
    $sql = "UPDATE CalendarActivity SET " . implode(', ', $updateFields) . " WHERE CalendarID = :id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($updateData);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Calendar event updated successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update calendar event']);
    }
}

function handleDeleteCalendarEvent($pdo) {
    $eventId = $_GET['id'] ?? null;
    
    if (!$eventId) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing event ID']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    $isSecretariat = is_secretariat();
    
    // Check if user can delete this event
    if (!$isSecretariat) {
        $checkSql = "SELECT created_by FROM CalendarActivity WHERE CalendarID = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$eventId]);
        $event = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event || $event['created_by'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'Not authorized to delete this event']);
            return;
        }
    }
    
    // Delete calendar event
    $sql = "DELETE FROM CalendarActivity WHERE CalendarID = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$eventId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Calendar event deleted successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete calendar event']);
    }
}
?>
