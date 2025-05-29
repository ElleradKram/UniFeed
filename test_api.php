<?php
// Test script for API functionality
echo "=== Testing BullBoard API Functionality ===\n\n";

// Test database connection
echo "1. Testing Database Connection:\n";
try {
    require_once 'includes/db.php';
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

// Test notifications table
echo "\n2. Testing Notifications Table:\n";
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM notifications");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Notifications table exists with " . $result['count'] . " records\n";
} catch (Exception $e) {
    echo "✗ Notifications table test failed: " . $e->getMessage() . "\n";
}

// Test get notifications functionality
echo "\n3. Testing Get Notifications Functionality:\n";
try {
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([2]); // Test with user_id 2
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ Retrieved " . count($notifications) . " notifications for user 2\n";
    
    foreach ($notifications as $notif) {
        $status = $notif['is_read'] ? 'read' : 'unread';
        echo "  - " . $notif['title'] . " (" . $status . ")\n";
    }
} catch (Exception $e) {
    echo "✗ Get notifications test failed: " . $e->getMessage() . "\n";
}

// Test mark notification as read
echo "\n4. Testing Mark Notification as Read:\n";
try {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
    $result = $stmt->execute([1, 2]); // Mark notification 1 as read for user 2
    
    if ($result) {
        echo "✓ Successfully marked notification as read\n";
        
        // Verify the update
        $stmt = $conn->prepare("SELECT is_read FROM notifications WHERE notification_id = ?");
        $stmt->execute([1]);
        $notif = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✓ Verification: Notification is now " . ($notif['is_read'] ? 'read' : 'unread') . "\n";
    } else {
        echo "✗ Failed to mark notification as read\n";
    }
} catch (Exception $e) {
    echo "✗ Mark notification test failed: " . $e->getMessage() . "\n";
}

// Test posts retrieval
echo "\n5. Testing Posts Retrieval:\n";
try {
    $stmt = $conn->query("
        SELECT p.*, o.name as org_name, c.name as category_name
        FROM posts p
        LEFT JOIN organizations o ON p.org_id = o.org_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.created_at DESC
        LIMIT 5
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ Retrieved " . count($posts) . " posts\n";
    
    foreach ($posts as $post) {
        echo "  - " . $post['title'] . " by " . ($post['org_name'] ?: 'Unknown') . "\n";
    }
} catch (Exception $e) {
    echo "✗ Posts retrieval test failed: " . $e->getMessage() . "\n";
}

echo "\n=== API Testing Complete ===\n";
?>
