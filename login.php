<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            redirectTo('index.php');
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UniFeed</title>
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

            <h2>BULLETIN BOARD SYSTEM</h2>

            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="login-btn">Login</button>

                <div class="forgot-password">
                    <a href="forgot-password.php">FORGOT PASSWORD</a>
                </div>

                <div class="register-link">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
            </form>
        </div>
    </div>
</body>

</html>