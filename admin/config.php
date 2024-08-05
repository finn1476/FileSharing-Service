<?php
$host = '127.0.0.1'; // Database host
$dbname = 'a'; // Database name
$username = '637f321b'; // New database username
$password = '31e3afb0cacd0686e6b2fba222f49ea24ca3cfd1e51645ac2de9b6644fa3dc7e'; // Password for the new database user

// Create a database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>