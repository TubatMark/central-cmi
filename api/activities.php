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

// Handle FormData or JSON input
$input = null;
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($contentType, 'multipart/form-data') !== false) {
    // FormData submission (with file uploads)
    $input = $_POST;
    // Check for method override (for PUT requests via POST)
    if (isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
        $method = 'PUT';
    }
} else {
    // JSON submission
    $input = json_decode(file_get_contents('php://input'), true);
}

try {
    require_once __DIR__ . '/../database/config.php';
    $pdo = $GLOBALS['pdo'];
    
    switch ($method) {
        case 'GET':
            handleGetActivities($pdo);
            break;
        case 'POST':
            handleCreateActivity($pdo, $input);
            break;
        case 'PUT':
            handleUpdateActivity($pdo, $input);
            break;
        case 'DELETE':
            handleDeleteActivity($pdo);
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

function handleGetActivities($pdo) {
    $userId = $_SESSION['user_id'];
    
    // Both secretariat and representatives see only their own activities
    $sql = "SELECT a.*, u.firstName, u.lastName, u.position, u.agency 
            FROM Activity a 
            LEFT JOIN User u ON a.created_by = u.UserID 
            WHERE a.created_by = ? 
            ORDER BY a.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    
    $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Check if ActivityAttachment table exists
    $attachmentTableExists = false;
    try {
        $checkTable = $pdo->query("SHOW TABLES LIKE 'ActivityAttachment'");
        $attachmentTableExists = $checkTable->rowCount() > 0;
    } catch (Exception $e) {
        $attachmentTableExists = false;
    }
    
    $attachmentStmt = null;
    if ($attachmentTableExists) {
        $attachmentSql = "SELECT * FROM ActivityAttachment WHERE ActivityID = ?";
        $attachmentStmt = $pdo->prepare($attachmentSql);
    }
    
    foreach ($activities as &$activity) {
        // Decode JSON fields
        if ($activity['accomplishmentDetails']) {
            $activity['accomplishmentDetails'] = json_decode($activity['accomplishmentDetails'], true);
        }
        
        // Get attachments if table exists
        if ($attachmentStmt) {
            $attachmentStmt->execute([$activity['ActivityID']]);
            $activity['attachments'] = $attachmentStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $activity['attachments'] = [];
        }
    }
    
    echo json_encode([
        'success' => true,
        'activities' => $activities
    ]);
}

function handleCreateActivity($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input data']);
        return;
    }
    
    // Validate required fields
    $requiredFields = ['title', 'type', 'startDate', 'endDate'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Missing required field: $field"]);
            return;
        }
    }
    
    $userId = $_SESSION['user_id'];
    
    // Prepare activity data
    $activityData = [
        'created_by' => $userId,
        'title' => $input['title'],
        'description' => $input['description'] ?? null,
        'reported_period_start' => $input['startDate'],
        'reported_period_end' => $input['endDate'],
        'location' => $input['location'] ?? null,
        'participants_count' => !empty($input['participants_count']) ? $input['participants_count'] : null,
        'budget_amount' => $input['budget_amount'] ?? null,
        'status' => $input['status'] ?? 'not_started',
        'accomplishmentDetails' => json_encode($input['accomplishments'] ?? [])
    ];
    
    // Insert activity
    $sql = "INSERT INTO Activity (created_by, title, description, reported_period_start, reported_period_end, 
            location, participants_count, budget_amount, status, accomplishmentDetails) 
            VALUES (:created_by, :title, :description, :reported_period_start, :reported_period_end, 
            :location, :participants_count, :budget_amount, :status, :accomplishmentDetails)";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($activityData);
    
    if ($result) {
        $activityId = $pdo->lastInsertId();
        
        // Handle file uploads
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            handleFileUploads($pdo, $activityId, $_FILES['images']);
        }
        
        // Insert related data based on activity type
        if (isset($input['accomplishments'])) {
            insertAccomplishmentData($pdo, $activityId, $input['accomplishments']);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Activity created successfully',
            'activity_id' => $activityId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create activity']);
    }
}

