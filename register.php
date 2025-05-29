<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectTo('index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = sanitizeInput($_POST['student_id']);
    $email = sanitizeInput($_POST['email']);
    $full_name = sanitizeInput($_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($student_id) || empty($email) || empty($full_name) || empty($password)) {
        $error = "Please fill in all fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if student ID or email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE student_id = ? OR email = ?");
        $stmt->execute([$student_id, $email]);
        
        if ($stmt->fetch()) {
            $error = "Student ID or email already exists";
        } else {
            // Create new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (student_id, email, full_name, password) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$student_id, $email, $full_name, $hashed_password])) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - UniFeed</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="logo">
                <img src="images/cvsu-logo.png" alt="CvSU Logo">
                <h1>CAVITE STATE UNIVERSITY</h1>
                <h2>SILANG CAMPUS</h2>
                <p>Truth | Excellence | Service</p>
            </div>

            <h2>CREATE ACCOUNT</h2>

            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" id="student_id" name="student_id" required 
                           value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required 
                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="login-btn">Register</button>

                <div class="forgot-password">
                    <a href="login.php">Already have an account? Login here</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
