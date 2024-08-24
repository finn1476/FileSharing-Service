<?php
require_once '../config.php';

// Get the short link code from the URL
$shortCode = filter_input(INPUT_GET, 'code', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Default wait time in seconds
$defaultWaitTime = 5;

// Get the wait time from the database or use the default time
$waitTime = $defaultWaitTime;

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <title>Anonfile - Redirect</title>
    <style>
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

      .message {
        font-size: 18px;
        color: var(--text-color);
        margin-bottom: 20px;
      }

      .countdown {
        font-size: 16px;
        color: var(--muted-text-color);
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
    <div class='logo'>Anonfile</div>
    <nav>
      <a href='index.php'>Home</a>
      <a href='pricing.php'>Pricing</a>
      <a href='User/login.php'>Login</a>
    </nav>
  </header>
  <main>
    <h2>Redirecting</h2>
    <div class='message'>";

if ($shortCode) {
    $stmt = $pdo->prepare("SELECT original_url, wait_time FROM urls WHERE short_code = :short_code");
    $stmt->execute(['short_code' => $shortCode]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $originalUrl = $result['original_url'];
        $waitTime = $result['wait_time'] ?? $defaultWaitTime;
        echo "You will be redirected in <span id='countdown'>$waitTime</span> seconds...<br>";
        echo "<meta http-equiv='refresh' content='$waitTime;url=$originalUrl'>";
    } else {
        echo "Short link not found.";
    }
} else {
    echo "No short link code provided.";
}

echo "</div>
  </main>
  <footer>
    <div class='footer-links'>
      <a href='FAQ.php'>FAQ</a>
      <a href='impressum.php'>Imprint</a>
      <a href='abuse.php'>Abuse</a>
      <a href='terms.php'>ToS</a>
      <a href='datenschutz.php'>Privacy Policy</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
  </footer>
  <script>
    // Countdown timer
    let countdownElement = document.getElementById('countdown');
    let countdownTime = parseInt(countdownElement.innerText);

    function updateCountdown() {
      if (countdownTime > 0) {
        countdownTime--;
        countdownElement.innerText = countdownTime;
      }
    }

    setInterval(updateCountdown, 1000);
  </script>
</body>
</html>";
?>
