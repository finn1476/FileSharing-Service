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
      :root {
        --primary-color: #005f73;
        --secondary-color: #94d2bd;
        --accent-color: #ee9b00;
        --background-color: #f7f9fb;
        --text-color: #023047;
        --border-color: #d9e2ec;
        --button-color: #56cfe1;
        --button-hover-color: #028090;
      }

      body {
        font-family: 'Arial', sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: grid;
        grid-template-rows: auto 1fr auto;
      }

      header {
        background-color: var(--primary-color);
        padding: 10px 20px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid var(--secondary-color);
      }

      header .logo {
        font-size: 24px;
        font-weight: bold;
      }

      nav {
        display: flex;
        gap: 20px;
      }

      nav a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        font-weight: 500;
      }

      nav a:hover {
        color: var(--accent-color);
      }

      main {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        text-align: center;
        margin-bottom: 20px;
      }

      .container {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        justify-content: center;
      }

      .status-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        flex: 1 1 300px;
        max-width: 300px;
        padding: 20px;
        transition: transform 0.2s ease, background-color 0.3s ease;
      }

      .status-card:hover {
        transform: translateY(-5px);
        background-color: #f0f0f0;
      }

      .status-card h3 {
        margin-top: 0;
        color: var(--primary-color);
        font-size: 24px;
        text-align: center;
        border-bottom: 2px solid var(--border-color);
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
        border-bottom: 1px solid var(--border-color);
      }

      .status-card th {
        background-color: var(--border-color);
        color: var(--primary-color);
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
        color: var(--primary-color);
        margin: 0;
      }

      .contact a {
        color: var(--primary-color);
        text-decoration: none;
        font-weight: bold;
      }

      .contact a:hover {
        text-decoration: underline;
      }

      .button-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
      }

      .button-container a {
        display: inline-block;
        padding: 10px 20px;
        background-color: var(--button-color);
        color: white; /* Weißer Text auf dem Button */
        text-decoration: none;
        border-radius: 5px;
        font-size: 16px;
        transition: background-color 0.2s ease, transform 0.2s ease;
        margin: 0 10px;
      }

      .button-container a:hover {
        background-color: var(--button-hover-color);
        transform: scale(1.05);
      }

      .button-container a:active {
        transform: scale(0.95);
      }

      footer {
        background-color: var(--primary-color);
        padding: 20px;
        color: white;
        text-align: center;
        border-top: 3px solid var(--secondary-color);
      }

      footer .footer-links {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 10px;
      }

      footer .footer-links a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        transition: color 0.3s ease;
      }

      footer .footer-links a:hover {
        color: var(--accent-color);
      }
    </style>
</head>
<body>
  <header>
    <div class="logo">Anonfile</div>
    <nav>
      <a href="index.php">Home</a>
      <a href="pricing.php">Pricing</a>
      <a href="User/login.php">Login</a>
    </nav>
  </header>

  <main>
    <h2>Available Subscription</h2>
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
                    <th>Download Speed (kb/s)</th>
                    <td><?php echo htmlspecialchars($limit['download_speed']); ?> kb/s</td>
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
  </main>

  <footer class="footer">
    <div class="footer-links">
      <a href="FAQ.php">FAQ</a>
      <a href="impressum.php">Imprint</a>
     <a href="abuse.php">Abuse</a>
	 <a href="terms.php">ToS</a>
      <a href="datenschutz.php">Privacy Policy</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
  </footer>
</body>
</html>
