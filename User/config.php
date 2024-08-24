<?php
$host = '127.0.0.1'; // Database host
$dbname = 'dggsfsffesdf'; // Database name
$username = 'af6d7f85'; // New database username
$password = '8d9a41ec283a22750c89d4b8b54a9045'; // Password for the new database user

// Create a database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>