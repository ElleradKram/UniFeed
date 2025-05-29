<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

requireLogin();

// Initialize arrays in case database tables don't exist
$categories = [];
$posts = [];
$organizations = [];

try {
    $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Categories table doesn't exist, use default
    $categories = [
        ['category_id' => 1, 'name' => 'General Events', 'color' => '#1B5E20'],
        ['category_id' => 2, 'name' => 'Academic Events', 'color' => '#2196F3'],
        ['category_id' => 3, 'name' => 'Sports Events', 'color' => '#4CAF50']
    ];
}

try {
    $stmt = $conn->query("
        SELECT p.*, o.name as org_name, u.full_name as author_name, c.name as category_name
        FROM posts p
        LEFT JOIN organizations o ON p.org_id = o.org_id
        JOIN users u ON p.user_id = u.user_id
        JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.is_pinned DESC, p.created_at DESC
        LIMIT 20
    ");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Posts table doesn't exist, use sample data
    $posts = [
        [
            'post_id' => 1,
            'title' => 'Welcome to UniFeed!',
            'content' => 'This is the official bulletin board system for CvSU Silang Campus. Post your events and announcements here!',
            'author_name' => 'System',
            'org_name' => 'Administration',
            'category_name' => 'General Events',
            'post_type' => 'announcement',
            'created_at' => date('Y-m-d H:i:s'),
            'event_date' => null,
            'event_location' => null
        ]
    ];
}

try {
    $stmt = $conn->query("SELECT * FROM organizations ORDER BY name");
    $organizations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Organizations table doesn't exist, use sample data
    $organizations = [
        ['org_id' => 1, 'name' => 'Computer Science Society', 'description' => 'Please import bullboard_simple.sql to initialize the database', 'logo_url' => 'images/profile.png'],
        ['org_id' => 2, 'name' => 'Student Government', 'description' => 'Please import bullboard_simple.sql to initialize the database', 'logo_url' => 'images/profile.png']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title></title>
</head>
<!-- SIDE NAV BAR -->

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="logo">
                <img src="images/cvsu-logo.png" alt="CvSU Logo">
                <span>UniFeed</span>
            </div>
            <nav>
                <a href="index.php" class="nav-item">
                    <img src="images/home.png" alt="Home">
                    <span>Home</span>
                </a>

                <a href="explore.php" class="nav-item">
                    <img src="images/explore.png" alt="Explore">
                    <span>Explore</span>
                </a>

                <a href="categories.php" class="nav-item">
                    <img src="images/category.png" alt="Category">
                    <span>Categories</span>
                </a>

                <a href="saved.php" class="nav-item">
                    <img src="images/saved.png" alt="Saved">
                    <span>Saved</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <!-- TOP NAV BAR -->
            <div class="top-icons">
                <div class="search-container">
                    <input type="text" placeholder="Search orgs, events, memos... e.g. org: 'CSHARP' or type:'memo'" />
                </div>
                <div class="top-icons">
                    <button class="icon-btn">
                        <img src="images/calendar.png" alt="Calendar" />
                        <span class="notification-badge">3</span>
                    </button>
                    <button class="icon-btn notif-btn">
                        <img src="images/notif.png" alt="Notifications" />
                        <span class="notif-badge">5</span>
                    </button>
                    <div class="notification-panel">
                        <!-- Notifications will be loaded here -->
                    </div>
                    <button class="icon-btn">
                        <img src="images/profile.png" alt="Profile" />
                    </button>
                </div>
            </div>
            <!--POSTS-->
            <section class="posts-section">
                <div class="posts-header">
                    <h2>Recent Posts</h2>
                    <a href="create_post.php" class="create-post-btn">+ Create Post</a>
                </div>
                <?php foreach ($posts as $post): ?>
                    <div class="post-card">
                        <div class="post-main">
                            <div class="post-header">
                                <img src="images/avatar.png" alt="User Avatar" />
                                <div class="post-meta">
                                    <div class="post-author">
                                        <strong><?php echo htmlspecialchars($post['author_name']); ?></strong>
                                        <?php if ($post['org_name']): ?>
                                            <span class="org-name">•
                                                <?php echo htmlspecialchars($post['org_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="post-info">
                                        <span class="category-tag"
                                            style="background-color: <?php echo isset($post['color']) ? $post['color'] : '#1B5E20'; ?>;">
                                            <?php echo htmlspecialchars($post['category_name']); ?>
                                        </span>
                                        <span class="post-type"><?php echo ucfirst($post['post_type']); ?></span>
                                        <span
                                            class="post-date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="post-content">
                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

                                <?php if ($post['event_date']): ?>
                                    <div class="event-details">
                                        <div class="event-date">
                                            <strong>📅 Event Date:</strong>
                                            <?php echo date('F j, Y g:i A', strtotime($post['event_date'])); ?>
                                        </div>
                                        <?php if (isset($post['event_location']) && $post['event_location']): ?>
                                            <div class="event-location">
                                                <strong>📍 Location:</strong>
                                                <?php echo htmlspecialchars($post['event_location']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="post-actions">
                                <button class="action-btn save-btn" data-post-id="<?php echo $post['post_id']; ?>">
                                    🔖 Save
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>
            <!--ORGS-->
            <section class="orgs-section">
                <h2>Organizations</h2>
                <div class="orgs-list">
                    <?php foreach ($organizations as $org): ?>
                        <div class="org-card">
                            <img src="<?php echo htmlspecialchars($org['logo_url'] ?: 'images/profile.png'); ?>"
                                alt="Org Logo" />
                            <h3><?php echo htmlspecialchars($org['name']); ?></h3>
                            <p><?php echo htmlspecialchars($org['description']); ?></p>
                            <button class="view-btn">View</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
    <script src="js/app.js"></script>
</body>

</html>