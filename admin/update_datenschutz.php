<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin - Datenschutzerklärung bearbeiten</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        /* Styles aus der zweiten Datei hier einfügen */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        main {
            text-align: center;
            padding: 10rem;
        }

        .awasr {
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 600px;
            width: 100%;
            border-right: 1px solid #ccc; /* Neuer Rand auf der rechten Seite hinzugefügt */
        }

        h2 {
            margin-bottom: 20px;
        }

        .impressum {
            text-align: left;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <main>
        <?php
    // Überprüfen, ob das Formular gesendet wurde
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Daten aus dem Formular erhalten
        $kontaktdaten = $_POST["kontaktdaten"];
        $speicherung_deutsch = $_POST["speicherung_deutsch"];
        $speicherung_englisch = $_POST["speicherung_englisch"];

        // Daten in CSV-Format konvertieren
        $data = array($kontaktdaten, $speicherung_deutsch, $speicherung_englisch);
        $dataLine = implode(",", $data) . PHP_EOL;

        // Pfad zur CSV-Datei
        $csvFilePath = "../Speicher/datenschutzerklaerung.csv";

        // CSV-Datei öffnen oder erstellen
        $csvFile = fopen($csvFilePath, "w");

        // Daten in die CSV-Datei schreiben
        fwrite($csvFile, $dataLine);

        // CSV-Datei schließen
        fclose($csvFile);

        echo "<p>Datenschutzerklärung erfolgreich aktualisiert.</p>";
    }
    ?>
        <div class="awasr">
            <div>
                <h2>Datenschutzerklärung bearbeiten</h2>
            </div>
            <?php
            // Aktuelle Werte aus der CSV-Datei lesen
            $csvFile = fopen('../Speicher/datenschutzerklaerung.csv', 'r');
            if ($csvFile !== FALSE) {
                $data = fgetcsv($csvFile); // Erste Zeile lesen
                fclose($csvFile);
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="kontaktdaten">Kontaktdaten:</label><br>
                <textarea id="kontaktdaten" name="kontaktdaten" rows="4" cols="50"><?php echo isset($data[0]) ? $data[0] : ''; ?></textarea><br>

                <label for="speicherung_deutsch">Welche Daten speichern wir? (Deutsch):</label><br>
                <textarea id="speicherung_deutsch" name="speicherung_deutsch" rows="4" cols="50"><?php echo isset($data[1]) ? $data[1] : ''; ?></textarea><br>

                <label for="speicherung_englisch">What data do we store? (Englisch):</label><br>
                <textarea id="speicherung_englisch" name="speicherung_englisch" rows="4" cols="50"><?php echo isset($data[2]) ? $data[2] : ''; ?></textarea><br>

                <input type="submit" value="Aktualisieren">
            </form>
        </div>
    </main>
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

