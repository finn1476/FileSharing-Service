<?php
// Einbindung der config.php, um die Datenbankverbindung herzustellen
require_once 'config.php';

// Session starten
session_start();

// Funktion zum Kürzen der URL
function shortenUrl($url, $pdo, $userId = null) {
    $shortCode = substr(md5(uniqid(rand(), true)), 0, 6);
    $stmt = $pdo->prepare("INSERT INTO urls (short_code, original_url, user_id) VALUES (:short_code, :original_url, :user_id)");
    $stmt->execute([
        'short_code' => $shortCode,
        'original_url' => $url,
        'user_id' => $userId
    ]);
    return $shortCode;
}

// Dynamische Basis-URL basierend auf der aktuellen Domain und verkürztem Pfad
$baseURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]/u";

// Überprüfen, ob das Formular gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalUrl = filter_var($_POST['url'], FILTER_SANITIZE_URL);
    if (filter_var($originalUrl, FILTER_VALIDATE_URL)) {
        // Überprüfen, ob der Benutzer eingeloggt ist
        $userId = null;
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['username'])) {
            // Benutzer-ID aus dem Benutzernamen abrufen
            $username = $_SESSION['username'];
            $sql = "SELECT id FROM users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $userId = $user['id'];
            }
        }
        
        // URL kürzen und Benutzer-ID speichern
        $shortCode = shortenUrl($originalUrl, $pdo, $userId);
        $shortenedUrl = $baseURL . "/" . $shortCode;
    } else {
        $error = "Invalid URL.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Anonfile - URL Shortener</title>
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
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding-left: 5rem;
        padding-right: 5rem;
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      .url-container {
        text-align: center;
      }

      .url-container input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        margin-bottom: 20px;
        border: 1px solid var(--border-color);
        border-radius: 5px;
      }

      .url-container button {
        background-color: var(--button-color);
        color: white;
        padding: 10px 20px;
        font-size: 18px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }

      .url-container button:hover {
        background-color: var(--button-hover-color);
      }

      .shortened-url-container {
        margin-top: 20px;
        font-size: 18px;
        color: var(--text-color);
        word-break: break-all;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
      }

      .shortened-url {
        color: var(--primary-color);
        text-decoration: none;
      }

      .error {
        color: var(--error-color);
        margin-top: 20px;
        font-size: 18px;
        text-align: center;
      }

      .copy-button {
        background-color: var(--accent-color);
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }

      .copy-button:hover {
        background-color: #d97706;
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

include("templates/header.php");	
	
?>
</header>

  <main>
    <h2>Shorten Link</h2>
    <div class="url-container">
      <form action="linkshorter.php" method="post">
        <input type="url" name="url" placeholder="Enter the URL you want to shorten" required>
        <button type="submit">Shorten</button>
      </form>

      <?php if (isset($shortenedUrl)): ?>
        <div class="shortened-url-container">
          <a class="shortened-url" href="<?php echo htmlspecialchars($shortenedUrl); ?>" target="_blank"><?php echo htmlspecialchars($shortenedUrl); ?></a>
          <button class="copy-button" onclick="copyToClipboard('<?php echo htmlspecialchars($shortenedUrl); ?>')">Copy</button>
        </div>
      <?php elseif (isset($error)): ?>
        <div class="error">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <footer>
    <div class="footer-links">
      <a href="FAQ.php">FAQ</a>
      <a href="impressum.php">Imprint</a>
      <a href="abuse.php">Abuse</a>
      <a href="terms.php">ToS</a>
      <a href="datenschutz.php">Privacy Policy</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
  </footer>

  <script>
    function copyToClipboard(text) {
      const tempInput = document.createElement('input');
      tempInput.value = text;
      document.body.appendChild(tempInput);
      tempInput.select();
      document.execCommand('copy');
      document.body.removeChild(tempInput);
      alert('Link copied to clipboard!');
    }
  </script>
</body>
</html>
