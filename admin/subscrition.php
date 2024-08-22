<?php

// Inkludiere die Datenbankkonfiguration
include('config.php');

// Initialisiere die Variablen
$expiredSubscriptions = [];
$activeSubscriptions = [];

// Abgelaufene Subscriptions abrufen
try {
    $sql = "SELECT u.id, u.username, u.upload_limit_expiration_date, f.upload_limit
            FROM users u
            JOIN file_upload_limits f ON u.file_upload_limit_id = f.id
            WHERE u.upload_limit_expiration_date IS NOT NULL
            AND u.upload_limit_expiration_date < CURDATE()";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $expiredSubscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Fehler beim Abrufen der abgelaufenen Subscriptions: " . $e->getMessage();
}

// Aktive Subscriptions abrufen (ohne file_upload_limit_id 2)
try {
    $sqlActive = "SELECT u.id, u.username, u.upload_limit_expiration_date, f.upload_limit
                  FROM users u
                  JOIN file_upload_limits f ON u.file_upload_limit_id = f.id
                  WHERE u.upload_limit_expiration_date IS NOT NULL
                  AND u.upload_limit_expiration_date >= CURDATE()
                  AND u.file_upload_limit_id != 2";
    $stmtActive = $pdo->prepare($sqlActive);
    $stmtActive->execute();
    $activeSubscriptions = $stmtActive->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Fehler beim Abrufen der aktiven Subscriptions: " . $e->getMessage();
}

// Abgelaufene Subscriptions löschen
if (isset($_POST['delete_expired'])) {
    try {
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
    } catch (PDOException $e) {
        echo "Fehler beim Löschen der abgelaufenen Subscriptions: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>Admin - Remove Expired Subscriptions</title>
    <style>
      :root {
        --primary-color: #005f73;
        --secondary-color: #94d2bd;
        --accent-color: #ee9b00;
        --background-color: #f7f9fb;
        --text-color: #023047;
        --border-color: #d9e2ec;
        --button-color: #56cfe1;
        --button-hover-color: #028090;
        --success-color: #2e7d32;
        --message-error: #e63946;
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
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      }

      h1, h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 20px;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
      }

      table th, table td {
        border: 1px solid var(--border-color);
        padding: 12px;
        text-align: left;
      }

      table th {
        background-color: var(--secondary-color);
        color: #ffffff;
      }

      table tr:nth-child(even) {
        background-color: #f0f4f4;
      }

      .delete-button {
        background-color: var(--button-color);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 16px;
      }

      .delete-button:hover {
        background-color: var(--button-hover-color);
      }

      .message {
        color: var(--success-color);
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
      }

      .message-error {
        color: var(--message-error);
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
      }

      .back {
        display: block;
        text-align: center;
        text-decoration: none;
        background-color: var(--primary-color);
        color: #ffffff;
        padding: 10px 20px;
        border-radius: 5px;
        margin: 20px auto;
        width: fit-content;
        font-size: 16px;
        transition: background-color 0.3s ease;
      }

      .back:hover {
        background-color: var(--accent-color);
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

      @media (max-width: 600px) {
        nav {
          flex-direction: column;
          gap: 10px;
        }

        .footer-links {
          flex-direction: column;
          gap: 10px;
        }
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
            <h1>Remove Expired Subscriptions</h1>

            <?php if (isset($successMessage)) : ?>
                <div class="message"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php endif; ?>

            <!-- Abgelaufene Subscriptions -->
            <h2>Expired Subscriptions</h2>
            <?php if (count($expiredSubscriptions) > 0): ?>
                <table>
                    <tr>
                        <th>Username</th>
                        <th>Upload Limit</th>
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
                    <input type="submit" name="delete_expired" value="Remove All Expired Subscriptions" class="delete-button">
                </form>
            <?php else: ?>
                <p>There are no expired subscriptions.</p>
            <?php endif; ?>

            <!-- Aktive Subscriptions -->
            <h2>Active Subscriptions</h2>
            <?php if (count($activeSubscriptions) > 0): ?>
                <table>
                    <tr>
                        <th>Username</th>
                        <th>Upload Limit</th>
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

            <a class="back" href="index.php">Home</a>
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