function handleUpdateActivity($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing activity ID']);
        return;
    }
    
    $activityId = $input['id'];
    $userId = $_SESSION['user_id'];
    $isSecretariat = is_secretariat();
    
    // Check if user can update this activity
    if (!$isSecretariat) {
        $checkSql = "SELECT created_by FROM Activity WHERE ActivityID = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$activityId]);
        $activity = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$activity || $activity['created_by'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'Not authorized to update this activity']);
            return;
        }
    }
    
    // Prepare update data
    $updateFields = [];
    $updateData = ['id' => $activityId];
    
    $allowedFields = ['title', 'description', 'reported_period_start', 'reported_period_end', 
                     'location', 'participants_count', 'budget_amount', 'status'];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateFields[] = "$field = :$field";
            $updateData[$field] = $input[$field];
        }
    }
    
    if (isset($input['accomplishments'])) {
        $updateFields[] = "accomplishmentDetails = :accomplishmentDetails";
        $updateData['accomplishmentDetails'] = json_encode($input['accomplishments']);
    }
    
    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }
    
    $sql = "UPDATE Activity SET " . implode(', ', $updateFields) . " WHERE ActivityID = :id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($updateData);
    
    if ($result) {
        // Handle file uploads for updates
        if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
            handleFileUploads($pdo, $activityId, $_FILES['images']);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Activity updated successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update activity']);
    }
}

function handleDeleteActivity($pdo) {
    $activityId = $_GET['id'] ?? null;
    
    if (!$activityId) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing activity ID']);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    $isSecretariat = is_secretariat();
    
    // Check if user can delete this activity
    if (!$isSecretariat) {
        $checkSql = "SELECT created_by FROM Activity WHERE ActivityID = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$activityId]);
        $activity = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$activity || $activity['created_by'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'Not authorized to delete this activity']);
            return;
        }
    }
    
    // Delete activity (cascade will handle related records)
    $sql = "DELETE FROM Activity WHERE ActivityID = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$activityId]);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Activity deleted successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete activity']);
    }
}

function insertAccomplishmentData($pdo, $activityId, $accomplishments) {
    // Insert NonDegreeTraining data
    if (isset($accomplishments['ict_resources_shared']['data'])) {
        foreach ($accomplishments['ict_resources_shared']['data'] as $training) {
            if (!empty($training['resource'])) {
                $sql = "INSERT INTO NonDegreeTraining (ActivityID, title, date_venue, num_participants, expenditures, source_of_funds) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $activityId,
                    $training['resource'] ?? '',
                    $training['recipient'] ?? '',
                    $training['quantity'] ?? 0,
                    $training['amount'] ?? null,
                    $training['remarks'] ?? ''
                ]);
            }
        }
    }
    
    // Insert Award data
    if (isset($accomplishments['systems_developed']['data'])) {
        foreach ($accomplishments['systems_developed']['data'] as $award) {
            if (!empty($award['system_name'])) {
                $sql = "INSERT INTO Award (ActivityID, scope, title, recipient_agency, sponsor, event_activity, place_of_award, date_of_award) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $activityId,
                    'Local', // Default scope
                    $award['system_name'] ?? '',
                    $award['purpose'] ?? '',
                    $award['users'] ?? '',
                    $award['status'] ?? '',
                    '',
                    null
                ]);
            }
        }
    }
}

function handleFileUploads($pdo, $activityId, $files) {
    // Check if ActivityAttachment table exists
    try {
        $checkTable = $pdo->query("SHOW TABLES LIKE 'ActivityAttachment'");
        if ($checkTable->rowCount() == 0) {
            return;
        }
    } catch (Exception $e) {
        return;
    }
    
    $uploadDir = __DIR__ . '/../uploads/activities/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            return;
        }
    }
    
    // Make sure directory is writable
    if (!is_writable($uploadDir)) {
        chmod($uploadDir, 0777);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    $sql = "INSERT INTO ActivityAttachment (ActivityID, filename, original_name, file_type, file_size) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Handle multiple file uploads
    $fileCount = count($files['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        
        $originalName = $files['name'][$i];
        $fileType = $files['type'][$i];
        $fileSize = $files['size'][$i];
        $tmpName = $files['tmp_name'][$i];
        
        // Validate file type - also check by extension as fallback
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($fileType, $allowedTypes) && !in_array($extension, $allowedExtensions)) {
            continue;
        }
        
        // Validate file size
        if ($fileSize > $maxSize) {
            continue;
        }
        
        // Generate unique filename
        $filename = 'activity_' . $activityId . '_' . time() . '_' . $i . '.' . $extension;
        $targetPath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($tmpName, $targetPath)) {
            // Insert attachment record
            try {
                $stmt->execute([
                    $activityId,
                    $filename,
                    $originalName,
                    $fileType,
                    $fileSize
                ]);
            } catch (Exception $e) {
                // Silent fail for individual files
            }
        }
    }
}
?>
