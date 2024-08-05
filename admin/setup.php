<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read form inputs
    $host = $_POST['host'];
    $dbname = $_POST['dbname'];
    $adminUsername = $_POST['username'];
    $adminPassword = $_POST['password'];

    // Generate a random password for the new database user
    $newUserPassword = bin2hex(random_bytes(32));
    $newUsername = bin2hex(random_bytes(4));

    try {
        // Connect to MySQL server
        $pdo = new PDO("mysql:host=$host;charset=utf8", $adminUsername, $adminPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create the database if it does not exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo->exec("USE $dbname");

        // Create the table
        $createTableQuery = "
        CREATE TABLE IF NOT EXISTS `reports` (
          `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `case_number` varchar(6) NOT NULL,
          `email` varchar(255) NOT NULL,
          `filename` varchar(255) NOT NULL,
          `description` text NOT NULL,
          `reason` varchar(255) NOT NULL,
          `passwords` text NOT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp()
        )";
        $pdo->exec($createTableQuery);

        // Create the new user
        $pdo->exec("CREATE USER '$newUsername'@'localhost' IDENTIFIED BY '$newUserPassword'");
        $pdo->exec("GRANT ALL PRIVILEGES ON $dbname.* TO '$newUsername'@'localhost'");
        $pdo->exec("FLUSH PRIVILEGES");

        // Create the configuration file
        $configContent = <<<EOD
<?php
\$host = '$host'; // Database host
\$dbname = '$dbname'; // Database name
\$username = '$newUsername'; // New database username
\$password = '$newUserPassword'; // Password for the new database user

// Create a database connection
try {
    \$pdo = new PDO("mysql:host=\$host;dbname=\$dbname;charset=utf8", \$username, \$password);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException \$e) {
    echo "Connection failed: " . \$e->getMessage();
}
?>
EOD;

        file_put_contents('config.php', $configContent);
		file_put_contents('../config.php', $configContent);
        echo "Database and table successfully created. The configuration file has been generated.<br>";
        echo "New database user: <b>$newUsername</b><br>";
        echo "New database user password: <b>$newUserPassword</b><br>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // Display form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Setup</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                color: white;
				background-color:black;
            }
            .container {
                max-width: 600px;
                margin: auto;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 5px;
                background-color: black;
            }
            label {
                display: block;
                margin-bottom: 10px;
            }
            input[type="text"] {
                width: 100%;
                padding: 8px;
                margin-bottom: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            input[type="submit"] {
                background-color: #4caf50;
                color: black;
                padding: 10px 15px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
            input[type="submit"]:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Database Setup</h2>
            <form method="POST">
                <label for="host">Database Host:</label>
                <input type="text" id="host" name="host" value="127.0.0.1" required>

                <label for="dbname">Database Name:</label>
                <input type="text" id="dbname" name="dbname" required>

                <label for="username">Database Administrator Username:</label>
                <input type="text" id="username" name="username" value="root" required>

                <label for="password">Database Administrator Password:</label>
                <input type="text" id="password" name="password">

                <input type="submit" value="Create Database">
            </form>
        </div>
    </body>
    </html>
    <?php
}
?>
