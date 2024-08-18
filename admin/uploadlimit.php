<?php
// Include config.php
include 'config.php';

// If the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_limits'])) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'limit_') === 0) {
                $userStatus = str_replace(['limit_', '_file', '_duration', '_price'], '', $key);
                $uploadLimit = $_POST['limit_' . $userStatus];
                $uploadLimitFile = $_POST['limit_' . $userStatus . '_file'];
                $duration = $_POST['duration_' . $userStatus];
                $price = $_POST['price_' . $userStatus];

                $sql = "UPDATE file_upload_limits 
                        SET upload_limit = :uploadLimit, upload_limit_file = :uploadLimitFile, duration = :duration, price = :price 
                        WHERE user_status = :userStatus";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':uploadLimit' => $uploadLimit,
                    ':uploadLimitFile' => $uploadLimitFile,
                    ':duration' => $duration,
                    ':price' => $price,
                    ':userStatus' => $userStatus
                ]);
            }
        }

        echo "<p style='color: green;'>Upload limits successfully updated!</p>";
    } elseif (isset($_POST['add_entry'])) {
        // Retrieve input values from the form
        $userStatus = $_POST['user_status'];
        $uploadLimit = $_POST['upload_limit'];
        $uploadLimitFile = $_POST['upload_limit_file'];
        $duration = $_POST['duration'];
        $price = $_POST['price'];

        // Check if the user status already exists
        $sql = "SELECT COUNT(*) FROM file_upload_limits WHERE user_status = :userStatus";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':userStatus' => $userStatus]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            echo "<p style='color: red;'>The user status already exists!</p>";
        } else {
            $sql = "INSERT INTO file_upload_limits (user_status, upload_limit, upload_limit_file, duration, price) 
                    VALUES (:userStatus, :uploadLimit, :uploadLimitFile, :duration, :price)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':userStatus' => $userStatus,
                ':uploadLimit' => $uploadLimit,
                ':uploadLimitFile' => $uploadLimitFile,
                ':duration' => $duration,
                ':price' => $price
            ]);
            echo "<p style='color: green;'>New entry successfully added!</p>";
        }
    } elseif (isset($_POST['delete_entry'])) {
        // Retrieve input value for deletion
        $userStatus = $_POST['delete_user_status'];

        $sql = "DELETE FROM file_upload_limits WHERE user_status = :userStatus";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':userStatus' => $userStatus]);

        echo "<p style='color: red;'>Entry successfully deleted!</p>";
    } elseif (isset($_POST['update_email'])) {
        // Retrieve the new email value
        $email = $_POST['email'];

        // Update the email in the configuration table
        $sql = "UPDATE configuration SET email = :email WHERE id = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);

        echo "<p style='color: green;'>Email successfully updated!</p>";
    }
}

// SQL query to retrieve data
$sql = "SELECT user_status, upload_limit, upload_limit_file, duration, price FROM file_upload_limits";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// SQL query to retrieve the current email
$sql = "SELECT email FROM configuration WHERE id = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$config = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $config['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload Limits</title>
    
    <style>
        html, main {
            overflow-x: hidden;
        }

        html {
            font-family: monospace;
            background: black;
            color: white;
        }

        table {
            border-collapse: collapse;
            margin: 20px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 0.5rem;
            font-size: 1rem;
            text-align: left;
        }

        th {
            background-color: #084cdf;
            color: white;
        }

        .awasr {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding-top: 1.6%;
        }

        .input-container {
            margin: 1rem;
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 500px;
        }

        .input-container label {
            flex: 1;
            margin-right: 1rem;
            font-size: 1rem;
        }

        .input-container input[type="number"], .input-container input[type="text"], .input-container input[type="email"] {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #333;
            color: white;
            font-size: 1rem;
        }

        .button-container {
            margin-top: 2rem;
            display: flex;
            justify-content: center;
        }

        input[type="submit"] {
            margin-right: 20px;
            border: none;
            background: #4e595d;
            padding: 10px 20px;
            border-radius: 10px;
            color: #fff;
            cursor: pointer;
            transition: background .2s ease-in-out;
            font-size: 1rem;
        }

        input[type="submit"]:hover {
            background: #6b787d;
        }

        .floadtobx {
            display: flex;
            justify-content: center;
        }
		.homelink{
			text-decoration:none;
			color:white;
			background:grey;
			padding:1rem;
			border-radius:0.5rem;
		}
		.homelink:hover{
			background:lightgrey;
		}
    </style>
