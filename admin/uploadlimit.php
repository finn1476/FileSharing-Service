<?php
// Include config.php
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// If the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_limits'])) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'limit_') === 0) {
                $userStatus = str_replace(['limit_', '_file', '_duration', '_price', '_download_speed'], '', $key);
                $uploadLimit = $_POST['limit_' . $userStatus];
                $uploadLimitFile = $_POST['limit_' . $userStatus . '_file'];
                $duration = $_POST['duration_' . $userStatus];
                $price = $_POST['price_' . $userStatus];
                $downloadSpeed = $_POST['download_speed_' . $userStatus]; // New field

                $sql = "UPDATE file_upload_limits 
                        SET upload_limit = :uploadLimit, upload_limit_file = :uploadLimitFile, duration = :duration, price = :price, download_speed = :downloadSpeed 
                        WHERE user_status = :userStatus";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':uploadLimit' => $uploadLimit,
                    ':uploadLimitFile' => $uploadLimitFile,
                    ':duration' => $duration,
                    ':price' => $price,
                    ':downloadSpeed' => $downloadSpeed, // New parameter
                    ':userStatus' => $userStatus
                ]);
            }
        }
        echo "<p class='success'>Upload limits successfully updated!</p>";
    } elseif (isset($_POST['add_entry'])) {
        // Retrieve input values from the form
        $userStatus = $_POST['user_status'];
        $uploadLimit = $_POST['upload_limit'];
        $uploadLimitFile = $_POST['upload_limit_file'];
        $duration = $_POST['duration'];
        $price = $_POST['price'];
        $downloadSpeed = $_POST['download_speed'];

        // Check if the user status already exists
        $sql = "SELECT COUNT(*) FROM file_upload_limits WHERE user_status = :userStatus";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':userStatus' => $userStatus]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            echo "<p class='error'>The user status already exists!</p>";
        } else {
            $sql = "INSERT INTO file_upload_limits (user_status, upload_limit, upload_limit_file, duration, price, download_speed) 
                VALUES (:userStatus, :uploadLimit, :uploadLimitFile, :duration, :price, :downloadSpeed)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':userStatus' => $userStatus,
                ':uploadLimit' => $uploadLimit,
                ':uploadLimitFile' => $uploadLimitFile,
                ':duration' => $duration,
                ':price' => $price,
                ':downloadSpeed' => $downloadSpeed
            ]);
            echo "<p class='success'>New entry successfully added!</p>";
        }
    } elseif (isset($_POST['delete_entry'])) {
        // Retrieve input value for deletion
        $userStatus = $_POST['delete_user_status'];

        $sql = "DELETE FROM file_upload_limits WHERE user_status = :userStatus";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':userStatus' => $userStatus]);

        echo "<p class='error'>Entry successfully deleted!</p>";
    } elseif (isset($_POST['update_email'])) {
        // Retrieve the new email value
        $email = $_POST['email'];

        // Update the email in the configuration table
        $sql = "UPDATE configuration SET email = :email WHERE id = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        echo "<p class='success'>Email successfully updated!</p>";
    }
}

