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
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #333;
    color: #fff;
}

main {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    flex-direction: column;
}

.container {
    background-color: #444;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 1000px;
    width: 100%;
    margin-bottom: 20px;
}

h1 {
    font-size: 24px;
    margin-bottom: 20px;
}

input[type="submit"] {
    background-color: #00f; /* Standard Hintergrundfarbe für andere Buttons */
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 3px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #00c; /* Hover-Effekt für andere Buttons */
}

select {
    padding: 10px;
    border-radius: 3px;
    border: 1px solid #ccc;
    width: calc(100% - 22px);
    margin-bottom: 10px;
    background-color: #555;
    color: #fff;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #555;
}

table, th, td {
    border: 1px solid #888;
}

th, td {
    padding: 10px;
    text-align: left;
}

.message {
    color: #f88;
    font-weight: bold;
    margin-top: 10px;
}

.message.success {
    color: #8f8;
}

.checkbox {
    transform: scale(1.5);
    margin-right: 10px;
}

.center {
    text-align: center;
}

th, td {
    word-wrap: break-word;
}

/* Spezifischer Stil für den Lösch-Button */
.delete-button {
    background-color:red; /* Hintergrundfarbe für den Lösch-Button */
    color:red;
    border: none;
    padding: 10px 20px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 14px; /* Optionale Schriftgröße für bessere Sichtbarkeit */
}

.delete-button:hover {
    background-color: #c00; /* Hover-Effekt für den Lösch-Button */
}

    </style>
</head>
<body>
<main>
    <div class="container">
        <h1>Admin - Create Coupon Codes</h1>
        <form method="post">
            <select name="upload_limit_id" required>
                <option value="" disabled selected>Select Upload Limit</option>
                <?php foreach ($uploadLimits as $limit): ?>
                    <option value="<?php echo htmlspecialchars($limit['id']); ?>">
                        ID: <?php echo htmlspecialchars($limit['id']); ?> - Limit: <?php echo htmlspecialchars($limit['upload_limit']); ?> MB
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" name="generate_coupon" value="Create Coupon">
        </form>

        <div class="message">
            Active Coupons: <?php echo htmlspecialchars($activeCouponsCount); ?>
        </div>

        <?php if (isset($message)) : ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <h1>Coupon List</h1>
        <table>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Upload Limit (MB)</th>
                <th>Distributed</th>
                <th class="center">Action</th>
            </tr>
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
        </table>
    </div>
</main>
</body>
</html>