</head>
<body>
<main>
<div class="awasr">
    <h1>File Upload Limits</h1>

    <table>
        <thead>
            <tr>
                <th>User Status</th>
                <th>Current Upload Limit (MB)</th>
                <th>Current File Limit (MB)</th>
                <th>Duration (Days)</th>
                <th>Price (€)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['upload_limit']); ?> MB</td>
                    <td><?php echo htmlspecialchars($row['upload_limit_file']); ?> MB</td>
                    <td><?php echo htmlspecialchars($row['duration']); ?> Days</td>
                    <td><?php echo htmlspecialchars($row['price']); ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<div class="floadtobx">
    <form method="post">
        <?php foreach ($results as $row): ?>
        <div class="input-container">
            <label for="limit_<?php echo htmlspecialchars($row['user_status']); ?>">
                <?php echo htmlspecialchars($row['user_status']); ?> (Total Limit, MB):
            </label>
            <input type="number" id="limit_<?php echo htmlspecialchars($row['user_status']); ?>" 
                   name="limit_<?php echo htmlspecialchars($row['user_status']); ?>" 
                   value="<?php echo htmlspecialchars($row['upload_limit']); ?>" required>
        </div>
        <div class="input-container">
            <label for="limit_<?php echo htmlspecialchars($row['user_status']); ?>_file">
                <?php echo htmlspecialchars($row['user_status']); ?> (File Limit, MB):
            </label>
            <input type="number" id="limit_<?php echo htmlspecialchars($row['user_status']); ?>_file" 
                   name="limit_<?php echo htmlspecialchars($row['user_status']); ?>_file" 
                   value="<?php echo htmlspecialchars($row['upload_limit_file']); ?>" required>
        </div>
        <div class="input-container">
            <label for="duration_<?php echo htmlspecialchars($row['user_status']); ?>">
                <?php echo htmlspecialchars($row['user_status']); ?> (Duration, Days):
            </label>
            <input type="number" id="duration_<?php echo htmlspecialchars($row['user_status']); ?>" 
                   name="duration_<?php echo htmlspecialchars($row['user_status']); ?>" 
                   value="<?php echo htmlspecialchars($row['duration']); ?>" >
        </div>
        <div class="input-container">
            <label for="price_<?php echo htmlspecialchars($row['user_status']); ?>">
                <?php echo htmlspecialchars($row['user_status']); ?> (Price, €):
            </label>
            <input type="number" step="0.01" id="price_<?php echo htmlspecialchars($row['user_status']); ?>" 
                   name="price_<?php echo htmlspecialchars($row['user_status']); ?>" 
                   value="<?php echo htmlspecialchars($row['price']); ?>" required>
        </div>
        <hr>
        <?php endforeach; ?>
        <div class="button-container">
            <input type="submit" name="update_limits" value="Update Upload Limits">
        </div>
    </form>

    <form method="post">
        <div class="input-container">
            <label for="user_status">User Status:</label>
            <input type="text" id="user_status" name="user_status" required>
        </div>
        <div class="input-container">
            <label for="upload_limit">Total Limit (MB):</label>
            <input type="number" id="upload_limit" name="upload_limit" required>
        </div>
        <div class="input-container">
            <label for="upload_limit_file">File Limit (MB):</label>
            <input type="number" id="upload_limit_file" name="upload_limit_file" required>
        </div>
        <div class="input-container">
            <label for="duration">Duration (Days):</label>
            <input type="number" id="duration" name="duration" >
        </div>
        <div class="input-container">
            <label for="price">Price (€):</label>
            <input type="number" step="0.01" id="price" name="price" required>
        </div>
        <div class="button-container">
            <input type="submit" name="add_entry" value="Add Entry">
        </div>
    </form>

    <form method="post">
        <div class="input-container">
            <label for="delete_user_status">User Status to Delete:</label>
            <input type="text" id="delete_user_status" name="delete_user_status" required>
        </div>
        <div class="button-container">
            <input type="submit" name="delete_entry" value="Delete Entry">
        </div>
    </form>
    <form method="post">
        <div class="input-container">
            <label for="email">Update Email Address:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="button-container">
            <input type="submit" name="update_email" value="Update Email">
        </div>
    </form>
    </div>
</div>
</main>
<footer class="footera">
<center><a class="homelink" href="index.php">HOME</a></center>
</footer>
</body>
</html>
