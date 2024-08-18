<?php
$host = '127.0.0.1'; // Database host
$dbname = 'test'; // Database name
$username = 'd8d0a788'; // New database username
$password = '40ee657c74cb042a4cf1eb454e750826'; // Password for the new database user

// Create a database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>