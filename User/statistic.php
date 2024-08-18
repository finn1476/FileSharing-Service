<?php
session_start();

// Inkludiere die Datenbankkonfiguration
include('config.php');

if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit;
}

// Benutzer-ID ermitteln
$username = $_SESSION['username'];
$sql = "SELECT id FROM users WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username]);
$userId = $stmt->fetchColumn();

if (!$userId) {
    die("Benutzer nicht gefunden.");
}

// Gesamtgröße der hochgeladenen Dateien berechnen
$sql = "SELECT SUM(file_size) AS total_size FROM uploads WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $userId]);
$totalSize = $stmt->fetchColumn();

$totalSizeMB = $totalSize ? $totalSize / 1048576 : 0; // In MB umwandeln und sicherstellen, dass der Wert nicht null ist

// Upload-Limit des Benutzers ermitteln
$sql = "SELECT file_upload_limit_id FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $userId]);
$fileUploadLimitId = $stmt->fetchColumn();

if (!$fileUploadLimitId) {
    die("Upload-Limit ID nicht gefunden.");
}

// Upload-Limit des Benutzers ermitteln (in MB)
$sql = "SELECT upload_limit FROM file_upload_limits WHERE id = :limit_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['limit_id' => $fileUploadLimitId]);
$uploadLimitMB = $stmt->fetchColumn();

if ($uploadLimitMB === false) {
    $uploadLimitMB = 0; // Falls kein Limit gefunden wird
}

// Anzahl der Fehlversuche beim Einlösen des Gutscheins abfragen
$sql = "SELECT failed_attempts FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $userId]);
$failedAttempts = $stmt->fetchColumn();

if ($failedAttempts === false) {
    $failedAttempts = 0; // Falls keine Fehlversuche vorhanden sind
}

// Gutschein-Verarbeitung
if (isset($_POST['coupon_code'])) {
    // Gutschein-Code bereinigen und validieren (nur alphanumerische Zeichen erlauben)
    $couponCode = preg_replace("/[^a-zA-Z0-9]/", "", $_POST['coupon_code']);

    // Zusätzlich XSS-Schutz durch htmlspecialchars
    $couponCode = htmlspecialchars($couponCode, ENT_QUOTES, 'UTF-8');

    // Gutschein überprüfen
    $sql = "SELECT file_upload_limit_id FROM coupons WHERE code = :code";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['code' => $couponCode]);
    $newLimitId = $stmt->fetchColumn();

    if ($newLimitId) {
        // Update der file_upload_limit_id in der users Tabelle
        $sql = "UPDATE users SET file_upload_limit_id = :new_limit_id WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['new_limit_id' => $newLimitId, 'user_id' => $userId]);

        // Gutschein aus der Datenbank löschen
        $sql = "DELETE FROM coupons WHERE code = :code";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['code' => $couponCode]);

        // Dauer des neuen Limits ermitteln
        $sql = "SELECT duration FROM file_upload_limits WHERE id = :limit_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['limit_id' => $newLimitId]);
        $duration = $stmt->fetchColumn();

        // Wenn eine Dauer festgelegt ist, Ablaufdatum berechnen und speichern
        if ($duration > 0) {
            $expirationDate = date('Y-m-d', strtotime("+$duration days"));
            $sql = "UPDATE users SET upload_limit_expiration_date = :expiration_date WHERE id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['expiration_date' => $expirationDate, 'user_id' => $userId]);
        } else {
            // Ablaufdatum auf NULL setzen, falls keine Dauer vorhanden ist
            $sql = "UPDATE users SET upload_limit_expiration_date = NULL WHERE id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
        }

        $successMessage = "Gutschein erfolgreich eingelöst!";
        
        // Erneut das aktuelle Limit ermitteln
        $sql = "SELECT upload_limit FROM file_upload_limits WHERE id = :limit_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['limit_id' => $newLimitId]);
        $uploadLimitMB = $stmt->fetchColumn();

        // Fehlversuche zurücksetzen
        $sql = "UPDATE users SET failed_attempts = 0 WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
    } else {
        // Fehlversuch erhöhen
        $failedAttempts++;
        $sql = "UPDATE users SET failed_attempts = :failed_attempts WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['failed_attempts' => $failedAttempts, 'user_id' => $userId]);

        $errorMessage = "Ungültiger Gutschein-Code.";

        // Abmelden, wenn 3 fehlgeschlagene Versuche
        if ($failedAttempts >= 3) {
            session_unset();
            session_destroy();
            header("Location: index.html");
            exit;
        }
    }
}



