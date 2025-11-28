<?php
// Inclure le fichier de connexion
require_once 'db_connect.php';

// Tester la connexion
$conn = getDatabaseConnection();

if ($conn !== null) {
    echo "<h2 style='color: green;'>✅ Connection successful!</h2>";
    echo "<p>Connected to database: <strong>" . DB_NAME . "</strong></p>";
} else {
    echo "<h2 style='color: red;'>❌ Connection failed!</h2>";
    echo "<p>Check the db_errors.log file for details.</p>";
}
?>