// SQL query to retrieve data
$sql = "SELECT user_status, upload_limit, upload_limit_file, duration, price, download_speed FROM file_upload_limits";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// SQL query to retrieve the current email
$sql = "SELECT email FROM configuration WHERE id = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $config['email'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
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
            --muted-text-color: #8e9aaf;
            --border-color: #d9e2ec;
            --button-color: #56cfe1;
            --button-hover-color: #028090;
            --error-color: #e63946;
            --success-color: #56cfe1;
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

        .awasr {
            max-width: 8000px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid var(--border-color);
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: var(--secondary-color);
            color: var(--text-color);
        }

        td {
            background-color: white;
        }

        .success {
            color: var(--success-color);
            text-align: center;
            margin-bottom: 20px;
        }

        .error {
            color: var(--error-color);
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        .input-container {
            margin: 0.5rem 0;
            display: flex;
            flex-direction: column;
        }

        .input-container label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .input-container input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            background-color: white;
            color: var(--text-color);
            font-size: 1rem;
        }

        .button-container {
            margin-top: 1rem;
            text-align: center;
        }

        input[type="submit"] {
            border: none;
            background: var(--button-color);
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1rem;
        }

        input[type="submit"]:hover {
            background: var(--button-hover-color);
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
        <div class="logo">Admin Panel</div>
        <nav>
            <a href="adminpanel5.php">Statistiken</a>
            <a href="adminpanel4.php">Datei-Typen</a>
            <a href="adminpanel3.php">Benutzer-Verwaltung</a>
            <a href="adminpanel2.php">Upload-Grenze</a>
            <a href="admindelete.php">Löschen</a>
        </nav>
    </header>
    <div class="awasr">
        <h1>File Upload Limits</h1>

        <!-- Update Form -->
        <form method="post">
            <h2>Update Upload Limits</h2>
            <table>
                <thead>
                    <tr>
                        <th>User Status</th>
                        <th>Upload Limit (MB)</th>
                        <th>Upload Limit File (MB)</th>
                        <th>Duration (Days)</th>
                        <th>Download Speed (kb/s)</th>
                        <th>Price (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['user_status']) ?></td>
                            <td>
                                <input type="text" id="limit_<?= htmlspecialchars($row['user_status']) ?>" name="limit_<?= htmlspecialchars($row['user_status']) ?>" value="<?= htmlspecialchars($row['upload_limit']) ?>">
                            </td>
                            <td>
                                <input type="text" id="limit_<?= htmlspecialchars($row['user_status']) ?>_file" name="limit_<?= htmlspecialchars($row['user_status']) ?>_file" value="<?= htmlspecialchars($row['upload_limit_file']) ?>">
                            </td>
                            <td>
                                <input type="text" id="duration_<?= htmlspecialchars($row['user_status']) ?>" name="duration_<?= htmlspecialchars($row['user_status']) ?>" value="<?= htmlspecialchars($row['duration']) ?>">
                            </td>
                            <td>
                                <input type="text" id="download_speed_<?= htmlspecialchars($row['user_status']) ?>" name="download_speed_<?= htmlspecialchars($row['user_status']) ?>" value="<?= htmlspecialchars($row['download_speed']) ?>">
                            </td>
                            <td>
                                <input type="text" id="price_<?= htmlspecialchars($row['user_status']) ?>" name="price_<?= htmlspecialchars($row['user_status']) ?>" value="<?= htmlspecialchars($row['price']) ?>">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="button-container">
                <input type="submit" name="update_limits" value="Update Limits">
            </div>
        </form>

        <!-- Add Entry Form -->
        <form method="post">
            <h2>Add New Entry</h2>
            <div class="input-container">
                <label for="user_status">User Status:</label>
                <input type="text" id="user_status" name="user_status" required>
            </div>
            <div class="input-container">
                <label for="upload_limit">Upload Limit (MB):</label>
                <input type="text" id="upload_limit" name="upload_limit" required>
            </div>
            <div class="input-container">
                <label for="upload_limit_file">Upload Limit File (MB):</label>
                <input type="text" id="upload_limit_file" name="upload_limit_file" required>
            </div>
            <div class="input-container">
                <label for="duration">Duration (Days):</label>
                <input type="text" id="duration" name="duration" required>
            </div>
            <div class="input-container">
                <label for="download_speed">Download Speed (kb/s):</label>
                <input type="text" id="download_speed" name="download_speed" required>
            </div>
            <div class="input-container">
                <label for="price">Price (€):</label>
                <input type="text" id="price" name="price" required>
            </div>
            <div class="button-container">
                <input type="submit" name="add_entry" value="Add Entry">
            </div>
        </form>

        <!-- Delete Entry Form -->
        <form method="post">
            <h2>Delete Entry</h2>
            <div class="input-container">
                <label for="delete_user_status">User Status:</label>
                <input type="text" id="delete_user_status" name="delete_user_status" required>
            </div>
            <div class="button-container">
                <input type="submit" name="delete_entry" value="Delete Entry">
            </div>
        </form>

        <!-- Update Email Form -->
        <form method="post">
            <h2>Update Email</h2>
            <div class="input-container">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="button-container">
                <input type="submit" name="update_email" value="Update Email">
            </div>
        </form>
    </div>
    <footer class="footer">
        <div class="footer-links">
            <a href="index.php">Linkpage</a>
            <a href="../index.php">Home</a>
        </div>
        <p>&copy; 2024 Anonfile. All rights reserved.</p>
    </footer>
</body>
</html>
