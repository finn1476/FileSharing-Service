<?php
// Pfad zur CSV-Datei anpassen
$csvFilePath = '../Speicher/monero.csv';

// Überprüfen, ob das Formular abgesendet wurde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Neue Monero-Option und Adresse aus dem Formular lesen
    $newOption = isset($_POST['option']) ? 1 : 0; // 1, wenn die Checkbox aktiviert ist, sonst 0
    $newAddress = isset($_POST['address']) ? $_POST['address'] : '';

    // Überprüfen, ob die Datei existiert und lesbar ist
    if (file_exists($csvFilePath) && is_readable($csvFilePath)) {
        // Die CSV-Datei öffnen
        $csvFile = fopen($csvFilePath, 'w');

        // Überprüfen, ob das Öffnen erfolgreich war
        if ($csvFile) {
            // Die Monero-Option und Adresse in die CSV schreiben
            fputcsv($csvFile, array($newOption, $newAddress));
            fclose($csvFile);
            $message = "Monero-Einstellungen erfolgreich aktualisiert.";
        } else {
            // Fehlermeldung, wenn die CSV-Datei nicht geöffnet werden konnte
            $message = "Fehler beim Öffnen der CSV-Datei zum Schreiben.";
        }
    } else {
        // Fehlermeldung, wenn die CSV-Datei nicht existiert oder nicht lesbar ist
        $message = "Die CSV-Datei existiert nicht oder ist nicht lesbar.";
    }
}

// Lesen der aktuellen Monero-Einstellungen aus der CSV-Datei
if (file_exists($csvFilePath) && is_readable($csvFilePath)) {
    // Die CSV-Datei öffnen
    $csvFile = fopen($csvFilePath, 'r');

    // Überprüfen, ob das Öffnen erfolgreich war
    if ($csvFile) {
        // Die erste Zeile der CSV-Datei lesen (enthält Option und Adresse)
        $firstLine = fgetcsv($csvFile);

        // Die Monero-Option und Adresse aus der ersten Zeile extrahieren
        $moneroOption = isset($firstLine[0]) ? $firstLine[0] : '';
        $moneroAddress = isset($firstLine[1]) ? $firstLine[1] : '';

        // CSV-Datei schließen
        fclose($csvFile);
    } else {
        // Fehlermeldung, wenn die CSV-Datei nicht geöffnet werden konnte
        $message = "Fehler beim Öffnen der CSV-Datei zum Lesen.";
    }
} else {
    // Fehlermeldung, wenn die CSV-Datei nicht existiert oder nicht lesbar ist
    $message = "Die CSV-Datei existiert nicht oder ist nicht lesbar.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminseite - Monero-Einstellungen</title>
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

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        input[type="checkbox"] {
            margin: 0 10px;
        }

        input[type="text"] {
            width: 100%;
            max-width: 400px;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 15px;
            font-size: 16px;
        }

        input[type="submit"] {
            cursor: pointer;
            background-color: var(--button-color);
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            border: none;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
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

    <div class="container">
        <h1>Adminseite - Monero-Einstellungen</h1>
        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
        <form method="post">
            <label for="option">Monero-Spendenoption:</label>
            <input type="checkbox" id="option" name="option" <?php if ($moneroOption == 1) echo "checked"; ?>>
            <label for="option">Aktivieren</label><br>
            <label for="address">Monero-Wallet-Adresse:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($moneroAddress); ?>"><br>
            <input type="submit" value="Einstellungen speichern">
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
