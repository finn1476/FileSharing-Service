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
            echo "Monero-Einstellungen erfolgreich aktualisiert.";
        } else {
            // Fehlermeldung, wenn die CSV-Datei nicht geöffnet werden konnte
            echo "Fehler beim Öffnen der CSV-Datei zum Schreiben.";
        }
    } else {
        // Fehlermeldung, wenn die CSV-Datei nicht existiert oder nicht lesbar ist
        echo "Die CSV-Datei existiert nicht oder ist nicht lesbar.";
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
        echo "Fehler beim Öffnen der CSV-Datei zum Lesen.";
    }
} else {
    // Fehlermeldung, wenn die CSV-Datei nicht existiert oder nicht lesbar ist
    echo "Die CSV-Datei existiert nicht oder ist nicht lesbar.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminseite - Monero-Einstellungen</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* Ihr CSS-Stil hier */
    </style>
</head>
<body>
<center>
    <div class="container">
        <h1>Adminseite - Monero-Einstellungen</h1>
        <form method="post">
            <label for="option">Monero-Spendenoption:</label>
            <input type="checkbox" id="option" name="option" <?php if ($moneroOption == 1) echo "checked"; ?>>
            <label for="option">Aktivieren</label><br>
            <label for="address">Monero-Wallet-Adresse:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($moneroAddress); ?>"><br>
            <input type="submit" value="Einstellungen speichern">
        </form>
    </div>
    </center>
  <footer class="footera">
        <div>
            <h1 class="right"><a class="bauttona" href="adminpanel5.php">Statistiken</a></h1>
        </div>
        <div>
            <h1 class="right"><a class="bauttona" href="adminpanel4.php">Datei-Typen</a></h1>
        </div>
        <div>
            <h1 class="right"><a class="bauttona" href="adminpanel3.php">Benutzer-Verwaltung</a></h1>
        </div>
        <div>
            <h1 class="right"><a class="bauttona" href="adminpanel2.php">Upload-Grenze</a></h1>
        </div>
        <div>
            <h1><a class="bauttona" href="admindelete.php">Löschen</a></h1>
        </div>
    </footer>
</body>
</html>
