<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

requireLogin();

// Fetch categories for filtering
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch organizations for explore page
$stmt = $conn->query("SELECT * FROM organizations ORDER BY name LIMIT 30");
$organizations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Explore - UniFeed</title>
    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="logo">
                <img src="images/cvsu-logo.png" alt="CvSU Logo" />
                <span>UniFeed</span>
            </div>
            <nav>
                <a href="index.php" class="nav-item">
                    <img src="images/home.png" alt="Home" />
                    <span>Home</span>
                </a>
                <a href="explore.php" class="nav-item active">
                    <img src="images/explore.png" alt="Explore" />
                    <span>Explore</span>
                </a>
                <a href="categories.php" class="nav-item">
                    <img src="images/category.png" alt="Category" />
                    <span>Categories</span>
                </a>
                <a href="saved.php" class="nav-item">
                    <img src="images/saved.png" alt="Saved" />
                    <span>Saved</span>
                </a>
            </nav>
        </aside>

        <main class="main-content">
            <div class="top-bar">
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
                        <div class="notification-header">
                            <h3>Notifications</h3>
                        </div>
                        <!-- Notification items would be dynamically loaded here -->
                    </div>
                    <button class="icon-btn">
                        <img src="images/profile.png" alt="Profile" />
                    </button>
                </div>
            </div>

            <section class="orgs-section">
                <h2>Explore Organizations</h2>
                <div class="orgs-list">
                    <?php foreach ($organizations as $org): ?>
                        <div class="org-card">
                            <img src="<?php echo htmlspecialchars($org['logo_url'] ?: 'images/profile.png'); ?>" alt="Org Logo" />
                            <h3><?php echo htmlspecialchars($org['name']); ?></h3>
                            <p><?php echo htmlspecialchars($org['description']); ?></p>
                            <button class="view-btn">View</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>
    </div>
</body>

</html>
