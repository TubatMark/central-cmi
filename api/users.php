<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../database/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Only secretariat can manage users
require_role(['secretariat']);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    if (!isset($pdo)) {
        throw new RuntimeException('Database connection not available');
    }
    
    switch ($method) {
        case 'GET':
            handleGetUsers($pdo);
            break;
        case 'POST':
            handleCreateUser($pdo, $input);
            break;
        case 'PUT':
            handleUpdateUser($pdo, $input);
            break;
        case 'DELETE':
            handleDeleteUser($pdo);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

function handleGetUsers($pdo) {
    // Optional filters
    $role = isset($_GET['role']) ? $_GET['role'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    
    $sql = "SELECT UserID, username, firstName, lastName, email, birthdate, 
                   designation, position, agency, is_representative, is_secretariat, 
                   created_at, updated_at 
            FROM User WHERE 1=1";
    $params = [];
    
    // Filter by role
    if ($role === 'secretariat') {
        $sql .= " AND is_secretariat = 1";
    } elseif ($role === 'representative') {
        $sql .= " AND is_representative = 1 AND is_secretariat = 0";
    }
    
    // Search filter
    if ($search) {
        $sql .= " AND (firstName LIKE ? OR lastName LIKE ? OR email LIKE ? OR username LIKE ?)";
        $searchTerm = '%' . $search . '%';
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Transform data for frontend
    $transformedUsers = array_map(function($user) {
        return [
            'id' => (int)$user['UserID'],
            'username' => $user['username'],
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'email' => $user['email'],
            'birthdate' => $user['birthdate'],
            'designation' => $user['designation'],
            'position' => $user['position'],
            'agency' => $user['agency'],
            'role' => $user['is_secretariat'] ? 'secretariat' : 'representative',
            'status' => 'active', // Could add status column to DB if needed
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at']
        ];
    }, $users);
    
    echo json_encode([
        'success' => true,
        'users' => $transformedUsers,
        'total' => count($transformedUsers)
    ]);
}

function handleCreateUser($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid input data']);
        return;
    }
    
    // Validate required fields
    $requiredFields = ['firstName', 'lastName', 'email', 'username', 'password', 'role'];
    foreach ($requiredFields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
            return;
        }
    }
    
    // Check if email or username already exists
    $checkStmt = $pdo->prepare('SELECT UserID FROM User WHERE email = ? OR username = ? LIMIT 1');
    $checkStmt->execute([$input['email'], $input['username']]);
    if ($checkStmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Email or username already exists']);
        return;
    }
    
    // Validate position if provided
    $validPositions = ['ICTC', 'RDC', 'SCC', 'TTC'];
    $position = isset($input['position']) ? strtoupper(trim($input['position'])) : 'ICTC';
    if (!in_array($position, $validPositions)) {
        $position = 'ICTC'; // Default
    }
    
    // Set role flags
    $isSecretariat = ($input['role'] === 'secretariat') ? 1 : 0;
    $isRepresentative = 1; // All users are representatives by default
    
    // Hash password
    $passwordHash = password_hash($input['password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO User (username, password_hash, firstName, lastName, email, birthdate, 
                              designation, position, agency, is_representative, is_secretariat, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $input['username'],
        $passwordHash,
        $input['firstName'],
        $input['lastName'],
        $input['email'],
        $input['birthdate'] ?? null,
        $input['designation'] ?? null,
        $position,
        $input['agency'] ?? null,
        $isRepresentative,
        $isSecretariat
    ]);
    
    if ($result) {
        $userId = $pdo->lastInsertId();
        echo json_encode([
            'success' => true,
            'message' => 'User created successfully',
            'user_id' => $userId
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create user']);
    }
}

function handleUpdateUser($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing user ID']);
        return;
    }
    
    $userId = $input['id'];
    
    // Check if user exists
    $checkStmt = $pdo->prepare('SELECT UserID FROM User WHERE UserID = ? LIMIT 1');
    $checkStmt->execute([$userId]);
    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        return;
    }
    
    // Check for duplicate email/username (excluding current user)
    if (isset($input['email']) || isset($input['username'])) {
        $checkDupSql = "SELECT UserID FROM User WHERE (email = ? OR username = ?) AND UserID != ? LIMIT 1";
        $checkDupStmt = $pdo->prepare($checkDupSql);
        $checkDupStmt->execute([
            $input['email'] ?? '',
            $input['username'] ?? '',
            $userId
        ]);
        if ($checkDupStmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Email or username already exists']);
            return;
        }
    }
    
    // Build update query dynamically
    $updateFields = [];
    $updateData = [];
    
    $allowedFields = ['username', 'firstName', 'lastName', 'email', 'birthdate', 
                      'designation', 'position', 'agency'];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateFields[] = "$field = ?";
            $updateData[] = $input[$field];
        }
    }
    
    // Handle role change
    if (isset($input['role'])) {
        $updateFields[] = "is_secretariat = ?";
        $updateData[] = ($input['role'] === 'secretariat') ? 1 : 0;
    }
    
    // Handle password change
    if (!empty($input['password'])) {
        $updateFields[] = "password_hash = ?";
        $updateData[] = password_hash($input['password'], PASSWORD_DEFAULT);
    }
    
    if (empty($updateFields)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'No fields to update']);
        return;
    }
    
    $updateData[] = $userId;
    $sql = "UPDATE User SET " . implode(', ', $updateFields) . " WHERE UserID = ?";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute($updateData);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update user']);
    }
}

function handleDeleteUser($pdo) {
    $userId = $_GET['id'] ?? null;
    
    if (!$userId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing user ID']);
        return;
    }
    
    // Prevent self-deletion
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Cannot delete your own account']);
        return;
    }
    
    // Check if user exists
    $checkStmt = $pdo->prepare('SELECT UserID FROM User WHERE UserID = ? LIMIT 1');
    $checkStmt->execute([$userId]);
    if (!$checkStmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
        return;
    }
    
    // Delete user (note: this may fail if user has related records due to FK constraints)
    // In production, consider soft delete instead
    try {
        $sql = "DELETE FROM User WHERE UserID = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$userId]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete user']);
        }
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => 'Cannot delete user with existing activities or records'
        ]);
    }
}
