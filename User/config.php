<?php
$host = '127.0.0.1'; // Database host
$dbname = 'afadafad'; // Database name
$username = 'c0ed3ab1'; // New database username
$password = '114aecaa4c84a48d47b3270332eb4bd7'; // Password for the new database user

// Create a database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>