<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['notification_id'])) {
    echo json_encode(['success' => false, 'error' => 'Notification ID required']);
    exit;
}

$user_id = $_SESSION['user_id'];
$notification_id = $input['notification_id'];

try {
    // Mark notification as read (only if it belongs to the current user)
    $stmt = $conn->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE notification_id = ? AND user_id = ?
    ");
    $stmt->execute([$notification_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Notification not found or access denied']);
    }
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
