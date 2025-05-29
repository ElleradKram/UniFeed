<?php
require_once '../includes/db.php';

try {
    // Read the SQL file
    $sql = file_get_contents('schema.sql');
    
    // Split the SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "Setting up database...\n";
    
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
    
    echo "\nDatabase setup completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
