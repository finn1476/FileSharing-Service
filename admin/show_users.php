<?php
// Define default values if not set
$currentDomain = $_SERVER['HTTP_HOST'];

// Function to get the list of existing users
function getExistingUsers() {
    $htpasswdFile = '.htpasswd';

    // Read the current .htpasswd file
    $htpasswdData = file($htpasswdFile, FILE_IGNORE_NEW_LINES);

    $users = array();
    foreach ($htpasswdData as $line) {
        list($username, $hash) = explode(':', $line, 2);
        $users[] = $username;
    }

    return $users;
}

// Get the list of existing users
$existingUsers = getExistingUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <title>Existing Users</title>
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

      main {
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      .awasr {
        border: 1px solid var(--border-color);
        padding: 20px;
        background-color: var(--background-color);
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .awasr h1 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      .awasr ul {
        list-style: none;
        padding: 0;
        margin: 0;
      }

      .awasr li {
        padding: 10px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 5px;
        background-color: white;
        border-radius: 4px;
      }

      .awasr li:last-child {
        border-bottom: none;
      }

      .awasr p {
        margin-top: 20px;
      }

      .buttona {
        background-color: var(--button-color);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        text-decoration: none;
        transition: background-color 0.3s ease;
        display: inline-block;
      }

      .buttona:hover {
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
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
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
    </style>
</head>
<body>
    <header>
        <div class="logo">Admin Panel</div>
        <nav>
            <a href="adminpanel5.php">Statistiken</a>
            <a href="adminpanel4.php">Datei-Typen</a>
            <a href="adminpanel3.php">Benutzer-Verwaltung</a>
            <a href="adminpanel2.php">Upload-Grenze</a>
            <a href="admindelete.php">Löschen</a>
        </nav>
    </header>
    <main>
        <div class="awasr">
            <h1>Existing Users</h1>

            <?php
            if (!empty($existingUsers)) {
                echo "<ul>";
                foreach ($existingUsers as $user) {
                    echo "<li>$user</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No existing users found.</p>";
            }
            ?>

            <p><a class="buttona" href="adminpanel3.php">Back to User Management</a></p>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-links">
            <a href="index.php">Linkpage</a>
            <a href="../index.php">Home</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>
</body>
</html>
