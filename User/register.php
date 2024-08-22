<?php
// Include config.php
include 'config.php';

function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = generateRandomString();
    $password = generateRandomString();

    // Path to the .htpasswd file
    $htpasswd_file = __DIR__ . '/password/.htpasswd';

    // Check if the directory exists
    if (!is_dir(__DIR__ . '/password')) {
        if (!mkdir(__DIR__ . '/password', 0755, true)) {
            die('Error creating the directory.');
        }
    }

    // Check if the file is writable or can be created
    if (!is_writable($htpasswd_file)) {
        if (file_exists($htpasswd_file)) {
            die('The .htpasswd file is not writable.');
        } else {
            if (file_put_contents($htpasswd_file, "") === false) {
                die('Error creating the .htpasswd file.');
            }
        }
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Add the username and password to the .htpasswd file
    $htpasswd_entry = "$username:$hashed_password\n";
    if (file_put_contents($htpasswd_file, $htpasswd_entry, FILE_APPEND | LOCK_EX) === false) {
        die('Error writing to the .htpasswd file.');
    } else {
        // Only store the username in the database
        try {
            // Database connection (from config.php)
            global $pdo;

            // SQL statement to insert the username
            $sql = "INSERT INTO users (username) VALUES (:username)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username
            ]);

            // Successful insertion
            echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv='X-UA-Compatible' content='ie=edge'>
    <meta name='robots' content='noindex'>
    <title>Registration Successful</title>
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
      }

      h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        max-width: 100%;
        margin: 0 auto;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      .button {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        background-color: var(--button-color);
        color: white;
        border-radius: 5px;
        margin: 5px;
        transition: background-color 0.3s ease;
      }

      .button:hover {
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
    <div class='logo'>Anonfile</div>
    <nav>
        <a href='../index.php'>Home</a>
        <a href='../pricing.php'>Pricing</a>
        <a href='login.php'>Login</a>
    </nav>
</header>
    <main>
        <div class='awasr'>
            <h2>Registration Successful!</h2>
            <p>Username: <span id='username'>$username</span> <button class='button' onclick='copyToClipboard(\"username\")'>Copy</button></p>
            <p>Password: <span id='password'>$password</span> <button class='button' onclick='copyToClipboard(\"password\")'>Copy</button></p>
            <p><a class='button' href='login.php'>Go to Login Page</a></p>
        </div>
    </main>
    <footer class='footer'>
        <div class='footer-links'>
            <a href='../FAQ.php'>FAQ</a>
            <a href='../impressum.php'>Imprint</a>
            <a href='../abuse.php'>Abuse</a>
            <a href='../terms.php'>ToS</a>
            <a href='../datenschutz.php'>Privacy Policy</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>
    <script>
        function copyToClipboard(id) {
            var copyText = document.getElementById(id).innerText;
            navigator.clipboard.writeText(copyText).then(function() {
                alert('Copied: ' + copyText);
            }, function(err) {
                alert('Error copying: ' + err);
            });
        }
    </script>
</body>
</html>";
        } catch (PDOException $e) {
            die('Error inserting the username into the database: ' . $e->getMessage());
        }
    }
} else {
    echo "Invalid request.";
}
?>
