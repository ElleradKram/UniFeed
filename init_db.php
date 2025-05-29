<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - UniFeed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .info {
            color: blue;
        }

        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
        }

        .btn {
            background: #1B5E20;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #2E7D32;
        }
    </style>
</head>

<body>
    <h1>UniFeed Database Setup</h1>

    <?php if (isset($_POST['setup'])): ?>
        <h2>Setting up database...</h2>
        <pre>
    <?php
    $host = 'localhost';
    $username = 'root';
    $password = '';

    try {
        // Connect to MySQL server
        $conn = new PDO("mysql:host=$host", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "✓ Connected to MySQL server successfully.\n";

        // Create database
        $conn->exec("CREATE DATABASE IF NOT EXISTS bullboard_db");
        echo "✓ Database 'bullboard_db' created or already exists.\n";

        // Use the database
        $conn->exec("USE bullboard_db");

        // Read and execute the schema
        $sql = file_get_contents('database/schema.sql');

        // Remove the CREATE DATABASE and USE statements since we already handled them
        $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS bullboard_db;/', '', $sql);
        $sql = preg_replace('/USE bullboard_db;/', '', $sql);

        // Split into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        $successCount = 0;
        $errorCount = 0;

        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $conn->exec($statement);
                    echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
                    $successCount++;
                } catch (PDOException $e) {
                    echo "✗ Error: " . $e->getMessage() . "\n";
                    echo "Statement: " . substr($statement, 0, 100) . "...\n";
                    $errorCount++;
                }
            }
        }

        echo "\n=== SETUP COMPLETED ===\n";
        echo "✓ Successful operations: $successCount\n";
        if ($errorCount > 0) {
            echo "✗ Errors encountered: $errorCount\n";
        }

        echo "\nTest accounts created:\n";
        echo "- test@cvsu.edu.ph / test123\n";
        echo "- admin@cvsu.edu.ph / admin123\n";
        echo "- student@cvsu.edu.ph / student123\n";

    } catch (PDOException $e) {
        echo "✗ Connection failed: " . $e->getMessage() . "\n";
    }
    ?>
            </pre>

        <div style="margin-top: 20px;">
            <a href="login.php" class="btn">Go to Login Page</a>
            <a href="index.php" class="btn" style="margin-left: 10px;">Go to Dashboard</a>
        </div>

    <?php else: ?>
        <p>This will set up the UniFeed database with all necessary tables and sample data.</p>

        <form method="POST">
            <button type="submit" name="setup" class="btn">Setup Database</button>
        </form>

        <h3>What this will do:</h3>
        <ul>
            <li>Create the 'bullboard_db' database</li>
            <li>Create all necessary tables (users, posts, organizations, etc.)</li>
            <li>Insert sample categories and organizations</li>
            <li>Create test user accounts</li>
            <li>Insert sample posts</li>
        </ul>

        <h3>Test Accounts:</h3>
        <ul>
            <li><strong>test@cvsu.edu.ph</strong> - Password: test123</li>
            <li><strong>admin@cvsu.edu.ph</strong> - Password: admin123</li>
            <li><strong>student@cvsu.edu.ph</strong> - Password: student123</li>
        </ul>
    <?php endif; ?>
</body>

</html>