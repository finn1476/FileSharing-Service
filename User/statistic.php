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

      .maske {
        margin-bottom: 20px;
      }

      .abusetextwidth {
        text-align: left;
        margin-bottom: 20px;
      }

      .emailbutton {
        display: inline-block;
        padding: 10px 20px;
        text-decoration: none;
        background-color: var(--button-color);
        color: white;
        border-radius: 5px;
        margin: 5px;
        transition: background-color 0.3s ease;
      }

      .emailbutton:hover {
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
	    input[type="text"],
        input[type="password"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: var(--button-color);
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: var(--button-hover-color);
        }

        .hidden {
            display: none;
        }

        .deactivated-message {
            background-color: var(--error-color);
            color: white;
            padding: 20px;
            border-radius: 5px;
        }
		      .container {
        background-color: white;
        border: 1px solid var(--border-color);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        margin: 0 auto;
      }
	  .back{
		  width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            box-sizing: border-box;
			            background-color: var(--button-color);
            color: white;
            cursor: pointer;
			 text-decoration: none;
	  }
	  	  .back:hover{
		 
            border: 1px solid var(--border-color);
			background-color: var(--button-hover-color);
            color: white;
            cursor: pointer;
			text-decoration: none;
	  }
    </style>
</head>
<body>
<header>
 <div class="logo">Anonfile</div>
<?php

include("header.php");	
	
?>
   

</header>
<main>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <h2>Your Total Storage Usage: <?php echo number_format($totalSizeMB, 2); ?> MB</h2>
        <h2>Your Maximum Storage Limit: <?php echo number_format($uploadLimitMB, 2); ?> MB</h2>
        <h2>Your subscription expires on: <?php echo $expirationDate; ?></h2>
<center>
        <div class="chart-container">
            <canvas id="storageChart" width="400" height="200"></canvas>
        </div>
</center>
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
<footer class="footer">
    <div class="footer-links">
      <a href="../FAQ.php">FAQ</a>
      <a href="../impressum.php">Imprint</a>
      <a href="../abuse.php">Abuse</a>
      <a href="../terms.php">ToS</a>
      <a href="../datenschutz.php">Privacy Policy</a>
    </div>
    <p>&copy; 2024 Anonfile. All rights reserved.</p>
</footer>
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
