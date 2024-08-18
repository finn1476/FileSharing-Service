<?php
// Include the database configuration file
require 'config.php'; // Verwendet die erstellte config.php

try {
    // Erstelle eine neue PDO-Verbindung
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Hole alle Daten aus der Tabelle file_upload_limits
    $stmt = $pdo->query('SELECT * FROM file_upload_limits');
    $fileUploadLimits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hole die E-Mail-Adresse aus der Tabelle configuration
    $stmt = $pdo->query('SELECT email FROM configuration WHERE id = 1');
    $emailConfig = $stmt->fetch(PDO::FETCH_ASSOC);
    $contactEmail = $emailConfig['email'];
} catch (PDOException $e) {
    echo "Verbindung zur Datenbank fehlgeschlagen: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Limits</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #121212;
            color: #e0e0e0;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .status-card {
            background-color: #1e1e1e;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            flex: 1 1 300px;
            max-width: 300px;
            padding: 20px;
            transition: transform 0.2s ease;
        }
        .status-card:hover {
            transform: translateY(-5px);
        }
        .status-card h3 {
            margin-top: 0;
            color: #4caf50;
            font-size: 24px;
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .status-card table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .status-card th, .status-card td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        .status-card th {
            background-color: #333;
            color: #4caf50;
        }
        .status-card tr:last-child td {
            border-bottom: none;
        }
        .contact {
            text-align: center;
            margin-top: 30px;
        }
        .contact p {
            font-size: 18px;
            color: #4caf50;
        }
        .contact a {
            color: #4caf50;
            text-decoration: none;
            font-weight: bold;
        }
        .contact a:hover {
            text-decoration: underline;
        }
        .button-container {
            text-align: center;
            margin-top: 20px;
        }
        .button-container a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.2s ease;
        }
        .button-container a:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center; color: #4caf50;">Available Subscription</h2>
    <div class="container">
        <?php foreach ($fileUploadLimits as $limit): ?>
        <div class="status-card">
            <h3><?php echo htmlspecialchars($limit['user_status']); ?></h3>
            <table>
                <tr>
                    <th>ID</th>
                    <td><?php echo htmlspecialchars($limit['id']); ?></td>
                </tr>
                <tr>
                    <th>Upload Limit (Total)</th>
                    <td><?php echo htmlspecialchars($limit['upload_limit']); ?> MB</td>
                </tr>
                <tr>
                    <th>Upload Limit (File)</th>
                    <td><?php echo htmlspecialchars($limit['upload_limit_file']); ?> MB</td>
                </tr>
                <tr>
                    <th>Duration (Days)</th>
                    <td><?php echo htmlspecialchars($limit['duration']); ?></td>
                </tr>
                <tr>
                    <th>Price (€)</th>
                    <td><?php echo htmlspecialchars($limit['price']); ?> €</td>
                </tr>
            </table>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="contact">
        <p>For inquiries and to purchase products, please contact us at: <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>"><?php echo htmlspecialchars($contactEmail); ?></a></p>
    </div>
    
    <div class="button-container">
        <a href="index.php">Back to Homepage</a>
    </div>
</body>
</html>
