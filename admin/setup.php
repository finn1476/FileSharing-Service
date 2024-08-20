<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read form inputs
    $host = $_POST['host'];
    $dbname = $_POST['dbname'];
    $adminUsername = $_POST['username'];
    $adminPassword = $_POST['password'];

    // Generate a random password for the new database user
    $newUserPassword = bin2hex(random_bytes(16)); // Changed from 32 to 16 for a more standard length
    $newUsername = bin2hex(random_bytes(4));

    try {
        // Connect to MySQL server
        $pdo = new PDO("mysql:host=$host;charset=utf8", $adminUsername, $adminPassword);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create the database if it does not exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        $pdo->exec("USE `$dbname`");

        // Create the tables and insert initial data
        $createTablesQuery = "
        CREATE TABLE configuration (
            id int(11) NOT NULL,
            email varchar(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        INSERT INTO configuration (id, email) VALUES
        (1, 'a@a.com');

        CREATE TABLE coupons (
            id int(11) NOT NULL,
            code varchar(255) NOT NULL,
            file_upload_limit_id int(11) NOT NULL,
            distributed tinyint(1) DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        CREATE TABLE file_upload_limits (
            id int(11) NOT NULL,
            user_status varchar(50) NOT NULL,
            upload_limit int(11) NOT NULL,
            upload_limit_file int(11) NOT NULL,
            duration int(11) NOT NULL,
            price decimal(10,2) NOT NULL DEFAULT 0.00,
            download_speed int(11) DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        INSERT INTO file_upload_limits (id, user_status, upload_limit, upload_limit_file, duration, price, download_speed) VALUES
        (1, 'Not_Registered', 5, 5, 0, 0.00, 5),
        (2, 'Registered', 500, 1000, 0, 0.00, 50),
        (3, 'Payed', 2000, 500, 30, 5.00, 123);

        CREATE TABLE reports (
            id int(11) NOT NULL,
            case_number varchar(6) NOT NULL,
            email varchar(255) NOT NULL,
            filename varchar(255) NOT NULL,
            description text NOT NULL,
            reason varchar(255) NOT NULL,
            passwords text NOT NULL,
            created_at timestamp NOT NULL DEFAULT current_timestamp()
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        CREATE TABLE uploads (
            id int(11) NOT NULL,
            user_id int(11) NOT NULL,
            file_size bigint(20) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        CREATE TABLE users (
            id int(11) NOT NULL,
            username varchar(255) NOT NULL,
            file_upload_limit_id int(11) NOT NULL DEFAULT 2,
            failed_attempts int(11) DEFAULT 0,
            upload_limit_expiration_date date DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

        ALTER TABLE configuration
            ADD PRIMARY KEY (id);

        ALTER TABLE coupons
            ADD PRIMARY KEY (id),
            ADD UNIQUE KEY code (code),
            ADD KEY file_upload_limit_id (file_upload_limit_id);

        ALTER TABLE file_upload_limits
            ADD PRIMARY KEY (id);

        ALTER TABLE reports
            ADD PRIMARY KEY (id);

        ALTER TABLE uploads
            ADD PRIMARY KEY (id),
            ADD KEY user_id (user_id);

        ALTER TABLE users
            ADD PRIMARY KEY (id),
            ADD KEY file_upload_limit_id (file_upload_limit_id);

        ALTER TABLE configuration
            MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

        ALTER TABLE coupons
            MODIFY id int(11) NOT NULL AUTO_INCREMENT;

        ALTER TABLE file_upload_limits
            MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

        ALTER TABLE reports
            MODIFY id int(11) NOT NULL AUTO_INCREMENT;

        ALTER TABLE uploads
            MODIFY id int(11) NOT NULL AUTO_INCREMENT;

        ALTER TABLE users
            MODIFY id int(11) NOT NULL AUTO_INCREMENT;

        ALTER TABLE coupons
            ADD CONSTRAINT coupons_ibfk_1 FOREIGN KEY (file_upload_limit_id) REFERENCES file_upload_limits (id);

        ALTER TABLE uploads
            ADD CONSTRAINT uploads_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;

        ALTER TABLE users
            ADD CONSTRAINT fk_file_upload_limit FOREIGN KEY (file_upload_limit_id) REFERENCES file_upload_limits (id);
        ";

        $pdo->exec($createTablesQuery);

        // Create the new user
        $pdo->exec("CREATE USER '$newUsername'@'localhost' IDENTIFIED BY '$newUserPassword'");
        $pdo->exec("GRANT ALL PRIVILEGES ON `$dbname`.* TO '$newUsername'@'localhost'");
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
        file_put_contents('../User/config.php', $configContent);
        
        echo "Database and tables successfully created. The configuration file has been generated.<br>";
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
                background-color: black;
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
