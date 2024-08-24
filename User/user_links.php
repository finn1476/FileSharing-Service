<?php
session_start();
require_once '../config.php';

// Dynamische Basis-URL basierend auf der aktuellen Domain
$baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]/u";

// Überprüfen, ob der Benutzer angemeldet ist
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

// Benutzer-ID abrufen
$userId = null;
if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $sql = "SELECT id FROM users WHERE username = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $userId = $user['id'];
    }
}

// Link-Löschung verarbeiten
if (isset($_GET['delete'])) {
    $shortCode = filter_input(INPUT_GET, 'delete', FILTER_SANITIZE_STRING);
    if ($shortCode) {
        $sql = "DELETE FROM urls WHERE short_code = :short_code AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':short_code' => $shortCode, ':user_id' => $userId]);
        header("Location: user_links.php");
        exit;
    }
}

// Wartezeit-Update verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['short_code']) && isset($_POST['wait_time'])) {
    $shortCode = filter_input(INPUT_POST, 'short_code', FILTER_SANITIZE_STRING);
    $waitTime = filter_input(INPUT_POST, 'wait_time', FILTER_VALIDATE_INT);
    
    if ($waitTime >= 1 && $waitTime <= 60) {
        $sql = "UPDATE urls SET wait_time = :wait_time WHERE short_code = :short_code AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':wait_time' => $waitTime,
            ':short_code' => $shortCode,
            ':user_id' => $userId
        ]);
        header("Location: user_links.php");
        exit;
    }
}

// Benutzer-Links abrufen
$sql = "SELECT short_code, original_url, wait_time FROM urls WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $userId]);
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Links</title>
    <style>
      /* Dein vorhandenes Styling */
      :root {
        --primary-color: #005f73;
        --secondary-color: #94d2bd;
        --accent-color: #ee9b00;
        --background-color: #f7f9fb;
        --text-color: #023047;
        --muted-text-color: #8e9aaf;
        --border-color: #d9e2ec;
        --button-color: #56cfe1;
        --button-hover-color: #028090;
        --error-color: #e63946;
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
      }

      h1 {
        color: var(--primary-color);
        font-size: 24px;
        margin-bottom: 20px;
        text-align: center;
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
      }

      .container {
        background-color: white;
        border: 1px solid var(--border-color);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
      }

      table {
        width: 100%;
        border-collapse: collapse;
      }

      th, td {
        padding: 12px;
        border-bottom: 1px solid var(--border-color);
        text-align: left;
      }

      th {
        background-color: var(--primary-color);
        color: #fff;
        font-weight: bold;
      }

      td a {
        color: var(--button-color);
        text-decoration: none;
      }

      td a:hover {
        text-decoration: underline;
      }

      .logout {
        text-align: center;
        margin-top: 20px;
      }

      .logout a {
        color: var(--accent-color);
        text-decoration: none;
        margin-right: 15px;
      }

      .logout a:hover {
        text-decoration: underline;
      }

      .delete-button {
        background-color: var(--error-color);
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
      }

      .delete-button:hover {
        background-color: darkred;
      }

      .update-button {
        background-color: var(--button-color);
        color: #fff;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
      }

      .update-button:hover {
        background-color: var(--button-hover-color);
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
<?php

include("header.php");	
	
?>
   

</header>
<main>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <h2>Your Shortened Links:</h2>
        <table>
            <tr>
                <th>Shortened URL</th>
                <th>Original URL</th>
                <th>Wait Time (s)</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>
            <?php foreach ($links as $link) : ?>
                <tr>
                    <form action="user_links.php" method="post">
                        <td><a href="<?php echo htmlspecialchars($baseURL . '/' . $link['short_code']); ?>" target="_blank"><?php echo htmlspecialchars($baseURL . '/' . $link['short_code']); ?></a></td>
                        <td><?php echo htmlspecialchars($link['original_url']); ?></td>
                        <td>
                            <input type="hidden" name="short_code" value="<?php echo htmlspecialchars($link['short_code']); ?>">
                            <input type="number" name="wait_time" value="<?php echo htmlspecialchars($link['wait_time']); ?>" min="1" max="60">
                        </td>
                        <td><button class="update-button" type="submit">Update</button></td>
                        <td><a class="delete-button" href="user_links.php?delete=<?php echo urlencode($link['short_code']); ?>" onclick="return confirm('Are you sure you want to delete this link?')">Delete</a></td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</main>
<footer class="footer">
    <div class="footer-links">
        <a href="../FAQ.php">FAQ</a>
        <a href="../impressum.php">Imprint</a>
        <a href="../abuse.php">Abuse</a>
        <a href="../terms.php">ToS</a>
        <a href="../datenschutz.php">Privacy Policy</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
</footer>
</body>
</html>
