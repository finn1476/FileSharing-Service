<?php

// Inkludiere die Datenbankkonfiguration
include('config.php');

// Abgelaufene Subscriptions abrufen
$sql = "SELECT u.id, u.username, u.upload_limit_expiration_date, f.upload_limit
        FROM users u
        JOIN file_upload_limits f ON u.file_upload_limit_id = f.id
        WHERE u.upload_limit_expiration_date IS NOT NULL
        AND u.upload_limit_expiration_date < CURDATE()";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$expiredSubscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Aktive Subscriptions abrufen (ohne file_upload_limit_id 2)
$sqlActive = "SELECT u.id, u.username, u.upload_limit_expiration_date, f.upload_limit
              FROM users u
              JOIN file_upload_limits f ON u.file_upload_limit_id = f.id
              WHERE u.upload_limit_expiration_date IS NOT NULL
              AND u.upload_limit_expiration_date >= CURDATE()
              AND u.file_upload_limit_id != 2";
$stmtActive = $pdo->prepare($sqlActive);
$stmtActive->execute();
$activeSubscriptions = $stmtActive->fetchAll(PDO::FETCH_ASSOC);

// Abgelaufene Subscriptions löschen
if (isset($_POST['delete_expired'])) {
    $sql = "UPDATE users 
            SET file_upload_limit_id = 2, upload_limit_expiration_date = NULL 
            WHERE upload_limit_expiration_date < CURDATE()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Bestätigungsmeldung setzen
    $successMessage = "Alle abgelaufenen Subscriptions wurden erfolgreich entfernt.";

    // Seite neu laden, um aktualisierte Daten anzuzeigen
    header("Location: subscrition.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Admin - Remove expired subscriptions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #333;
            color: #fff;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #444;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #555;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #555;
        }

        table tr:nth-child(even) {
            background-color: #666;
        }

        .delete-button {
            background-color: #f00;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
            display: block;
            width: 100%;
            margin: 0 auto;
        }

        .delete-button:hover {
            background-color: #c00;
        }

        .message {
            color: #8f8;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .back {
            text-decoration: none;
            background-color: blue;
            color: white;
            padding: 0.5rem;
            border-radius: 0.25rem;
            display: block;
            width: fit-content;
            margin: auto;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Remove expired subscriptions</h1>

    <?php if (isset($successMessage)) : ?>
        <div class="message"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <!-- Abgelaufene Subscriptions -->
    <h2>Expired Subscriptions</h2>
    <?php if (count($expiredSubscriptions) > 0): ?>
        <table>
            <tr>
                <th>Username</th>
                <th>Upload-Limit</th>
                <th>Date of Expiry</th>
            </tr>
            <?php foreach ($expiredSubscriptions as $subscription): ?>
                <tr>
                    <td><?php echo htmlspecialchars($subscription['username']); ?></td>
                    <td><?php echo htmlspecialchars($subscription['upload_limit']) . ' MB'; ?></td>
                    <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($subscription['upload_limit_expiration_date']))); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <form method="post">
            <input type="submit" name="delete_expired" value="Remove all expired subscriptions" class="delete-button">
        </form>
    <?php else: ?>
        <p>There are no expired subscriptions.</p>
    <?php endif; ?>

    <!-- Aktive Subscriptions -->
    <h2>Active Subscriptions </h2>
    <?php if (count($activeSubscriptions) > 0): ?>
        <table>
            <tr>
                <th>Username</th>
                <th>Upload-Limit</th>
                <th>Date of Expiry</th>
            </tr>
            <?php foreach ($activeSubscriptions as $subscription): ?>
                <tr>
                    <td><?php echo htmlspecialchars($subscription['username']); ?></td>
                    <td><?php echo htmlspecialchars($subscription['upload_limit']) . ' MB'; ?></td>
                    <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($subscription['upload_limit_expiration_date']))); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>There are no active subscriptions.</p>
    <?php endif; ?>

    <a class="back" href="index.php">HOME</a>
</div>
</body>
</html>
