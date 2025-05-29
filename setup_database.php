<?php
// Simple database setup script
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL server (without specifying database)
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully.\n";

    // Create database
    $conn->exec("CREATE DATABASE IF NOT EXISTS bullboard_db");
    echo "Database 'bullboard_db' created or already exists.\n";

    // Use the database
    $conn->exec("USE bullboard_db");

    // Read and execute the schema
    $sql = file_get_contents('database/schema.sql');

    // Remove the CREATE DATABASE and USE statements since we already handled them
    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS bullboard_db;/', '', $sql);
    $sql = preg_replace('/USE bullboard_db;/', '', $sql);

    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
                echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }

    echo "\nDatabase setup completed successfully!\n";
    echo "You can now login with:\n";
    echo "- test@cvsu.edu.ph / test123\n";
    echo "- admin@cvsu.edu.ph / admin123\n";
    echo "- student@cvsu.edu.ph / student123\n";

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>