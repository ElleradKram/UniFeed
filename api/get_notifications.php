<?php
require_once '../includes/config.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Get notifications for the user
    $stmt = $conn->prepare("
        SELECT n.*, p.title as post_title 
        FROM notifications n
        LEFT JOIN posts p ON n.related_post_id = p.post_id
        WHERE n.user_id = ?
        ORDER BY n.created_at DESC
        LIMIT 20
    ");
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get unread count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as unread_count 
        FROM notifications 
        WHERE user_id = ? AND is_read = 0
    ");
    $stmt->execute([$user_id]);
    $unread_count = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => (int) $unread_count
    ]);

} catch (PDOException $e) {
    // If notifications table doesn't exist, return empty data
    echo json_encode([
        'success' => true,
        'notifications' => [],
        'unread_count' => 0
    ]);
}
?>