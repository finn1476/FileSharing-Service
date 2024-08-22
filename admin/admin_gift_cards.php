<?php
session_start();
include('config.php');

// Function to generate a random coupon code
function generateRandomCode($length = 50) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, strlen($characters) - 1)];
    }
    return $code;
}

// Create a coupon code when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_coupon'])) {
    $uploadLimitId = $_POST['upload_limit_id'];
    $couponCode = generateRandomCode();

    // Check if the coupon code already exists
    $sql = "SELECT COUNT(*) FROM coupons WHERE code = :code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['code' => $couponCode]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $message = "Coupon code already exists.";
    } else {
        // Insert the new coupon code into the database
        $sql = "INSERT INTO coupons (code, file_upload_limit_id) VALUES (:code, :upload_limit_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['code' => $couponCode, 'upload_limit_id' => $uploadLimitId]);
        $message = "Coupon code successfully created: $couponCode";
    }
}

// Update the distribution status when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_coupon_status'])) {
    $couponId = $_POST['coupon_id'];
    $distributed = isset($_POST['distributed']) ? 1 : 0;

    // Update the distribution status
    $sql = "UPDATE coupons SET distributed = :distributed WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['distributed' => $distributed, 'id' => $couponId]);
}

// Delete a coupon when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_coupon'])) {
    $couponId = $_POST['coupon_id'];

    // Check if the coupon is distributed before deleting
    $sql = "SELECT distributed FROM coupons WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $couponId]);
    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon && $coupon['distributed'] == 0) {
        // Delete the coupon from the database if it is not distributed
        $sql = "DELETE FROM coupons WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $couponId]);
        $message = "Coupon code successfully deleted.";
    } else {
        $message = "Cannot delete a distributed coupon.";
    }
}

// Get all upload limits from the database for the dropdown menu
$sql = "SELECT id, upload_limit FROM file_upload_limits";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$uploadLimits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count the number of active coupons
$sql = "SELECT COUNT(*) FROM coupons";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$activeCouponsCount = $stmt->fetchColumn();

// Get all existing coupons from the database, including upload limits
$sql = "
    SELECT c.id, c.code, c.distributed, f.upload_limit 
    FROM coupons c
    JOIN file_upload_limits f ON c.file_upload_limit_id = f.id
";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin - Coupon Codes</title>
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

        .container {
            max-width: 800px;
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

        .input-container input, select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid var(--border-color);
            background-color: white;
            color: var(--text-color);
            font-size: 1rem;
            width: 100%;
            box-sizing: border-box;
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

        .checkbox {
            width: 20px;
            height: 20px;
        }

        .center {
            text-align: center;
        }

        .delete-button {
            background: var(--error-color);
        }

        .delete-button:hover {
            background: #d62839;
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
    <div class="container">
        <h1>Create Coupon Codes</h1>
        <form method="post">
            <div class="input-container">
                <label for="upload_limit_id">Select Upload Limit</label>
                <select id="upload_limit_id" name="upload_limit_id" required>
                    <option value="" disabled selected>Select Upload Limit</option>
                    <?php foreach ($uploadLimits as $limit): ?>
                        <option value="<?php echo htmlspecialchars($limit['id']); ?>">
                            ID: <?php echo htmlspecialchars($limit['id']); ?> - Limit: <?php echo htmlspecialchars($limit['upload_limit']); ?> MB
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="button-container">
                <input type="submit" name="generate_coupon" value="Create Coupon">
            </div>
        </form>

        <div class="message">
            Active Coupons: <?php echo htmlspecialchars($activeCouponsCount); ?>
        </div>

        <?php if (isset($message)) : ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <h1>Coupon List</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Upload Limit (MB)</th>
                    <th>Distributed</th>
                    <th class="center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($coupon['id']); ?></td>
                        <td><?php echo htmlspecialchars($coupon['code']); ?></td>
                        <td><?php echo htmlspecialchars($coupon['upload_limit']); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="coupon_id" value="<?php echo htmlspecialchars($coupon['id']); ?>">
                                <input type="checkbox" name="distributed" class="checkbox" <?php echo $coupon['distributed'] ? 'checked' : ''; ?>>
                        </td>
                        <td class="center">
                                <input type="submit" name="update_coupon_status" value="Save">
                            </form>
                            <?php if (!$coupon['distributed']): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="coupon_id" value="<?php echo htmlspecialchars($coupon['id']); ?>">
                                    <input type="submit" name="delete_coupon" value="Delete" class="delete-button" onclick="return confirm('Are you sure you want to delete this coupon?');">
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<footer>
    <div class="footer-links">
        <a href="index.php">Linkpage</a>
        <a href="../index.php">Home</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
</footer>
</body>
</html>