// Hole alle Upload-Limits aus der Datenbank für das Dropdown-Menü
$sql = "SELECT id, upload_limit FROM file_upload_limits";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$uploadLimits = $stmt->fetchAll(PDO::FETCH_ASSOC);
$sql = "SELECT upload_limit_expiration_date FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $userId]);
$expirationDate = $stmt->fetchColumn();

if ($expirationDate === false || $expirationDate === NULL) {
    $expirationDate = "No subscription";
} else {
    $expirationDate = date('d.m.Y', strtotime($expirationDate)); // Formatieren des Datums
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Geschützter Bereich</title>
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
        }

        .container {
            background-color: #444;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .logout {
            margin-top: 20px;
        }

        .logout a {
            color: #f88;
            text-decoration: none;
        }

        .logout a:hover {
            text-decoration: underline;
        }

        .delete-button {
            background-color: #f00;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            margin-top: 10px;
        }

        .delete-button:hover {
            background-color: #c00;
        }

        /* Canvas Styles */
        .chart-container {
            margin: 20px auto;
            max-width: 600px;
            position: relative;
        }

        canvas {
            background-color: #555;
            border-radius: 5px;
        }

        /* Gutschein-Formular */
        .coupon-form {
            margin-top: 20px;
        }

        .coupon-form input[type="text"] {
            padding: 10px;
            border-radius: 3px;
            border: 1px solid #ccc;
            width: calc(100% - 22px);
            margin-bottom: 10px;
        }

        .coupon-form input[type="submit"] {
            background-color: #00f;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 3px;
            cursor: pointer;
        }

        .coupon-form input[type="submit"]:hover {
            background-color: #00c;
        }

        .message {
            color: #f88;
            font-weight: bold;
            margin-top: 10px;
        }

        .message.success {
            color: #8f8;
        }
		.back{
			text-decoration:none;
			background-color:blue;
			color:white;
			padding:0.5rem;
			border-radius:0.25rem;
		}
    </style>
</head>
<body>
<main>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <h2>Your Total Storage Usage: <?php echo number_format($totalSizeMB, 2); ?> MB</h2>
        <h2>Your Maximum Storage Limit: <?php echo number_format($uploadLimitMB, 2); ?> MB</h2>
		 <h2>Your subscription expires on: <?php echo $expirationDate; ?></h2> <!-- Ablaufdatum anzeigen -->

        <div class="chart-container">
            <canvas id="storageChart" width="400" height="200"></canvas>
        </div>

<form method="post" class="coupon-form">
    <input type="text" name="coupon_code" placeholder="Enter coupon code" required>
    <input type="submit" value="Redeem Coupon"><br><br>
    <a class="back" href="index.php">Back</a>
</form>


        <?php if (isset($successMessage)) : ?>
            <div class="message success"><?php echo htmlspecialchars($successMessage); ?></div>
        <?php elseif (isset($errorMessage)) : ?>
            <div class="message"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>


    </div>
</main>
<script>
    // JavaScript to draw the storage usage chart
    var canvas = document.getElementById('storageChart');
    var ctx = canvas.getContext('2d');

    var totalSizeMB = <?php echo json_encode($totalSizeMB); ?>;
    var uploadLimitMB = <?php echo json_encode($uploadLimitMB); ?>;

    var used = totalSizeMB;
    var remaining = uploadLimitMB - totalSizeMB;

    // Draw the used portion in red
    ctx.fillStyle = '#f00'; // Red
    ctx.fillRect(0, 0, (used / uploadLimitMB) * canvas.width, canvas.height);

    // Draw the remaining portion in green
    ctx.fillStyle = '#0f0'; // Green
    ctx.fillRect((used / uploadLimitMB) * canvas.width, 0, (remaining / uploadLimitMB) * canvas.width, canvas.height);

    // Draw text in black
    ctx.fillStyle = '#000'; // Black
    ctx.font = '16px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('Used: ' + used.toFixed(2) + ' MB', canvas.width / 4, canvas.height / 2);
    ctx.fillText('Remaining: ' + remaining.toFixed(2) + ' MB', (canvas.width / 4) * 3, canvas.height / 2);
</script>

</body>
</html>
