<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

requireLogin();

$error = '';
$success = '';

// Get categories and organizations for the form
$stmt = $conn->query("SELECT * FROM categories ORDER BY name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("SELECT * FROM organizations WHERE is_active = 1 ORDER BY name");
$organizations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title']);
    $content = sanitizeInput($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $org_id = !empty($_POST['org_id']) ? (int)$_POST['org_id'] : null;
    $post_type = sanitizeInput($_POST['post_type']);
    $event_date = !empty($_POST['event_date']) ? $_POST['event_date'] : null;
    $event_location = !empty($_POST['event_location']) ? sanitizeInput($_POST['event_location']) : null;

    // Validation
    if (empty($title) || empty($content) || empty($category_id)) {
        $error = "Please fill in all required fields";
    } else {
        // Insert new post
        $stmt = $conn->prepare("
            INSERT INTO posts (user_id, org_id, category_id, title, content, post_type, event_date, event_location) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if ($stmt->execute([$_SESSION['user_id'], $org_id, $category_id, $title, $content, $post_type, $event_date, $event_location])) {
            $success = "Post created successfully!";
            // Clear form data
            $_POST = array();
        } else {
            $error = "Failed to create post. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - UniFeed</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="layout">
        <!-- SIDE NAV BAR -->
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
                    <button class="icon-btn">
                        <img src="images/notif.png" alt="Notifications" />
                        <span class="notif-badge">5</span>
                    </button>
                    <button class="icon-btn">
                        <img src="images/profile.png" alt="Profile" />
                    </button>
                </div>
            </div>

            <!-- CREATE POST FORM -->
            <section class="create-post-section">
                <div class="create-post-container">
                    <h2>Create New Post</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (!empty($success)): ?>
                        <div class="success-message"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" class="create-post-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="title">Title *</label>
                                <input type="text" id="title" name="title" required 
                                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="category_id">Category *</label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['category_id']; ?>"
                                                <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="post_type">Post Type</label>
                                <select id="post_type" name="post_type">
                                    <option value="event" <?php echo (isset($_POST['post_type']) && $_POST['post_type'] == 'event') ? 'selected' : ''; ?>>Event</option>
                                    <option value="announcement" <?php echo (isset($_POST['post_type']) && $_POST['post_type'] == 'announcement') ? 'selected' : ''; ?>>Announcement</option>
                                    <option value="memo" <?php echo (isset($_POST['post_type']) && $_POST['post_type'] == 'memo') ? 'selected' : ''; ?>>Memo</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="org_id">Organization (Optional)</label>
                                <select id="org_id" name="org_id">
                                    <option value="">Select Organization</option>
                                    <?php foreach ($organizations as $org): ?>
                                        <option value="<?php echo $org['org_id']; ?>"
                                                <?php echo (isset($_POST['org_id']) && $_POST['org_id'] == $org['org_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($org['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="event_date">Event Date (for events)</label>
                                <input type="datetime-local" id="event_date" name="event_date" 
                                       value="<?php echo isset($_POST['event_date']) ? $_POST['event_date'] : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="event_location">Event Location (for events)</label>
                            <input type="text" id="event_location" name="event_location" 
                                   placeholder="e.g., CvSU Gymnasium, Computer Laboratory, Online via Zoom"
                                   value="<?php echo isset($_POST['event_location']) ? htmlspecialchars($_POST['event_location']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="content">Content *</label>
                            <textarea id="content" name="content" rows="8" required 
                                      placeholder="Write your post content here..."><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="submit-btn">Create Post</button>
                            <a href="index.php" class="cancel-btn">Cancel</a>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
